<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Tenant;
use App\Services\FocusNFeService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Lista de notas fiscais emitidas
     */
    public function index()
    {
        $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;
        $invoices = Invoice::where('tenant_id', $tenantId)->latest()->paginate(15);
        $tenant = Tenant::find($tenantId);

        return view('admin.invoices.index', compact('invoices', 'tenant'));
    }

    /**
     * Emite nota fiscal a partir de uma venda
     */
    public function emit(Order $order, Request $request, FocusNFeService $nfeService)
    {
        $type = $request->input('type', 'nfce'); // nfce ou nfe
        $result = $nfeService->emitFromOrder($order, $type);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Emite NFS-e (Nota de Serviço) a partir de uma Ordem de Serviço concluída/entregue
     */
    public function emitServiceOrder(\App\Models\ServiceOrder $os, FocusNFeService $nfeService)
    {
        $result = $nfeService->emitFromServiceOrder($os);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Atualiza / sincroniza status de uma nota com a SEFAZ via Focus NFe
     */
    public function sync(Invoice $invoice, FocusNFeService $nfeService)
    {
        $result = $nfeService->checkStatus($invoice);

        if ($result['success']) {
            return back()->with('success', 'Status da nota fiscal atualizado com sucesso!');
        }

        return back()->with('error', $result['message']);
    }
}
