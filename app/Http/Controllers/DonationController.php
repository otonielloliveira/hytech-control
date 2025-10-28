<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Services\Payment\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class DonationController extends Controller
{
    protected $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }
    /**
     * Exibe o formulário de doação
     */
    public function index()
    {
        return view('donations.index');
    }

    /**
     * Processa o formulário de doação
     */
    public function store(Request $request)
    {
        Log::info('Iniciando criação de doação', $request->all());
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'document' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $document = preg_replace('/\D/', '', $value);
                    if (strlen($document) < 11 || strlen($document) > 14) {
                        $fail('O CPF deve ter 11 dígitos ou o CNPJ deve ter 14 dígitos.');
                    }
                    if (strlen($document) != 11 && strlen($document) != 14) {
                        $fail('Digite um CPF (11 dígitos) ou CNPJ (14 dígitos) válido.');
                    }
                },
            ],
            'amount' => 'required|numeric|min:1|max:10000',
            'message' => 'nullable|string|max:500',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Por favor, insira um email válido.',
            'document.required' => 'O CPF/CNPJ é obrigatório para gerar o QR Code PIX.',
            'amount.required' => 'O valor da doação é obrigatório.',
            'amount.min' => 'O valor mínimo para doação é R$ 1,00.',
            'amount.max' => 'O valor máximo para doação é R$ 10.000,00.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Criar a doação
        $donation = Donation::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'document' => preg_replace('/\D/', '', $request->document), // Remove formatação e salva
            'amount' => $request->amount,
            'message' => $request->message,
            'status' => Donation::STATUS_PENDING,
            'expires_at' => Carbon::now()->addMinutes(30), // Expira em 30 minutos
            'ip_address' => $request->ip(),
        ]);

        // Preparar dados do pagador
        $payerData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'document' => preg_replace('/\D/', '', $request->document), // Remove formatação
        ];

        Log::info('DonationController - Dados do pagador preparados:', [
            'payer_data' => $payerData,
            'document_length' => strlen($payerData['document']),
            'amount' => $request->amount,
        ]);

        // Criar pagamento PIX usando o PaymentManager
        try {
            $paymentResult = $this->paymentManager->createPixPayment(
                $donation, 
                $payerData, 
                $request->amount,
                [
                    'description' => 'Doação para o projeto',
                    'metadata' => [
                        'donation_id' => $donation->id,
                        'message' => $request->message,
                    ]
                ]
            );
        } catch (\Exception $e) {
            Log::error('Erro ao criar pagamento PIX:', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $donation->update(['status' => Donation::STATUS_CANCELLED]);
            
            return back()
                ->withErrors(['payment' => 'Erro ao processar pagamento: ' . $e->getMessage()])
                ->withInput();
        }

        if ($paymentResult['success']) {
            return redirect()->route('donations.payment', $donation->id)
                            ->with('success', 'Doação criada com sucesso! Use o QR Code para realizar o pagamento.');
        } else {
            $donation->update(['status' => Donation::STATUS_CANCELLED]);
            
            return back()
                ->withErrors(['payment' => $paymentResult['error'] ?? 'Erro ao processar pagamento'])
                ->withInput();
        }
    }

    /**
     * Exibe a página de pagamento com QR Code
     */
    public function payment($id)
    {
        $donation = Donation::findOrFail($id);
        $payment = $donation->latestPayment();

        // Verificar se não expirou
        if ($donation->isExpired() || ($payment && $payment->isExpired())) {
            $donation->markAsExpired();
            if ($payment) {
                $payment->update(['status' => 'cancelled']);
            }
            
            return redirect()->route('donations.index')
                           ->with('error', 'Esta doação expirou. Por favor, faça uma nova doação.');
        }

        // Verificar se é PIX Manual
        $isPixManual = $payment && $payment->gateway === 'pix_manual';
        
        // Dados do PIX Manual
        $pixManualData = null;
        if ($isPixManual && $payment->gateway_response) {
            $gatewayResponse = is_string($payment->gateway_response) 
                ? json_decode($payment->gateway_response, true) 
                : $payment->gateway_response;
                
            $pixManualData = [
                'pix_key' => $gatewayResponse['pix_key'] ?? $payment->pix_code,
                'pix_key_type' => $this->getPixKeyType($gatewayResponse['pix_key'] ?? $payment->pix_code),
                'beneficiary_name' => $gatewayResponse['beneficiary_name'] ?? 'Destinatário',
                'amount' => $payment->amount,
            ];
        }

        // Gerar QR Code apenas se NÃO for PIX Manual
        $qrCode = null;
        if (!$isPixManual) {
            if ($payment && $payment->qr_code_base64) {
                // Se tiver QR Code em base64, use diretamente
                $qrCode = '<img src="data:image/png;base64,' . $payment->qr_code_base64 . '" alt="QR Code PIX" class="img-fluid" />';
            } elseif ($payment && $payment->qr_code_url) {
                // Se for uma URL de QR Code, use diretamente
                $qrCode = '<img src="' . $payment->qr_code_url . '" alt="QR Code PIX" class="img-fluid" />';
            } elseif ($payment && $payment->pix_code) {
                // Gerar QR Code usando o código PIX
                $qrCode = QrCode::size(300)
                               ->style('round')
                               ->eye('circle')
                               ->margin(1)
                               ->generate($payment->pix_code);
            }
        }

        return view('donations.payment', compact('donation', 'payment', 'qrCode', 'isPixManual', 'pixManualData'));
    }
    
    /**
     * Detecta o tipo de chave PIX
     */
    private function getPixKeyType($pixKey)
    {
        if (!$pixKey) {
            return 'Chave PIX';
        }
        
        // Remove caracteres não numéricos para verificar CPF/CNPJ/Telefone
        $numbersOnly = preg_replace('/\D/', '', $pixKey);
        
        // CPF (11 dígitos)
        if (strlen($numbersOnly) === 11 && is_numeric($numbersOnly)) {
            return 'CPF';
        }
        
        // CNPJ (14 dígitos)
        if (strlen($numbersOnly) === 14 && is_numeric($numbersOnly)) {
            return 'CNPJ';
        }
        
        // Telefone (10 ou 11 dígitos com DDD)
        if ((strlen($numbersOnly) === 10 || strlen($numbersOnly) === 11) && is_numeric($numbersOnly)) {
            return 'Telefone';
        }
        
        // Email
        if (filter_var($pixKey, FILTER_VALIDATE_EMAIL)) {
            return 'E-mail';
        }
        
        // Chave Aleatória (UUID format)
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $pixKey)) {
            return 'Chave Aleatória';
        }
        
        return 'Chave PIX';
    }

    /**
     * Verifica o status do pagamento (AJAX)
     */
    public function checkStatus($id)
    {
        $donation = Donation::findOrFail($id);
        $payment = $donation->latestPayment();

        if ($payment) {
            // Verificar status no gateway
            $statusResult = $this->paymentManager->checkPaymentStatus($payment);
            
            if ($statusResult['success'] && $statusResult['status_changed']) {
                // Atualizar doação baseado no status do pagamento
                if ($payment->fresh()->isApproved()) {
                    $donation->update([
                        'status' => Donation::STATUS_PAID,
                        'paid_at' => now(),
                        'payment_id' => $payment->gateway_transaction_id
                    ]);
                } elseif ($payment->fresh()->isRejected()) {
                    $donation->update(['status' => Donation::STATUS_CANCELLED]);
                }
            }
        }

        // Verificar se expirou
        if ($donation->isExpired() || ($payment && $payment->isExpired())) {
            $donation->markAsExpired();
            if ($payment) {
                $payment->update(['status' => 'cancelled']);
            }
        }

        return response()->json([
            'status' => $donation->fresh()->status,
            'status_label' => $donation->fresh()->status_label,
            'is_paid' => $donation->fresh()->isPaid(),
            'is_expired' => $donation->fresh()->isExpired(),
            'payment_status' => $payment ? $payment->fresh()->status : null,
        ]);
    }

    /**
     * Simula confirmação de pagamento (para testes)
     */
    public function simulatePayment($id)
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        $donation = Donation::findOrFail($id);
        $donation->markAsPaid('SIMULATED_' . time());

        return redirect()->route('donations.success', $donation->id);
    }

    /**
     * Página de sucesso
     */
    public function success($id)
    {
        $donation = Donation::findOrFail($id);

        if (!$donation->isPaid()) {
            return redirect()->route('donations.payment', $id);
        }

        return view('donations.success', compact('donation'));
    }

    /**
     * Webhook para receber notificações do gateway de pagamento
     */
    public function webhook(Request $request)
    {
        // Processar webhook usando o PaymentManager
        $result = $this->paymentManager->processWebhook(
            $request->all(),
            $request->header('X-Gateway-Name') // Gateway pode enviar seu nome no header
        );

        if ($result['success'] && isset($result['payment'])) {
            $payment = $result['payment'];
            $donation = $payment->payable;

            if ($donation && $payment->isApproved()) {
                $donation->update([
                    'status' => Donation::STATUS_PAID,
                    'paid_at' => now(),
                    'payment_id' => $payment->gateway_transaction_id
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
