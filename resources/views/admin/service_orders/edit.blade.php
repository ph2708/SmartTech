@extends('layouts.admin')
@section('title', 'Editar OS #' . $order->os_number)

@section('content')
<div class="card">
    <div class="card-header">
        <h3>✏️ Editar Ordem de Serviço #{{ $order->os_number }}</h3>
        <a href="{{ route('admin.ordens-servico.show', $order) }}" class="btn btn-outline">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.ordens-servico.update', $order) }}" class="admin-form">
            @csrf @method('PUT')

            <!-- Identificação da OS e Datas -->
            <div class="form-section-title">1. Dados da Entrada & Status</div>
            <div class="form-row">
                <div class="form-group flex-1">
                    <label>Número da OS</label>
                    <input type="text" value="{{ $order->os_number }}" disabled class="input-disabled" style="font-weight: bold;">
                </div>
                <div class="form-group flex-1">
                    <label for="status">Status do Reparo *</label>
                    <select id="status" name="status" required style="font-weight: bold;">
                        <option value="orcamento" {{ old('status', $order->status) === 'orcamento' ? 'selected' : '' }}>⏳ Em Orçamento</option>
                        <option value="aguardando_peca" {{ old('status', $order->status) === 'aguardando_peca' ? 'selected' : '' }}>📦 Aguardando Peça</option>
                        <option value="aprovado" {{ old('status', $order->status) === 'aprovado' ? 'selected' : '' }}>⚙️ Aprovado / Em Reparo</option>
                        <option value="pronto" {{ old('status', $order->status) === 'pronto' ? 'selected' : '' }}>🎉 Pronto p/ Retirada</option>
                        <option value="entregue" {{ old('status', $order->status) === 'entregue' ? 'selected' : '' }}>✅ Entregue / Concluído</option>
                        <option value="cancelado" {{ old('status', $order->status) === 'cancelado' ? 'selected' : '' }}>❌ Cancelado</option>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label for="entry_date">Data de Entrada *</label>
                    <input type="date" id="entry_date" name="entry_date" value="{{ old('entry_date', $order->entry_date->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group flex-1">
                    <label for="estimated_date">Previsão</label>
                    <input type="date" id="estimated_date" name="estimated_date" value="{{ old('estimated_date', $order->estimated_date ? $order->estimated_date->format('Y-m-d') : '') }}">
                </div>
            </div>

            <!-- Dados do Cliente -->
            <div class="form-section-title">2. Dados do Cliente</div>
            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="customer_name">Nome do Cliente *</label>
                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required>
                </div>
                <div class="form-group flex-1">
                    <label for="customer_phone">WhatsApp / Telefone *</label>
                    <input type="text" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}" required>
                </div>
                <div class="form-group flex-2">
                    <label for="customer_email">E-mail</label>
                    <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email', $order->customer_email) }}" placeholder="cliente@email.com">
                </div>
                <div class="form-group flex-1">
                    <label for="customer_document">CPF</label>
                    <input type="text" id="customer_document" name="customer_document" value="{{ old('customer_document', $order->customer_document) }}">
                </div>
            </div>

            <!-- Dados do Aparelho -->
            <div class="form-section-title">3. Equipamento / Aparelho</div>
            <div class="form-row">
                <div class="form-group flex-1">
                    <label for="device_type">Tipo *</label>
                    <select id="device_type" name="device_type" required>
                        <option value="celular" {{ old('device_type', $order->device_type) === 'celular' ? 'selected' : '' }}>📱 Celular</option>
                        <option value="computador" {{ old('device_type', $order->device_type) === 'computador' ? 'selected' : '' }}>🖥️ Computador</option>
                        <option value="notebook" {{ old('device_type', $order->device_type) === 'notebook' ? 'selected' : '' }}>💻 Notebook</option>
                        <option value="tablet" {{ old('device_type', $order->device_type) === 'tablet' ? 'selected' : '' }}>📟 Tablet</option>
                        <option value="outro" {{ old('device_type', $order->device_type) === 'outro' ? 'selected' : '' }}>🔧 Outro</option>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label for="device_brand">Marca *</label>
                    <input type="text" id="device_brand" name="device_brand" value="{{ old('device_brand', $order->device_brand) }}" required>
                </div>
                <div class="form-group flex-2">
                    <label for="device_model">Modelo *</label>
                    <input type="text" id="device_model" name="device_model" value="{{ old('device_model', $order->device_model) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group flex-1">
                    <label for="device_serial">IMEI / Nº de Série</label>
                    <input type="text" id="device_serial" name="device_serial" value="{{ old('device_serial', $order->device_serial) }}">
                </div>
                <div class="form-group flex-1">
                    <label for="device_password">Senha do Aparelho</label>
                    <input type="text" id="device_password" name="device_password" value="{{ old('device_password', $order->device_password) }}">
                </div>
                <div class="form-group flex-2">
                    <label for="device_accessories">Acessórios Deixados</label>
                    <input type="text" id="device_accessories" name="device_accessories" value="{{ old('device_accessories', $order->device_accessories) }}">
                </div>
            </div>

            <div class="form-group">
                <label for="device_condition">Estado Físico / Condições do Aparelho</label>
                <textarea id="device_condition" name="device_condition" rows="2">{{ old('device_condition', $order->device_condition) }}</textarea>
            </div>

            <!-- Defeito & Diagnóstico -->
            <div class="form-section-title">4. Defeito & Serviços</div>
            <div class="form-group">
                <label for="reported_defect">Defeito Relatado *</label>
                <textarea id="reported_defect" name="reported_defect" rows="2" required>{{ old('reported_defect', $order->reported_defect) }}</textarea>
            </div>

            <div class="form-group">
                <label for="technical_diagnosis">Diagnóstico Técnico / Laudo</label>
                <textarea id="technical_diagnosis" name="technical_diagnosis" rows="2">{{ old('technical_diagnosis', $order->technical_diagnosis) }}</textarea>
            </div>

            <div class="form-group">
                <label for="services_description">Serviços Realizados</label>
                <textarea id="services_description" name="services_description" rows="2">{{ old('services_description', $order->services_description) }}</textarea>
            </div>

            <!-- Orçamento & Valores -->
            <div class="form-section-title">5. Valores & Orçamento</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="parts_cost">Valor das Peças (R$)</label>
                    <input type="number" id="parts_cost" name="parts_cost" value="{{ old('parts_cost', $order->parts_cost) }}" step="0.01" min="0" oninput="calcTotal()">
                </div>
                <div class="form-group">
                    <label for="labor_cost">Mão de Obra (R$)</label>
                    <input type="number" id="labor_cost" name="labor_cost" value="{{ old('labor_cost', $order->labor_cost) }}" step="0.01" min="0" oninput="calcTotal()">
                </div>
                <div class="form-group">
                    <label for="discount_amount">Desconto (R$)</label>
                    <input type="number" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', $order->discount_amount) }}" step="0.01" min="0" oninput="calcTotal()">
                </div>
                <div class="form-group">
                    <label>Valor Total Final</label>
                    <input type="text" id="final_display" value="{{ $order->formatted_final_amount }}" readonly class="input-disabled" style="font-weight: 800; font-size: 1.1rem; color: var(--whatsapp);">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="payment_method">Forma de Pagamento</label>
                    <select id="payment_method" name="payment_method">
                        <option value="aguardando" {{ old('payment_method', $order->payment_method) === 'aguardando' ? 'selected' : '' }}>Aguardando Pagamento</option>
                        <option value="pix" {{ old('payment_method', $order->payment_method) === 'pix' ? 'selected' : '' }}>PIX</option>
                        <option value="cartao_credito" {{ old('payment_method', $order->payment_method) === 'cartao_credito' ? 'selected' : '' }}>Cartão de Crédito</option>
                        <option value="cartao_debito" {{ old('payment_method', $order->payment_method) === 'cartao_debito' ? 'selected' : '' }}>Cartão de Débito</option>
                        <option value="dinheiro" {{ old('payment_method', $order->payment_method) === 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="completion_date">Data de Conclusão / Entrega</label>
                    <input type="date" id="completion_date" name="completion_date" value="{{ old('completion_date', $order->completion_date ? $order->completion_date->format('Y-m-d') : '') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="internal_notes">Observações Internas</label>
                <textarea id="internal_notes" name="internal_notes" rows="2">{{ old('internal_notes', $order->internal_notes) }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="{{ route('admin.ordens-servico.show', $order) }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function calcTotal() {
    const parts = parseFloat(document.getElementById('parts_cost').value) || 0;
    const labor = parseFloat(document.getElementById('labor_cost').value) || 0;
    const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const total = Math.max(0, (parts + labor) - discount);
    document.getElementById('final_display').value = 'R$ ' + total.toFixed(2).replace('.', ',');
}
</script>
@endsection
