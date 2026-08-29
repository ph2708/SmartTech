@extends('layouts.admin')
@section('title', 'Novo Lançamento Financeiro')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>{{ $type === 'income' ? '📈 Nova Entrada (Receita)' : '📉 Nova Despesa (Saída)' }}</h3>
        <a href="{{ route('admin.financeiro.index') }}" class="btn btn-outline">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.financeiro.store') }}" class="admin-form">
            @csrf

            <div class="form-row">
                <div class="form-group flex-1">
                    <label for="type">Tipo de Movimentação *</label>
                    <select id="type" name="type" required style="font-weight: bold;">
                        <option value="income" {{ old('type', $type) === 'income' ? 'selected' : '' }}>📈 Entrada / Receita</option>
                        <option value="expense" {{ old('type', $type) === 'expense' ? 'selected' : '' }}>📉 Despesa / Saída</option>
                    </select>
                </div>
                <div class="form-group flex-2">
                    <label for="description">Descrição do Lançamento *</label>
                    <input type="text" id="description" name="description" value="{{ old('description') }}" required placeholder="Ex: Pagamento Fornecedor de Telas / Conta de Luz / Aluguel">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group flex-1">
                    <label for="amount">Valor (R$) *</label>
                    <input type="number" id="amount" name="amount" value="{{ old('amount') }}" step="0.01" min="0.01" required placeholder="0.00" style="font-size: 1.1rem; font-weight: bold;">
                </div>
                <div class="form-group flex-1">
                    <label for="category">Categoria *</label>
                    <select id="category" name="category" required>
                        <optgroup label="Despesas Comuns">
                            <option value="Compra de Peças / Estoque" {{ old('category') === 'Compra de Peças / Estoque' ? 'selected' : '' }}>Peças / Estoque</option>
                            <option value="Aluguel" {{ old('category') === 'Aluguel' ? 'selected' : '' }}>Aluguel</option>
                            <option value="Energia / Água / Internet" {{ old('category') === 'Energia / Água / Internet' ? 'selected' : '' }}>Energia / Água / Internet</option>
                            <option value="Salários / Comissões" {{ old('category') === 'Salários / Comissões' ? 'selected' : '' }}>Salários / Comissões</option>
                            <option value="Ferramentas / Equipamentos" {{ old('category') === 'Ferramentas / Equipamentos' ? 'selected' : '' }}>Ferramentas / Equipamentos</option>
                            <option value="Marketing / Anúncios" {{ old('category') === 'Marketing / Anúncios' ? 'selected' : '' }}>Marketing / Anúncios</option>
                            <option value="Impostos / Taxas" {{ old('category') === 'Impostos / Taxas' ? 'selected' : '' }}>Impostos / Taxas</option>
                        </optgroup>
                        <optgroup label="Receitas / Vendas">
                            <option value="Venda de Balcão / WhatsApp" {{ old('category') === 'Venda de Balcão / WhatsApp' ? 'selected' : '' }}>Venda Balcão / WhatsApp</option>
                            <option value="Serviço de Assistência Técnica" {{ old('category') === 'Serviço de Assistência Técnica' ? 'selected' : '' }}>Serviço de Assistência</option>
                            <option value="Acessórios & Capinhas" {{ old('category') === 'Acessórios & Capinhas' ? 'selected' : '' }}>Acessórios & Capinhas</option>
                            <option value="Perfumes" {{ old('category') === 'Perfumes' ? 'selected' : '' }}>Perfumes</option>
                            <option value="Outras Receitas" {{ old('category') === 'Outras Receitas' ? 'selected' : '' }}>Outras Receitas</option>
                        </optgroup>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group flex-1">
                    <label for="payment_method">Forma de Pagamento *</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="pix" {{ old('payment_method') === 'pix' ? 'selected' : '' }}>PIX</option>
                        <option value="cartao_credito" {{ old('payment_method') === 'cartao_credito' ? 'selected' : '' }}>Cartão de Crédito</option>
                        <option value="cartao_debito" {{ old('payment_method') === 'cartao_debito' ? 'selected' : '' }}>Cartão de Débito</option>
                        <option value="dinheiro" {{ old('payment_method') === 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                        <option value="boleto" {{ old('payment_method') === 'boleto' ? 'selected' : '' }}>Boleto Bancário</option>
                        <option value="transferencia" {{ old('payment_method') === 'transferencia' ? 'selected' : '' }}>Transferência / TED</option>
                        <option value="outro" {{ old('payment_method') === 'outro' ? 'selected' : '' }}>Outro</option>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label for="status">Status *</label>
                    <select id="status" name="status" required>
                        <option value="pago" {{ old('status', 'pago') === 'pago' ? 'selected' : '' }}>Pago / Realizado</option>
                        <option value="pendente" {{ old('status') === 'pendente' ? 'selected' : '' }}>Pendente / A Pagar</option>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label for="date">Data de Lançamento *</label>
                    <input type="date" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group flex-1">
                    <label for="due_date">Data de Vencimento</label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Observações / Detalhes</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Informações do fornecedor, número de nota fiscal, parcelamento...">{{ old('notes') }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar Lançamento</button>
                <a href="{{ route('admin.financeiro.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
