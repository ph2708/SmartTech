@extends('layouts.admin')
@section('title', 'Editar Venda')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>✏️ Editar Venda #{{ $order->id }}</h3>
        <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.pedidos.update', $order) }}" class="admin-form">
            @csrf @method('PUT')

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="customer_name">Nome do Cliente</label>
                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}">
                </div>
                <div class="form-group flex-1">
                    <label for="customer_phone">WhatsApp / Telefone</label>
                    <input type="text" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="product_id">Produto / Serviço Vinculado</label>
                    <select id="product_id" name="product_id">
                        <option value="">Nenhum produto específico...</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ old('product_id', $order->product_id) == $prod->id ? 'selected' : '' }}>
                                {{ $prod->name }} - R$ {{ number_format($prod->promotional_price ?? $prod->price, 2, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label for="amount">Valor Total (R$) *</label>
                    <input type="number" id="amount" name="amount" value="{{ old('amount', $order->amount) }}" step="0.01" min="0" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="payment_method">Forma de Pagamento *</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="pix" {{ old('payment_method', $order->payment_method) === 'pix' ? 'selected' : '' }}>PIX</option>
                        <option value="cartao_credito" {{ old('payment_method', $order->payment_method) === 'cartao_credito' ? 'selected' : '' }}>Cartão de Crédito</option>
                        <option value="cartao_debito" {{ old('payment_method', $order->payment_method) === 'cartao_debito' ? 'selected' : '' }}>Cartão de Débito</option>
                        <option value="dinheiro" {{ old('payment_method', $order->payment_method) === 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                        <option value="outro" {{ old('payment_method', $order->payment_method) === 'outro' ? 'selected' : '' }}>Outro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status do Pedido *</label>
                    <select id="status" name="status" required>
                        <option value="concluido" {{ old('status', $order->status) === 'concluido' ? 'selected' : '' }}>Concluído / Pago</option>
                        <option value="pendente" {{ old('status', $order->status) === 'pendente' ? 'selected' : '' }}>Pendente / Aguardando</option>
                        <option value="cancelado" {{ old('status', $order->status) === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date">Data *</label>
                    <input type="date" id="date" name="date" value="{{ old('date', $order->date->format('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea id="notes" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
