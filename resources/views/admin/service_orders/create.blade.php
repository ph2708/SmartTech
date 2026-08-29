@extends('layouts.admin')
@section('title', 'Nova Ordem de Serviço')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>🛠️ Nova Ordem de Serviço (Entrada de Aparelho)</h3>
        <a href="{{ route('admin.ordens-servico.index') }}" class="btn btn-outline">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.ordens-servico.store') }}" class="admin-form">
            @csrf

            <!-- Identificação da OS e Datas -->
            <div class="form-section-title">1. Dados da Entrada</div>
            <div class="form-row">
                <div class="form-group flex-1">
                    <label for="os_number">Número da OS *</label>
                    <input type="text" id="os_number" name="os_number" value="{{ old('os_number', $suggestedNumber) }}" required style="font-weight: bold; color: var(--primary);">
                </div>
                <div class="form-group flex-1">
                    <label for="entry_date">Data de Entrada *</label>
                    <input type="date" id="entry_date" name="entry_date" value="{{ old('entry_date', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group flex-1">
                    <label for="estimated_date">Previsão de Conclusão</label>
                    <input type="date" id="estimated_date" name="estimated_date" value="{{ old('estimated_date') }}">
                </div>
                <div class="form-group flex-1">
                    <label for="status">Status Inicial *</label>
                    <select id="status" name="status" required>
                        <option value="orcamento" {{ old('status') === 'orcamento' ? 'selected' : '' }}>Em Orçamento</option>
                        <option value="aprovado" {{ old('status') === 'aprovado' ? 'selected' : '' }}>Aprovado / Em Reparo</option>
                        <option value="aguardando_peca" {{ old('status') === 'aguardando_peca' ? 'selected' : '' }}>Aguardando Peça</option>
                    </select>
                </div>
            </div>

            <!-- Dados do Cliente -->
            <div class="form-section-title">2. Dados do Cliente</div>
            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="customer_name">Nome do Cliente *</label>
                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required placeholder="Ex: Roberto Silva">
                </div>
                <div class="form-group flex-1">
                    <label for="customer_phone">WhatsApp / Telefone *</label>
                    <input type="text" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required placeholder="64 99999-9999">
                </div>
                <div class="form-group flex-2">
                    <label for="customer_email">E-mail (Para avisar quando pronto)</label>
                    <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email') }}" placeholder="cliente@email.com">
                </div>
                <div class="form-group flex-1">
                    <label for="customer_document">CPF (Opcional)</label>
                    <input type="text" id="customer_document" name="customer_document" value="{{ old('customer_document') }}" placeholder="000.000.000-00">
                </div>
            </div>

            <!-- Dados do Aparelho / Equipamento -->
            <div class="form-section-title">3. Equipamento / Aparelho</div>
            <div class="form-row">
                <div class="form-group flex-1">
                    <label for="device_type">Tipo de Equipamento *</label>
                    <select id="device_type" name="device_type" required>
                        <option value="celular" {{ old('device_type') === 'celular' ? 'selected' : '' }}>📱 Celular / Smartphone</option>
                        <option value="computador" {{ old('device_type') === 'computador' ? 'selected' : '' }}>🖥️ Computador (PC / Desktop)</option>
                        <option value="notebook" {{ old('device_type') === 'notebook' ? 'selected' : '' }}>💻 Notebook / Laptop</option>
                        <option value="tablet" {{ old('device_type') === 'tablet' ? 'selected' : '' }}>📟 Tablet / iPad</option>
                        <option value="outro" {{ old('device_type') === 'outro' ? 'selected' : '' }}>🔧 Outro Equipamento</option>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label for="device_brand">Marca *</label>
                    <input type="text" id="device_brand" name="device_brand" value="{{ old('device_brand') }}" required placeholder="Ex: Apple, Samsung, Dell, Asus">
                </div>
                <div class="form-group flex-2">
                    <label for="device_model">Modelo do Aparelho *</label>
                    <input type="text" id="device_model" name="device_model" value="{{ old('device_model') }}" required placeholder="Ex: iPhone 13 Pro Max, Galaxy S23, Inspiron 15">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group flex-1">
                    <label for="device_serial">IMEI / Nº de Série</label>
                    <input type="text" id="device_serial" name="device_serial" value="{{ old('device_serial') }}" placeholder="Número serial ou IMEI">
                </div>
                <div class="form-group flex-1">
                    <label for="device_password">Senha do Aparelho (PIN / Padrão)</label>
                    <input type="text" id="device_password" name="device_password" value="{{ old('device_password') }}" placeholder="Para testes de bancada">
                </div>
                <div class="form-group flex-2">
                    <label for="device_accessories">Acessórios Deixados</label>
                    <input type="text" id="device_accessories" name="device_accessories" value="{{ old('device_accessories') }}" placeholder="Ex: Carregador, capinha, chip">
                </div>
            </div>

            <div class="form-group">
                <label for="device_condition">Estado Físico / Condições do Aparelho</label>
                <textarea id="device_condition" name="device_condition" rows="2" placeholder="Ex: Trincado na tampa traseira, arranhões laterais, câmera intacta...">{{ old('device_condition') }}</textarea>
            </div>

            <!-- Defeito & Diagnóstico -->
            <div class="form-section-title">4. Defeito & Serviços</div>
            <div class="form-group">
                <label for="reported_defect">Defeito Relatado pelo Cliente *</label>
                <textarea id="reported_defect" name="reported_defect" rows="3" required placeholder="Ex: Aparelho não liga após queda / Tela preta / Não carrega no conector...">{{ old('reported_defect') }}</textarea>
            </div>

            <div class="form-group">
                <label for="technical_diagnosis">Diagnóstico Técnico / Laudo</label>
                <textarea id="technical_diagnosis" name="technical_diagnosis" rows="2" placeholder="Laudo do técnico após abertura e teste...">{{ old('technical_diagnosis') }}</textarea>
            </div>

            <div class="form-group">
                <label for="services_description">Serviços a Realizar</label>
                <textarea id="services_description" name="services_description" rows="2" placeholder="Ex: Troca de módulo frontal original + troca de conector de carga...">{{ old('services_description') }}</textarea>
            </div>

            <!-- Orçamento & Valores -->
            <div class="form-section-title">5. Valores & Orçamento</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="parts_cost">Valor das Peças (R$)</label>
                    <input type="number" id="parts_cost" name="parts_cost" value="{{ old('parts_cost', '0.00') }}" step="0.01" min="0" oninput="calcTotal()">
                </div>
                <div class="form-group">
                    <label for="labor_cost">Mão de Obra (R$)</label>
                    <input type="number" id="labor_cost" name="labor_cost" value="{{ old('labor_cost', '0.00') }}" step="0.01" min="0" oninput="calcTotal()">
                </div>
                <div class="form-group">
                    <label for="discount_amount">Desconto (R$)</label>
                    <input type="number" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', '0.00') }}" step="0.01" min="0" oninput="calcTotal()">
                </div>
                <div class="form-group">
                    <label>Valor Total Final</label>
                    <input type="text" id="final_display" value="R$ 0,00" readonly class="input-disabled" style="font-weight: 800; font-size: 1.1rem; color: var(--whatsapp);">
                </div>
            </div>

            <div class="form-group">
                <label for="internal_notes">Observações Internas</label>
                <textarea id="internal_notes" name="internal_notes" rows="2" placeholder="Anotações internas dos técnicos...">{{ old('internal_notes') }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar e Gerar OS 🚀</button>
                <a href="{{ route('admin.ordens-servico.index') }}" class="btn btn-outline">Cancelar</a>
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
calcTotal();
</script>
@endsection
