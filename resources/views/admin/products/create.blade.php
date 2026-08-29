@extends('layouts.admin')
@section('title', 'Novo Item')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>📦 Novo Item (Produto Físico ou Serviço)</h3>
        <a href="{{ route('admin.produtos.index') }}" class="btn btn-outline">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.produtos.store') }}" enctype="multipart/form-data" class="admin-form">
            @csrf

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="name">Nome do Item *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Ex: Capinha Anti-impacto iPhone 15 ou Troca de Tela">
                </div>
                <div class="form-group flex-1">
                    <label for="type">Tipo de Item *</label>
                    <select id="type" name="type" required onchange="toggleStockSection(this.value)">
                        <option value="product" {{ old('type', 'product') === 'product' ? 'selected' : '' }}>📦 Produto Físico (Mercadoria para Venda)</option>
                        <option value="service" {{ old('type') === 'service' ? 'selected' : '' }}>🔧 Serviço (Mão de Obra / Assistência Técnica)</option>
                        <option value="asset" {{ old('type') === 'asset' ? 'selected' : '' }}>🏛️ Bem Imobilizado / Patrimônio da Loja (Mesa, Cadeira, Ferramentas)</option>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label for="category_id">Categoria *</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Selecione...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Controle de Estoque (Produtos físicos e Imobilizados) -->
            <div id="stockSection" style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid var(--border); margin-bottom: 8px;">
                <div class="form-section-title" style="margin-top: 0; margin-bottom: 12px;">📊 Controle de Quantidade & Estoque</div>
                <div class="form-row">
                    <div class="form-group flex-1">
                        <label for="stock_quantity" id="lblStockQty">Quantidade Disponível</label>
                        <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', '10') }}" min="0" placeholder="0">
                    </div>
                    <div class="form-group flex-1" id="grpMinStock">
                        <label for="min_stock_alert">Alerta de Estoque Baixo (Qtd Mínima)</label>
                        <input type="number" id="min_stock_alert" name="min_stock_alert" value="{{ old('min_stock_alert', '2') }}" min="0" placeholder="2">
                        <span class="help-text">Avisa no painel quando atingir esse valor</span>
                    </div>
                    <div class="form-check" id="grpManageStock" style="align-self: center; margin-top: 14px;">
                        <label>
                            <input type="checkbox" name="manage_stock" value="1" {{ old('manage_stock', true) ? 'checked' : '' }}>
                            <span>Controlar estoque / inventário</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea id="description" name="description" rows="3" placeholder="Descreva o produto, serviço ou detalhes do patrimônio...">{{ old('description') }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price" id="lblPrice">Preço de Venda / Valor Patrimonial (R$) *</label>
                    <input type="number" id="price" name="price" value="{{ old('price') }}" required step="0.01" min="0" placeholder="99.90">
                </div>
                <div class="form-group" id="grpPromoPrice">
                    <label for="promotional_price">Preço Promocional (R$)</label>
                    <input type="number" id="promotional_price" name="promotional_price" value="{{ old('promotional_price') }}" step="0.01" min="0" placeholder="79.90">
                    <span class="help-text">Deixe vazio se não estiver em promoção</span>
                </div>
            </div>

            <div class="form-group">
                <label for="image">Imagem Principal</label>
                <div class="image-upload" id="mainImageUpload">
                    <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this, 'mainPreview')">
                    <div class="upload-placeholder" id="mainPreview">
                        <span>📷</span>
                        <p>Clique ou arraste para enviar</p>
                        <small>JPG, PNG ou WebP. Máx. 2MB</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="additional_images">Imagens Adicionais</label>
                <input type="file" id="additional_images" name="additional_images[]" accept="image/*" multiple class="file-input">
            </div>

            <div class="form-group" id="grpWhatsappMsg">
                <label for="whatsapp_message">Mensagem WhatsApp Personalizada</label>
                <textarea id="whatsapp_message" name="whatsapp_message" rows="2" placeholder="Use {produto} e {preco} como variáveis. Ex: Olá! Quero o {produto} por {preco}">{{ old('whatsapp_message') }}</textarea>
            </div>

            <div class="form-section-title" style="margin-top: 8px;">🌐 Regras de Publicação & Venda</div>
            <div style="background: #f8fafc; padding: 14px; border-radius: 8px; border: 1px solid var(--border); display: flex; flex-direction: column; gap: 10px;">
                <div class="form-check">
                    <label>
                        <input type="checkbox" name="show_in_catalog" id="chkShowInCatalog" value="1" {{ old('show_in_catalog', true) ? 'checked' : '' }}>
                        <span><strong>🌐 Exibir na Vitrine do Catálogo Online</strong> (Visível para clientes navegarem pelo link público)</span>
                    </label>
                </div>
                <div class="form-check">
                    <label>
                        <input type="checkbox" name="allow_physical_sale" id="chkAllowPhysicalSale" value="1" {{ old('allow_physical_sale', true) ? 'checked' : '' }}>
                        <span><strong>🛒 Disponível para Venda Física / Balcão</strong> (Aparece no menu Vendas para lançar pedidos e emitir cupom fiscal)</span>
                    </label>
                </div>
                <div class="form-check">
                    <label>
                        <input type="checkbox" name="is_featured" id="chkIsFeatured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        <span>⭐ Item em destaque no topo do catálogo</span>
                    </label>
                </div>
                <div class="form-check">
                    <label>
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span>Item ativo no sistema</span>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar Item</button>
                <a href="{{ route('admin.produtos.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width:100%;max-height:200px;border-radius:8px;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function toggleStockSection(type) {
    const stockSec = document.getElementById('stockSection');
    const chkCatalog = document.getElementById('chkShowInCatalog');
    const chkPhysical = document.getElementById('chkAllowPhysicalSale');
    const chkFeatured = document.getElementById('chkIsFeatured');
    const grpPromoPrice = document.getElementById('grpPromoPrice');
    const grpWhatsappMsg = document.getElementById('grpWhatsappMsg');
    const lblPrice = document.getElementById('lblPrice');
    const lblStockQty = document.getElementById('lblStockQty');

    if (type === 'service') {
        stockSec.style.display = 'none';
        grpPromoPrice.style.display = 'block';
        grpWhatsappMsg.style.display = 'block';
        chkCatalog.disabled = false;
        chkPhysical.disabled = false;
        chkFeatured.disabled = false;
        lblPrice.textContent = 'Preço do Serviço (Mão de Obra R$) *';
    } else if (type === 'asset') {
        stockSec.style.display = 'block';
        grpPromoPrice.style.display = 'none';
        grpWhatsappMsg.style.display = 'none';
        chkCatalog.checked = false;
        chkCatalog.disabled = true;
        chkPhysical.checked = false;
        chkPhysical.disabled = true;
        chkFeatured.checked = false;
        chkFeatured.disabled = true;
        lblPrice.textContent = 'Valor do Bem Patrimonial (R$) *';
        lblStockQty.textContent = 'Quantidade de Bens / Unidades na Loja';
    } else {
        stockSec.style.display = 'block';
        grpPromoPrice.style.display = 'block';
        grpWhatsappMsg.style.display = 'block';
        chkCatalog.disabled = false;
        chkPhysical.disabled = false;
        chkFeatured.disabled = false;
        lblPrice.textContent = 'Preço de Venda (R$) *';
        lblStockQty.textContent = 'Quantidade em Estoque';
    }
}
toggleStockSection(document.getElementById('type').value);
</script>
@endsection
