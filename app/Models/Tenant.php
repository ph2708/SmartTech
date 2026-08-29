<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'is_branch',
        'branch_name',
        'name',
        'slug',
        'whatsapp',
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        'description',
        'address',
        'city',
        'state',
        'google_maps_embed',
        'google_maps_link',
        'instagram',
        'show_instagram',
        'facebook',
        'is_active',
        'plan',
        // SMTP / E-mail
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'mail_is_verified',
        'mail_verified_at',
        // SMS
        'sms_enabled',
        'sms_provider',
        'sms_api_key',
        'sms_api_secret',
        'sms_from_number',
        // Focus NFe (Fiscal)
        'nfe_enabled',
        'nfe_environment',
        'nfe_token',
        'cnpj',
        'inscricao_estadual',
        'inscricao_municipal',
        'regime_tributario',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_branch' => 'boolean',
        'show_instagram' => 'boolean',
        'mail_is_verified' => 'boolean',
        'mail_verified_at' => 'datetime',
        'sms_enabled' => 'boolean',
        'nfe_enabled' => 'boolean',
        'mail_port' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(Tenant::class, 'parent_id');
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Tenant::class, 'parent_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getWhatsappLinkAttribute(): string
    {
        $number = preg_replace('/\D/', '', $this->whatsapp);
        if (!str_starts_with($number, '55')) {
            $number = '55' . $number;
        }
        return "https://wa.me/{$number}";
    }

    public function getInstagramUrlAttribute(): ?string
    {
        if (!$this->instagram) {
            return null;
        }

        $user = trim($this->instagram);
        if (str_starts_with($user, 'http://') || str_starts_with($user, 'https://')) {
            return $user;
        }

        $user = ltrim($user, '@');
        return "https://instagram.com/{$user}";
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }
}
