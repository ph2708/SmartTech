<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('product')->latest('date')->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $orders = $query->paginate(15);
        $totalSales = Order::where('status', 'concluido')->sum('amount');
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'concluido')->count();

        return view('admin.orders.index', compact('orders', 'totalSales', 'totalOrders', 'completedOrders'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->where('allow_physical_sale', true)
            ->where('type', '!=', 'asset')
            ->orderBy('name')
            ->get();
            
        return view('admin.orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'product_id' => 'nullable|exists:products,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:pix,cartao_credito,cartao_debito,dinheiro,outro',
            'status' => 'required|in:pendente,concluido,cancelado',
            'notes' => 'nullable|string|max:1000',
            'date' => 'required|date',
        ]);

        $order = Order::create($validated);

        // Se a venda for concluída, gera a Entrada no Fluxo de Caixa automaticamente
        if ($order->status === 'concluido') {
            \App\Models\FinancialTransaction::create([
                'tenant_id' => session('tenant_id'),
                'type' => 'income',
                'description' => 'Venda: ' . ($order->product ? $order->product->name : 'Venda Balcão / Diversos') . ($order->customer_name ? " ({$order->customer_name})" : ''),
                'amount' => $order->amount,
                'category' => 'Venda de Balcão / WhatsApp',
                'payment_method' => $order->payment_method,
                'status' => 'pago',
                'order_id' => $order->id,
                'date' => $order->date,
            ]);

            // Dá baixa no estoque do produto físico se o estoque for gerenciado
            if ($order->product && $order->product->manage_stock && !$order->product->isService()) {
                $order->product->decrement('stock_quantity', 1);
            }
        }

        return redirect()->route('admin.pedidos.index')
            ->with('success', 'Venda registrada com sucesso e lançada no Fluxo de Caixa!');
    }

    public function edit(Order $pedido)
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.orders.edit', ['order' => $pedido, 'products' => $products]);
    }

    public function update(Request $request, Order $pedido)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'product_id' => 'nullable|exists:products,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:pix,cartao_credito,cartao_debito,dinheiro,outro',
            'status' => 'required|in:pendente,concluido,cancelado',
            'notes' => 'nullable|string|max:1000',
            'date' => 'required|date',
        ]);

        $pedido->update($validated);

        return redirect()->route('admin.pedidos.index')
            ->with('success', 'Venda atualizada com sucesso!');
    }

    public function destroy(Order $pedido)
    {
        $pedido->delete();

        return redirect()->route('admin.pedidos.index')
            ->with('success', 'Venda excluída com sucesso!');
    }
}
