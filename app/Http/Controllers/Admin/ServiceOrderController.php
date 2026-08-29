<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;

class ServiceOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceOrder::latest('entry_date')->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('os_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('device_brand', 'like', "%{$search}%")
                  ->orWhere('device_model', 'like', "%{$search}%");
            });
        }

        $serviceOrders = $query->paginate(15);

        // Contadores para o mini dash de OS
        $totalOs = ServiceOrder::count();
        $orcamentoCount = ServiceOrder::where('status', 'orcamento')->count();
        $emReparoCount = ServiceOrder::where('status', 'aprovado')->count();
        $aguardandoPecaCount = ServiceOrder::where('status', 'aguardando_peca')->count();
        $prontoCount = ServiceOrder::where('status', 'pronto')->count();
        $entregueCount = ServiceOrder::where('status', 'entregue')->count();
        $faturamentoOs = ServiceOrder::where('status', 'entregue')->sum('final_amount');

        return view('admin.service_orders.index', compact(
            'serviceOrders',
            'totalOs',
            'orcamentoCount',
            'emReparoCount',
            'aguardandoPecaCount',
            'prontoCount',
            'entregueCount',
            'faturamentoOs'
        ));
    }

    public function create()
    {
        // Gera número sequencial de OS sugerido
        $lastId = ServiceOrder::withoutGlobalScope('tenant')->max('id') ?? 0;
        $suggestedNumber = 'OS-' . date('Y') . '-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        return view('admin.service_orders.create', compact('suggestedNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'os_number' => 'required|string|max:50',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_document' => 'nullable|string|max:20',
            'device_type' => 'required|in:celular,computador,notebook,tablet,outro',
            'device_brand' => 'required|string|max:100',
            'device_model' => 'required|string|max:100',
            'device_serial' => 'nullable|string|max:100',
            'device_password' => 'nullable|string|max:100',
            'device_condition' => 'nullable|string|max:1000',
            'device_accessories' => 'nullable|string|max:500',
            'reported_defect' => 'required|string|max:2000',
            'technical_diagnosis' => 'nullable|string|max:2000',
            'services_description' => 'nullable|string|max:2000',
            'parts_cost' => 'nullable|numeric|min:0',
            'labor_cost' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'status' => 'required|in:orcamento,aguardando_peca,aprovado,pronto,entregue,cancelado',
            'entry_date' => 'required|date',
            'estimated_date' => 'nullable|date',
            'completion_date' => 'nullable|date',
            'internal_notes' => 'nullable|string|max:2000',
        ]);

        $parts = (float) ($validated['parts_cost'] ?? 0);
        $labor = (float) ($validated['labor_cost'] ?? 0);
        $discount = (float) ($validated['discount_amount'] ?? 0);

        $validated['total_amount'] = $parts + $labor;
        $validated['final_amount'] = max(0, $validated['total_amount'] - $discount);

        $os = ServiceOrder::create($validated);

        // Se já nascer pronto, dispara notificação
        if ($os->status === 'pronto') {
            app(\App\Services\NotificationService::class)->notifyOrderReady($os);
        }

        return redirect()->route('admin.ordens-servico.show', $os)
            ->with('success', "Ordem de Serviço #{$os->os_number} criada com sucesso!");
    }

    public function show(ServiceOrder $ordens_servico)
    {
        return view('admin.service_orders.show', ['order' => $ordens_servico]);
    }

    public function edit(ServiceOrder $ordens_servico)
    {
        return view('admin.service_orders.edit', ['order' => $ordens_servico]);
    }

    public function update(Request $request, ServiceOrder $ordens_servico)
    {
        $oldStatus = $ordens_servico->status;

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_document' => 'nullable|string|max:20',
            'device_type' => 'required|in:celular,computador,notebook,tablet,outro',
            'device_brand' => 'required|string|max:100',
            'device_model' => 'required|string|max:100',
            'device_serial' => 'nullable|string|max:100',
            'device_password' => 'nullable|string|max:100',
            'device_condition' => 'nullable|string|max:1000',
            'device_accessories' => 'nullable|string|max:500',
            'reported_defect' => 'required|string|max:2000',
            'technical_diagnosis' => 'nullable|string|max:2000',
            'services_description' => 'nullable|string|max:2000',
            'parts_cost' => 'nullable|numeric|min:0',
            'labor_cost' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'status' => 'required|in:orcamento,aguardando_peca,aprovado,pronto,entregue,cancelado',
            'entry_date' => 'required|date',
            'estimated_date' => 'nullable|date',
            'completion_date' => 'nullable|date',
            'internal_notes' => 'nullable|string|max:2000',
        ]);

        $parts = (float) ($validated['parts_cost'] ?? 0);
        $labor = (float) ($validated['labor_cost'] ?? 0);
        $discount = (float) ($validated['discount_amount'] ?? 0);

        $validated['total_amount'] = $parts + $labor;
        $validated['final_amount'] = max(0, $validated['total_amount'] - $discount);

        if ($validated['status'] === 'entregue' && empty($validated['completion_date'])) {
            $validated['completion_date'] = now()->toDateString();
        }

        $ordens_servico->update($validated);

        // Se a OS foi entregue / concluída, lança automaticamente a entrada no financeiro caso ainda não tenha sido lançada
        if ($ordens_servico->status === 'entregue' && $ordens_servico->final_amount > 0) {
            $existingTransaction = \App\Models\FinancialTransaction::where('service_order_id', $ordens_servico->id)->first();
            if (!$existingTransaction) {
                \App\Models\FinancialTransaction::create([
                    'tenant_id' => session('tenant_id'),
                    'type' => 'income',
                    'description' => "OS #{$ordens_servico->os_number}: {$ordens_servico->device_brand} {$ordens_servico->device_model} ({$ordens_servico->customer_name})",
                    'amount' => $ordens_servico->final_amount,
                    'category' => 'Serviço de Assistência Técnica',
                    'payment_method' => $ordens_servico->payment_method === 'aguardando' ? 'pix' : $ordens_servico->payment_method,
                    'status' => 'pago',
                    'service_order_id' => $ordens_servico->id,
                    'date' => $ordens_servico->completion_date ?? now()->toDateString(),
                ]);
            }
        }

        // Se o status mudou para 'pronto', dispara e-mail e registra SMS para o cliente
        if ($oldStatus !== 'pronto' && $ordens_servico->status === 'pronto') {
            app(\App\Services\NotificationService::class)->notifyOrderReady($ordens_servico);
        }

        return redirect()->route('admin.ordens-servico.show', $ordens_servico)
            ->with('success', "Ordem de Serviço #{$ordens_servico->os_number} atualizada com sucesso!");
    }

    public function destroy(ServiceOrder $ordens_servico)
    {
        $ordens_servico->delete();

        return redirect()->route('admin.ordens-servico.index')
            ->with('success', 'Ordem de serviço excluída com sucesso!');
    }

    public function print(ServiceOrder $order)
    {
        $tenant = session('tenant');
        return view('admin.service_orders.print', compact('order', 'tenant'));
    }
}
