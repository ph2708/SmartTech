@extends('layouts.admin')
@section('title', 'Registrar Venda')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>💵 Registrar Venda</h3>
        <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.pedidos.store') }}" class="admin-form">
            @csrf

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="customer_name">Nome do Cliente</label>
                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" placeholder="Ex: Maria Oliveira">
                </div>
                <div class="form-group flex-1">
                    <label for="customer_phone">WhatsApp / Telefone</label>
                    <input type="text" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="64 99999-9999">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="product_id">Produto / Serviço Vinculado</label>
                    <select id="product_id" name="product_id" onchange="autoFillPrice(this)">
                        <option value="">Selecione um produto (opcional)...</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" data-price="{{ $prod->promotional_price ?? $prod->price }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                                {{ $prod->name }} - R$ {{ number_format($prod->promotional_price ?? $prod->price, 2, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label for="amount">Valor Total (R$) *</label>
                    <input type="number" id="amount" name="amount" value="{{ old('amount') }}" step="0.01" min="0" required placeholder="0.00">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="payment_method">Forma de Pagamento *</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="pix" {{ old('payment_method') === 'pix' ? 'selected' : '' }}>PIX</option>
                        <option value="cartao_credito" {{ old('payment_method') === 'cartao_credito' ? 'selected' : '' }}>Cartão de Crédito</option>
                        <option value="cartao_debito" {{ old('payment_method') === 'cartao_debito' ? 'selected' : '' }}>Cartão de Débito</option>
                        <option value="dinheiro" {{ old('payment_method') === 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                        <option value="outro" {{ old('payment_method') === 'outro' ? 'selected' : '' }}>Outro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status do Pedido *</label>
                    <select id="status" name="status" required>
                        <option value="concluido" {{ old('status', 'concluido') === 'concluido' ? 'selected' : '' }}>Concluído / Pago</option>
                        <option value="pendente" {{ old('status') === 'pendente' ? 'selected' : '' }}>Pendente / Aguardando</option>
                        <option value="cancelado" {{ old('status') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date">Data *</label>
                    <input type="date" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Detalhes do reparo, garantia, modelo do celular/PC...">{{ old('notes') }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar Venda</button>
                <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function autoFillPrice(select) {
    const selected = select.options[select.selectedIndex];
    const price = selected.getAttribute('data-price');
    const amountInput = document.getElementById('amount');
    if (price && !amountInput.value) {
        amountInput.value = parseFloat(price).toFixed(2);
    }
}
</script>
@endsection
