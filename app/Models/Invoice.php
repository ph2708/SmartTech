<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'order_id',
        'service_order_id',
        'reference_code',
        'type',
        'status',
        'total_amount',
        'customer_name',
        'customer_cpf_cnpj',
        'access_key',
        'nfe_number',
        'nfe_series',
        'pdf_url',
        'xml_url',
        'error_message',
        'payload_sent',
        'response_received',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'payload_sent' => 'array',
        'response_received' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function getFormattedStatusAttribute(): string
    {
        return match ($this->status) {
            'autorizado' => 'Emitida (Autorizada)',
            'processando' => 'Em Processamento',
            'erro_autorizacao' => 'Rejeitada / Erro',
            'cancelado' => 'Cancelada',
            default => ucfirst($this->status),
        };
    }
}
