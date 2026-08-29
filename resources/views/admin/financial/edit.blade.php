@extends('layouts.admin')
@section('title', 'Editar Lançamento Financeiro')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>✏️ Editar Lançamento #{{ $transaction->id }}</h3>
        <a href="{{ route('admin.financeiro.index') }}" class="btn btn-outline">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.financeiro.update', $transaction) }}" class="admin-form">
            @csrf @method('PUT')

            <div class="form-row">
                <div class="form-group flex-1">
                    <label for="type">Tipo *</label>
                    <select id="type" name="type" required style="font-weight: bold;">
                        <option value="income" {{ old('type', $transaction->type) === 'income' ? 'selected' : '' }}>📈 Entrada / Receita</option>
                        <option value="expense" {{ old('type', $transaction->type) === 'expense' ? 'selected' : '' }}>📉 Despesa / Saída</option>
                    </select>
                </div>
                <div class="form-group flex-2">
                    <label for="description">Descrição *</label>
                    <input type="text" id="description" name="description" value="{{ old('description', $transaction->description) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group flex-1">
                    <label for="amount">Valor (R$) *</label>
                    <input type="number" id="amount" name="amount" value="{{ old('amount', $transaction->amount) }}" step="0.01" min="0.01" required>
                </div>
                <div class="form-group flex-1">
                    <label for="category">Categoria *</label>
                    <input type="text" id="category" name="category" value="{{ old('category', $transaction->category) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group flex-1">
                    <label for="payment_method">Forma de Pagamento *</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="pix" {{ old('payment_method', $transaction->payment_method) === 'pix' ? 'selected' : '' }}>PIX</option>
                        <option value="cartao_credito" {{ old('payment_method', $transaction->payment_method) === 'cartao_credito' ? 'selected' : '' }}>Cartão de Crédito</option>
                        <option value="cartao_debito" {{ old('payment_method', $transaction->payment_method) === 'cartao_debito' ? 'selected' : '' }}>Cartão de Débito</option>
                        <option value="dinheiro" {{ old('payment_method', $transaction->payment_method) === 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                        <option value="boleto" {{ old('payment_method', $transaction->payment_method) === 'boleto' ? 'selected' : '' }}>Boleto Bancário</option>
                        <option value="transferencia" {{ old('payment_method', $transaction->payment_method) === 'transferencia' ? 'selected' : '' }}>Transferência</option>
                        <option value="outro" {{ old('payment_method', $transaction->payment_method) === 'outro' ? 'selected' : '' }}>Outro</option>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label for="status">Status *</label>
                    <select id="status" name="status" required>
                        <option value="pago" {{ old('status', $transaction->status) === 'pago' ? 'selected' : '' }}>Pago / Concluído</option>
                        <option value="pendente" {{ old('status', $transaction->status) === 'pendente' ? 'selected' : '' }}>Pendente / A Pagar</option>
                        <option value="cancelado" {{ old('status', $transaction->status) === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label for="date">Data *</label>
                    <input type="date" id="date" name="date" value="{{ old('date', $transaction->date->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group flex-1">
                    <label for="due_date">Vencimento</label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date', $transaction->due_date ? $transaction->due_date->format('Y-m-d') : '') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea id="notes" name="notes" rows="3">{{ old('notes', $transaction->notes) }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="{{ route('admin.financeiro.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
