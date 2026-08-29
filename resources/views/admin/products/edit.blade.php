@extends('layouts.admin')
@section('title', 'Editar Item')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>✏️ Editar Item: {{ $product->name }}</h3>
        <a href="{{ route('admin.produtos.index') }}" class="btn btn-outline">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.produtos.update', $product) }}" enctype="multipart/form-data" class="admin-form">
            @csrf @method('PUT')

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="name">Nome do Item *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                </div>
                <div class="form-group flex-1">
                    <label for="type">Tipo *</label>
                    <select id="type" name="type" required onchange="toggleStockSection(this.value)">
                        <option value="product" {{ old('type', $product->type) === 'product' ? 'selected' : '' }}>📦 Produto Físico (Mercadoria para Venda)</option>
                        <option value="service" {{ old('type', $product->type) === 'service' ? 'selected' : '' }}>🔧 Serviço (Mão de Obra / Assistência Técnica)</option>
                        <option value="asset" {{ old('type', $product->type) === 'asset' ? 'selected' : '' }}>🏛️ Bem Imobilizado / Patrimônio da Loja (Mesa, Cadeira, Ferramentas)</option>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label for="category_id">Categoria *</label>
                    <select id="category_id" name="category_id" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Controle de Estoque -->
            <div id="stockSection" style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid var(--border); margin-bottom: 8px;">
                <div class="form-section-title" style="margin-top: 0; margin-bottom: 12px;">📊 Controle de Quantidade & Estoque</div>
                <div class="form-row">
                    <div class="form-group flex-1">
                        <label for="stock_quantity" id="lblStockQty">Quantidade Disponível</label>
                        <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0">
                    </div>
                    <div class="form-group flex-1" id="grpMinStock">
                        <label for="min_stock_alert">Alerta de Estoque Baixo (Qtd Mínima)</label>
                        <input type="number" id="min_stock_alert" name="min_stock_alert" value="{{ old('min_stock_alert', $product->min_stock_alert) }}" min="0">
                    </div>
                    <div class="form-check" id="grpManageStock" style="align-self: center; margin-top: 14px;">
                        <label>
                            <input type="checkbox" name="manage_stock" value="1" {{ old('manage_stock', $product->manage_stock) ? 'checked' : '' }}>
                            <span>Controlar estoque / inventário</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price" id="lblPrice">Preço de Venda / Valor Patrimonial (R$) *</label>
                    <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" required step="0.01" min="0">
                </div>
                <div class="form-group" id="grpPromoPrice">
                    <label for="promotional_price">Preço Promocional (R$)</label>
                    <input type="number" id="promotional_price" name="promotional_price" value="{{ old('promotional_price', $product->promotional_price) }}" step="0.01" min="0">
                </div>
            </div>

            <div class="form-group">
                <label>Imagem Atual</label>
                @if($product->image_url)
                    <div class="current-image">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="max-width:200px;border-radius:8px;">
                    </div>
                @else
                    <p class="help-text">Nenhuma imagem cadastrada</p>
                @endif
            </div>

            <div class="form-group">
                <label for="image">Nova Imagem Principal</label>
                <div class="image-upload" id="mainImageUpload">
                    <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this, 'mainPreview')">
                    <div class="upload-placeholder" id="mainPreview">
                        <span>📷</span>
                        <p>Clique para trocar a imagem de capa</p>
                        <small>Recomendado: Proporção 1:1 Quadrada (800x800px até 1200x1200px) • JPG, PNG ou WebP</small>
                    </div>
                </div>
            </div>

            @if($product->images->count() > 0)
            <div class="form-group">
                <label>Imagens Adicionais Atuais</label>
                <div class="images-grid-admin">
                    @foreach($product->images as $img)
                    <div class="admin-image-item" id="img-{{ $img->id }}">
                        <img src="{{ $img->image_url }}" alt="Imagem">
                        <button type="button" class="delete-image-btn" onclick="deleteImage({{ $img->id }})">✕</button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="form-group">
                <label for="additional_images">Adicionar Mais Fotos à Galeria</label>
                <input type="file" id="additional_images" name="additional_images[]" accept="image/*" multiple class="file-input">
                <span class="help-text">Você pode selecionar várias fotos ao mesmo tempo (Recomendado: 800x800px).</span>
            </div>

            <div class="form-group" id="grpWhatsappMsg">
                <label for="whatsapp_message">Mensagem WhatsApp Personalizada</label>
                <textarea id="whatsapp_message" name="whatsapp_message" rows="2">{{ old('whatsapp_message', $product->whatsapp_message) }}</textarea>
            </div>

            <div class="form-section-title" style="margin-top: 8px;">🌐 Regras de Publicação & Venda</div>
            <div style="background: #f8fafc; padding: 14px; border-radius: 8px; border: 1px solid var(--border); display: flex; flex-direction: column; gap: 10px;">
                <div class="form-check">
                    <label>
                        <input type="checkbox" name="show_in_catalog" id="chkShowInCatalog" value="1" {{ old('show_in_catalog', $product->show_in_catalog) ? 'checked' : '' }}>
                        <span><strong>🌐 Exibir na Vitrine do Catálogo Online</strong> (Visível para clientes navegarem pelo link público)</span>
                    </label>
                </div>
                <div class="form-check">
                    <label>
                        <input type="checkbox" name="allow_physical_sale" id="chkAllowPhysicalSale" value="1" {{ old('allow_physical_sale', $product->allow_physical_sale) ? 'checked' : '' }}>
                        <span><strong>🛒 Disponível para Venda Física / Balcão</strong> (Aparece no menu Vendas para lançar pedidos e emitir cupom fiscal)</span>
                    </label>
                </div>
                <div class="form-check">
                    <label>
                        <input type="checkbox" name="is_featured" id="chkIsFeatured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                        <span>⭐ Item em destaque no topo do catálogo</span>
                    </label>
                </div>
                <div class="form-check">
                    <label>
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                        <span>Item ativo no sistema</span>
                    </label>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
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

function deleteImage(imageId) {
    if (!confirm('Remover esta imagem?')) return;
    fetch(`/admin/produtos/imagem/${imageId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('img-' + imageId).remove();
        }
    });
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
