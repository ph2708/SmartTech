<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancialTransaction::latest('date')->latest('id');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $monthParts = explode('-', $request->month);
            if (count($monthParts) === 2) {
                $query->whereYear('date', $monthParts[0])->whereMonth('date', $monthParts[1]);
            }
        }

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $transactions = $query->paginate(20);

        // Totais Gerais (apenas pagos/concluídos)
        $totalIncome = FinancialTransaction::where('type', 'income')->where('status', 'pago')->sum('amount');
        $totalExpense = FinancialTransaction::where('type', 'expense')->where('status', 'pago')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        // Totais do Mês Atual
        $currentYear = now()->year;
        $currentMonth = now()->month;
        $monthIncome = FinancialTransaction::where('type', 'income')->where('status', 'pago')->whereYear('date', $currentYear)->whereMonth('date', $currentMonth)->sum('amount');
        $monthExpense = FinancialTransaction::where('type', 'expense')->where('status', 'pago')->whereYear('date', $currentYear)->whereMonth('date', $currentMonth)->sum('amount');
        $monthBalance = $monthIncome - $monthExpense;

        // Despesas Pendentes a Pagar
        $pendingExpenses = FinancialTransaction::where('type', 'expense')->where('status', 'pendente')->sum('amount');

        // Categorias mais frequentes
        $expenseCategories = [
            'Compra de Peças / Estoque',
            'Aluguel',
            'Energia / Água / Internet',
            'Salários / Comissões',
            'Ferramentas / Equipamentos',
            'Marketing / Anúncios',
            'Impostos / Taxas',
            'Outras Despesas'
        ];

        $incomeCategories = [
            'Venda de Balcão / WhatsApp',
            'Serviço de Assistência Técnica',
            'Venda de Capinhas & Películas',
            'Venda de Acessórios',
            'Venda de Perfumes',
            'Outras Receitas'
        ];

        return view('admin.financial.index', compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'netBalance',
            'monthIncome',
            'monthExpense',
            'monthBalance',
            'pendingExpenses',
            'expenseCategories',
            'incomeCategories'
        ));
    }

    public function create(Request $request)
    {
        $type = $request->query('type', 'expense');
        return view('admin.financial.create', compact('type'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|string|max:100',
            'payment_method' => 'required|in:pix,cartao_credito,cartao_debito,dinheiro,boleto,transferencia,outro',
            'status' => 'required|in:pago,pendente,cancelado',
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        FinancialTransaction::create($validated);

        $msg = $validated['type'] === 'income' ? 'Entrada registrada com sucesso!' : 'Despesa registrada com sucesso!';

        return redirect()->route('admin.financeiro.index')->with('success', $msg);
    }

    public function edit(FinancialTransaction $financeiro)
    {
        return view('admin.financial.edit', ['transaction' => $financeiro]);
    }

    public function update(Request $request, FinancialTransaction $financeiro)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|string|max:100',
            'payment_method' => 'required|in:pix,cartao_credito,cartao_debito,dinheiro,boleto,transferencia,outro',
            'status' => 'required|in:pago,pendente,cancelado',
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $financeiro->update($validated);

        return redirect()->route('admin.financeiro.index')->with('success', 'Lançamento atualizado com sucesso!');
    }

    public function destroy(FinancialTransaction $financeiro)
    {
        $financeiro->delete();

        return redirect()->route('admin.financeiro.index')->with('success', 'Lançamento excluído com sucesso!');
    }
}
