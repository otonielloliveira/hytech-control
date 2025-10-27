<?php

namespace App\Services;

use App\Models\PaymentGatewayConfig;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PixService
{
    /**
     * Gera o código PIX para pagamento
     *
     * @param float $amount Valor em reais
     * @param string $description Descrição do pagamento
     * @param string|null $txid ID único da transação (opcional)
     * @return array ['payload' => string, 'qr_code_base64' => string]
     */
    public function generatePixCode(float $amount, string $description, ?string $txid = null): array
    {
        $gateway = PaymentGatewayConfig::active()->first();
        
        if (!$gateway) {
            throw new \Exception('Nenhum gateway de pagamento ativo encontrado');
        }

        // Se for PIX Manual, gera o código com a chave do usuário
        if ($gateway->gateway === 'pix_manual') {
            return $this->generateManualPixCode($gateway, $amount, $description, $txid);
        }

        // Se for ASAAS, gera pela API
        if ($gateway->gateway === 'asaas') {
            return $this->generateAsaasPixCode($gateway, $amount, $description);
        }

        // Se for EFI Pay, gera pela API
        if ($gateway->gateway === 'efipay') {
            return $this->generateEfiPixCode($gateway, $amount, $description);
        }

        throw new \Exception('Gateway de pagamento não suporta PIX');
    }

    /**
     * Gera PIX Manual com a chave configurada pelo usuário
     */
    private function generateManualPixCode(PaymentGatewayConfig $gateway, float $amount, string $description, ?string $txid): array
    {
        $pixKey = $gateway->getCredential('pix_key');
        $pixKeyType = $gateway->getCredential('pix_key_type');
        $beneficiaryName = $gateway->getCredential('pix_beneficiary_name');
        
        if (!$pixKey || !$beneficiaryName) {
            throw new \Exception('Chave PIX ou nome do beneficiário não configurados');
        }

        // Gerar payload PIX EMV
        $payload = $this->buildPixPayload([
            'pixKey' => $pixKey,
            'description' => $description,
            'merchantName' => $beneficiaryName,
            'merchantCity' => config('app.city', 'Brasilia'),
            'txid' => $txid ?? $this->generateTxId(),
            'amount' => $amount,
        ]);

        // Gerar QR Code em base64
        $qrCode = $this->generateQrCodeBase64($payload);

        return [
            'payload' => $payload,
            'qr_code_base64' => $qrCode,
            'pix_key' => $pixKey,
            'beneficiary_name' => $beneficiaryName,
        ];
    }

    /**
     * Gera PIX via ASAAS
     */
    private function generateAsaasPixCode(PaymentGatewayConfig $gateway, float $amount, string $description): array
    {
        // TODO: Implementar integração com ASAAS API
        // Por enquanto, retorna um exemplo
        throw new \Exception('Integração ASAAS PIX em desenvolvimento');
    }

    /**
     * Gera PIX via EFI Pay
     */
    private function generateEfiPixCode(PaymentGatewayConfig $gateway, float $amount, string $description): array
    {
        // TODO: Implementar integração com EFI Pay API
        throw new \Exception('Integração EFI Pay PIX em desenvolvimento');
    }

    /**
     * Constrói o payload PIX EMV conforme padrão do Banco Central
     */
    private function buildPixPayload(array $data): string
    {
        $pixKey = $data['pixKey'];
        $description = $data['description'] ?? '';
        $merchantName = $this->normalize($data['merchantName']);
        $merchantCity = $this->normalize($data['merchantCity']);
        $txid = $data['txid'];
        $amount = number_format($data['amount'], 2, '.', '');

        // Payload Format Indicator
        $payload = "000201";
        
        // Merchant Account Information
        $gui = "br.gov.bcb.pix";
        $pixKeyId = "01" . str_pad(strlen($pixKey), 2, '0', STR_PAD_LEFT) . $pixKey;
        
        if (!empty($description)) {
            $descriptionField = "02" . str_pad(strlen($description), 2, '0', STR_PAD_LEFT) . $description;
            $pixKeyId .= $descriptionField;
        }
        
        $merchantAccountInfo = "0014{$gui}{$pixKeyId}";
        $payload .= "26" . str_pad(strlen($merchantAccountInfo), 2, '0', STR_PAD_LEFT) . $merchantAccountInfo;
        
        // Merchant Category Code
        $payload .= "52040000";
        
        // Transaction Currency (986 = BRL)
        $payload .= "5303986";
        
        // Transaction Amount
        if ($amount > 0) {
            $payload .= "54" . str_pad(strlen($amount), 2, '0', STR_PAD_LEFT) . $amount;
        }
        
        // Country Code
        $payload .= "5802BR";
        
        // Merchant Name
        $payload .= "59" . str_pad(strlen($merchantName), 2, '0', STR_PAD_LEFT) . $merchantName;
        
        // Merchant City
        $payload .= "60" . str_pad(strlen($merchantCity), 2, '0', STR_PAD_LEFT) . $merchantCity;
        
        // Additional Data Field Template
        if (!empty($txid)) {
            $txidField = "05" . str_pad(strlen($txid), 2, '0', STR_PAD_LEFT) . $txid;
            $payload .= "62" . str_pad(strlen($txidField), 2, '0', STR_PAD_LEFT) . $txidField;
        }
        
        // CRC16
        $payload .= "6304";
        $payload .= $this->crc16($payload);
        
        return $payload;
    }

    /**
     * Normaliza texto removendo acentos e caracteres especiais
     */
    private function normalize(string $text): string
    {
        $text = preg_replace('/[^a-zA-Z0-9 ]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $text));
        return strtoupper(substr($text, 0, 25));
    }

    /**
     * Calcula CRC16 CCITT (0xFFFF)
     */
    private function crc16(string $payload): string
    {
        $polynomial = 0x1021;
        $crc = 0xFFFF;

        for ($i = 0; $i < strlen($payload); $i++) {
            $crc ^= (ord($payload[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ $polynomial) : ($crc << 1);
                $crc &= 0xFFFF;
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }

    /**
     * Gera QR Code em formato Base64
     */
    private function generateQrCodeBase64(string $payload): string
    {
        $qrCode = QrCode::format('png')
            ->size(300)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($payload);
        
        return 'data:image/png;base64,' . base64_encode($qrCode);
    }

    /**
     * Gera ID de transação único
     */
    private function generateTxId(): string
    {
        return strtoupper(substr(uniqid(), -25));
    }

    /**
     * Valida se o gateway ativo suporta PIX
     */
    public function pixIsAvailable(): bool
    {
        $gateway = PaymentGatewayConfig::active()->first();
        
        if (!$gateway) {
            return false;
        }

        return in_array($gateway->gateway, ['pix_manual', 'asaas', 'efipay']);
    }

    /**
     * Retorna informações do gateway PIX ativo
     */
    public function getActivePixGateway(): ?array
    {
        $gateway = PaymentGatewayConfig::active()->first();
        
        if (!$gateway || !in_array($gateway->gateway, ['pix_manual', 'asaas', 'efipay'])) {
            return null;
        }

        return [
            'gateway' => $gateway->gateway,
            'name' => $gateway->name,
            'is_manual' => $gateway->gateway === 'pix_manual',
        ];
    }
}
