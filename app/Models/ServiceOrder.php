<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'os_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_document',
        'device_type',
        'device_brand',
        'device_model',
        'device_serial',
        'device_password',
        'device_condition',
        'device_accessories',
        'reported_defect',
        'technical_diagnosis',
        'services_description',
        'parts_cost',
        'labor_cost',
        'total_amount',
        'discount_amount',
        'final_amount',
        'payment_method',
        'entry_date',
        'estimated_date',
        'completion_date',
        'status',
        'internal_notes',
    ];

    protected $casts = [
        'parts_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'entry_date' => 'date',
        'estimated_date' => 'date',
        'completion_date' => 'date',
    ];

    public function getFormattedFinalAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->final_amount, 2, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'orcamento' => 'Em Orçamento',
            'aguardando_peca' => 'Aguardando Peça',
            'aprovado' => 'Aprovado / Em Reparo',
            'pronto' => 'Pronto p/ Retirada',
            'entregue' => 'Entregue / Concluído',
            'cancelado' => 'Cancelado / Sem Reparo',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'orcamento' => 'os-badge-orcamento',
            'aguardando_peca' => 'os-badge-peca',
            'aprovado' => 'os-badge-aprovado',
            'pronto' => 'os-badge-pronto',
            'entregue' => 'os-badge-entregue',
            'cancelado' => 'os-badge-cancelado',
            default => 'status-badge',
        };
    }

    public function getDeviceTypeIconAttribute(): string
    {
        return match($this->device_type) {
            'celular' => '📱',
            'computador' => '🖥️',
            'notebook' => '💻',
            'tablet' => '📟',
            default => '🔧',
        };
    }

    public function getWhatsappNotifyUrlAttribute(): string
    {
        $phone = preg_replace('/\D/', '', $this->customer_phone);
        if (!str_starts_with($phone, '55')) {
            $phone = '55' . $phone;
        }

        $tenant = $this->tenant ?? Tenant::find($this->tenant_id);
        $storeName = $tenant ? $tenant->name : 'Assistência Técnica';

        $statusText = match($this->status) {
            'orcamento' => "Seu orçamento da OS *#{$this->os_number}* para o aparelho *{$this->device_brand} {$this->device_model}* ficou em *{$this->formatted_final_amount}*. Gostaria de aprovar?",
            'aguardando_peca' => "Informamos que a OS *#{$this->os_number}* está aguardando a chegada de peças para finalizar o reparo.",
            'aprovado' => "Sua OS *#{$this->os_number}* foi aprovada e nosso técnico já iniciou o reparo do seu *{$this->device_brand} {$this->device_model}*.",
            'pronto' => "🎉 Boa notícia! O seu aparelho *{$this->device_brand} {$this->device_model}* (OS *#{$this->os_number}*) está *PRONTO* para retirada! Valor: *{$this->formatted_final_amount}*.",
            'entregue' => "Agradecemos pela preferência! Sua OS *#{$this->os_number}* foi concluída. Conte sempre com a *{$storeName}*!",
            default => "Olá {$this->customer_name}, referente à sua OS *#{$this->os_number}* ({$this->device_brand} {$this->device_model}).",
        };

        $msg = "Olá *{$this->customer_name}*, aqui é da *{$storeName}*!\n\n{$statusText}";

        return "https://wa.me/{$phone}?text=" . urlencode($msg);
    }
}
