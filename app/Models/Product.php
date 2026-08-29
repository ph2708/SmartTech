<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'type',
        'name',
        'slug',
        'description',
        'price',
        'promotional_price',
        'stock_quantity',
        'manage_stock',
        'min_stock_alert',
        'image',
        'is_active',
        'show_in_catalog',
        'allow_physical_sale',
        'is_featured',
        'whatsapp_message',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'promotional_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'manage_stock' => 'boolean',
        'min_stock_alert' => 'integer',
        'is_active' => 'boolean',
        'show_in_catalog' => 'boolean',
        'allow_physical_sale' => 'boolean',
        'is_featured' => 'boolean',
        'order' => 'integer',
    ];

    public function isService(): bool
    {
        return $this->type === 'service';
    }

    public function isAsset(): bool
    {
        return $this->type === 'asset';
    }

    public function isProduct(): bool
    {
        return $this->type === 'product';
    }

    public function isLowStock(): bool
    {
        return $this->manage_stock && !$this->isService() && $this->stock_quantity <= $this->min_stock_alert;
    }

    public function isOutOfStock(): bool
    {
        return $this->manage_stock && !$this->isService() && $this->stock_quantity <= 0;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'R$ ' . number_format($this->price, 2, ',', '.');
    }

    public function getFormattedPromotionalPriceAttribute(): ?string
    {
        if ($this->promotional_price) {
            return 'R$ ' . number_format($this->promotional_price, 2, ',', '.');
        }
        return null;
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if ($this->promotional_price && $this->price > 0) {
            return (int) round((($this->price - $this->promotional_price) / $this->price) * 100);
        }
        return null;
    }

    public function getWhatsappUrlAttribute(): string
    {
        $tenant = $this->tenant ?? Tenant::find($this->tenant_id);
        $number = preg_replace('/\D/', '', $tenant->whatsapp);
        if (!str_starts_with($number, '55')) {
            $number = '55' . $number;
        }

        $price = $this->promotional_price ?? $this->price;
        $formattedPrice = 'R$ ' . number_format($price, 2, ',', '.');

        if ($this->whatsapp_message) {
            $message = str_replace(
                ['{produto}', '{preco}'],
                [$this->name, $formattedPrice],
                $this->whatsapp_message
            );
        } else {
            $message = "Olá! 😊 Tenho interesse no produto: *{$this->name}* - {$formattedPrice}. Poderia me dar mais informações?";
        }

        return "https://wa.me/{$number}?text=" . urlencode($message);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }
}
