<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\ServiceOrder;
use App\Models\Tenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FocusNFeService
{
    /**
     * Retorna a URL base conforme o ambiente (Homologação / Produção)
     */
    protected function getBaseUrl(Tenant $tenant): string
    {
        return $tenant->nfe_environment === 'producao'
            ? 'https://api.focusnfe.com.br'
            : 'https://homologacao.focusnfe.com.br';
    }

    /**
     * Emite NFC-e / NF-e para uma venda de produtos
     */
    public function emitFromOrder(Order $order, string $type = 'nfce'): array
    {
        $tenant = $order->tenant ?? Tenant::find($order->tenant_id);

        if (!$tenant || !$tenant->nfe_enabled || empty($tenant->nfe_token)) {
            return [
                'success' => false,
                'message' => 'Emissão fiscal Focus NFe desativada ou sem token cadastrado nas configurações da loja.',
            ];
        }

        $ref = 'PED-' . $order->id . '-' . Str::random(6);

        // Montagem do payload conforme especificações oficiais Focus NFe (API v2)
        $payload = [
            'natureza_operacao' => 'VENDA DE MERCADORIA',
            'data_emissao' => now()->toIso8601String(),
            'tipo_documento' => 1, // 1 = Saída
            'finalidade_emissao' => 1, // 1 = Normal
            'consumidor_final' => 1,
            'presenca_comprador' => 1, // 1 = Operação presencial
            'cnpj_emitente' => preg_replace('/\D/', '', $tenant->cnpj),
            'forma_pagamento' => match ($order->payment_method) {
                'pix' => '17', // PIX
                'cartao_credito' => '03', // Cartão de Crédito
                'cartao_debito' => '04', // Cartão de Débito
                'dinheiro' => '01', // Dinheiro
                default => '99', // Outros
            },
            'valor_pagamento' => (float) $order->amount,
            'items' => [
                [
                    'numero_item' => 1,
                    'codigo_produto' => $order->product_id ? (string) $order->product_id : 'DIVERSOS',
                    'descricao' => $order->product ? $order->product->name : 'Venda de Balcão / Acessórios',
                    'codigo_ncm' => '85177099', // Código NCM padrão para acessórios/peças de celulares
                    'cfop' => '5102', // Venda de mercadoria adquirida de terceiros
                    'unidade_comercial' => 'UN',
                    'quantidade_comercial' => 1,
                    'valor_unitario_comercial' => (float) $order->amount,
                    'valor_bruto' => (float) $order->amount,
                    'unidade_tributavel' => 'UN',
                    'quantidade_tributavel' => 1,
                    'valor_unitario_tributavel' => (float) $order->amount,
                    'origem' => 0,
                    'icms_situacao_tributaria' => '102', // Simples Nacional sem crédito
                ]
            ]
        ];

        // Se o cliente informou CPF ou nome
        if (!empty($order->customer_name)) {
            $payload['nome_destinatario'] = $order->customer_name;
        }

        try {
            $url = $this->getBaseUrl($tenant) . "/v2/{$type}?ref={$ref}";
            
            $response = Http::withBasicAuth($tenant->nfe_token, '')
                ->timeout(15)
                ->post($url, $payload);

            $data = $response->json();

            $status = 'processando';
            $pdfUrl = null;
            $xmlUrl = null;
            $accessKey = null;
            $nfeNumber = null;
            $errorMsg = null;

            if ($response->successful()) {
                $status = ($data['status'] ?? '') === 'autorizado' ? 'autorizado' : 'processando';
                $pdfUrl = $data['caminho_danfe'] ?? null;
                $xmlUrl = $data['caminho_xml_nota_fiscal'] ?? null;
                $accessKey = $data['chave_nfe'] ?? null;
                $nfeNumber = $data['numero'] ?? null;
            } else {
                $status = 'erro_autorizacao';
                $errorMsg = $data['mensagem'] ?? ($data['erros'][0]['mensagem'] ?? 'Erro retornado pela SEFAZ / Focus NFe');
            }

            $invoice = Invoice::create([
                'tenant_id' => $tenant->id,
                'order_id' => $order->id,
                'reference_code' => $ref,
                'type' => $type,
                'status' => $status,
                'total_amount' => $order->amount,
                'customer_name' => $order->customer_name,
                'access_key' => $accessKey,
                'nfe_number' => $nfeNumber,
                'pdf_url' => $pdfUrl ? ($this->getBaseUrl($tenant) . $pdfUrl) : null,
                'xml_url' => $xmlUrl ? ($this->getBaseUrl($tenant) . $xmlUrl) : null,
                'error_message' => $errorMsg,
                'payload_sent' => $payload,
                'response_received' => $data,
            ]);

            return [
                'success' => $status !== 'erro_autorizacao',
                'invoice' => $invoice,
                'message' => $status === 'autorizado' ? 'Nota Fiscal emitida com sucesso!' : 'Nota enviada para processamento na SEFAZ.',
            ];
        } catch (\Exception $e) {
            Log::error("Erro Focus NFe: " . $e->getMessage());

            $invoice = Invoice::create([
                'tenant_id' => $tenant->id,
                'order_id' => $order->id,
                'reference_code' => $ref,
                'type' => $type,
                'status' => 'erro_autorizacao',
                'total_amount' => $order->amount,
                'customer_name' => $order->customer_name,
                'error_message' => $e->getMessage(),
                'payload_sent' => $payload,
            ]);

            return [
                'success' => false,
                'message' => 'Falha na conexão com a Focus NFe: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Emite NFS-e (Nota Fiscal de Serviços Eletrônica) a partir de uma Ordem de Serviço (Assistência Técnica)
     */
    public function emitFromServiceOrder(ServiceOrder $os): array
    {
        $tenant = $os->tenant ?? Tenant::find($os->tenant_id);

        if (!$tenant || !$tenant->nfe_enabled || empty($tenant->nfe_token)) {
            return [
                'success' => false,
                'message' => 'Emissão fiscal Focus NFe desativada ou sem token cadastrado nas configurações da loja.',
            ];
        }

        $ref = 'OS-' . $os->id . '-' . Str::random(6);
        $totalServico = (float) ($os->final_amount > 0 ? $os->final_amount : ($os->service_cost + $os->parts_cost));

        // Payload oficial de NFS-e para a Focus NFe (API v2)
        $payload = [
            'data_emissao' => now()->toIso8601String(),
            'prestador' => [
                'cnpj' => preg_replace('/\D/', '', $tenant->cnpj),
                'inscricao_municipal' => $tenant->inscricao_municipal ?? 'ISENTO',
                'codigo_municipio' => '5208707', // Código IBGE do município (fallback para Goiás/Goiânia)
            ],
            'tomador' => [
                'razao_social' => $os->customer_name ?? 'Consumidor',
                'cpf' => !empty($os->customer_document) ? preg_replace('/\D/', '', $os->customer_document) : null,
                'email' => $os->customer_email ?? null,
                'telefone' => !empty($os->customer_phone) ? preg_replace('/\D/', '', $os->customer_phone) : null,
            ],
            'servico' => [
                'valor_servicos' => $totalServico,
                'discriminacao' => "Serviço de Manutenção / Conserto em {$os->device_name} (OS #{$os->os_number}). Defeito: {$os->reported_issue}. Laudo Técnico: " . ($os->technical_report ?? 'Manutenção realizada com sucesso.'),
                'codigo_tributacao_municipio' => '14.01', // Código padrão CNAE: Lubrificação, limpeza, revisão, conserto e restauração de bens
                'iss_retido' => false,
                'item_lista_servico' => '14.01',
            ]
        ];

        try {
            $url = $this->getBaseUrl($tenant) . "/v2/nfse?ref={$ref}";
            
            $response = Http::withBasicAuth($tenant->nfe_token, '')
                ->timeout(15)
                ->post($url, $payload);

            $data = $response->json();

            $status = 'processando';
            $pdfUrl = null;
            $xmlUrl = null;
            $accessKey = null;
            $nfeNumber = null;
            $errorMsg = null;

            if ($response->successful()) {
                $status = ($data['status'] ?? '') === 'autorizado' ? 'autorizado' : 'processando';
                $pdfUrl = $data['caminho_danfe'] ?? ($data['url_danfe'] ?? null);
                $xmlUrl = $data['caminho_xml_nota_fiscal'] ?? ($data['url_xml'] ?? null);
                $accessKey = $data['chave_nfe'] ?? ($data['codigo_verificacao'] ?? null);
                $nfeNumber = $data['numero'] ?? null;
            } else {
                $status = 'erro_autorizacao';
                $errorMsg = $data['mensagem'] ?? ($data['erros'][0]['mensagem'] ?? 'Erro retornado pela Prefeitura / Focus NFe');
            }

            $invoice = Invoice::create([
                'tenant_id' => $tenant->id,
                'service_order_id' => $os->id,
                'reference_code' => $ref,
                'type' => 'nfse',
                'status' => $status,
                'total_amount' => $totalServico,
                'customer_name' => $os->customer_name,
                'customer_cpf_cnpj' => $os->customer_document,
                'access_key' => $accessKey,
                'nfe_number' => $nfeNumber,
                'pdf_url' => $pdfUrl ? (str_starts_with($pdfUrl, 'http') ? $pdfUrl : $this->getBaseUrl($tenant) . $pdfUrl) : null,
                'xml_url' => $xmlUrl ? (str_starts_with($xmlUrl, 'http') ? $xmlUrl : $this->getBaseUrl($tenant) . $xmlUrl) : null,
                'error_message' => $errorMsg,
                'payload_sent' => $payload,
                'response_received' => $data,
            ]);

            return [
                'success' => $status !== 'erro_autorizacao',
                'invoice' => $invoice,
                'message' => $status === 'autorizado' ? 'NFS-e de Serviço emitida com sucesso!' : 'NFS-e enviada para processamento junto à Prefeitura.',
            ];
        } catch (\Exception $e) {
            Log::error("Erro Focus NFS-e: " . $e->getMessage());

            $invoice = Invoice::create([
                'tenant_id' => $tenant->id,
                'service_order_id' => $os->id,
                'reference_code' => $ref,
                'type' => 'nfse',
                'status' => 'erro_autorizacao',
                'total_amount' => $totalServico,
                'customer_name' => $os->customer_name,
                'error_message' => $e->getMessage(),
                'payload_sent' => $payload,
            ]);

            return [
                'success' => false,
                'message' => 'Falha na conexão com a Focus NFe: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Consulta status de uma nota fiscal emitida pela referência
     */
    public function checkStatus(Invoice $invoice): array
    {
        $tenant = $invoice->tenant ?? Tenant::find($invoice->tenant_id);

        if (!$tenant || empty($tenant->nfe_token)) {
            return ['success' => false, 'message' => 'Token Focus NFe não encontrado.'];
        }

        try {
            $url = $this->getBaseUrl($tenant) . "/v2/{$invoice->type}/{$invoice->reference_code}";
            
            $response = Http::withBasicAuth($tenant->nfe_token, '')->get($url);
            $data = $response->json();

            if ($response->successful()) {
                $status = ($data['status'] ?? '') === 'autorizado' ? 'autorizado' : $invoice->status;
                $pdfUrl = $data['caminho_danfe'] ?? $invoice->pdf_url;
                $xmlUrl = $data['caminho_xml_nota_fiscal'] ?? $invoice->xml_url;
                $accessKey = $data['chave_nfe'] ?? $invoice->access_key;
                $nfeNumber = $data['numero'] ?? $invoice->nfe_number;

                $invoice->update([
                    'status' => $status,
                    'access_key' => $accessKey,
                    'nfe_number' => $nfeNumber,
                    'pdf_url' => $pdfUrl ? (str_starts_with($pdfUrl, 'http') ? $pdfUrl : $this->getBaseUrl($tenant) . $pdfUrl) : null,
                    'xml_url' => $xmlUrl ? (str_starts_with($xmlUrl, 'http') ? $xmlUrl : $this->getBaseUrl($tenant) . $xmlUrl) : null,
                    'response_received' => $data,
                ]);

                return ['success' => true, 'status' => $status, 'invoice' => $invoice];
            }

            return ['success' => false, 'message' => 'Nota ainda em processamento pela SEFAZ.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
