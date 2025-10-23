<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    protected $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    /**
     * Webhook genérico para todos os gateways
     */
    public function handleWebhook(Request $request, string $gateway)
    {
        try {
            Log::info("Webhook recebido do gateway: {$gateway}", [
                'headers' => $request->headers->all(),
                'body' => $request->all()
            ]);

            $result = $this->paymentManager->processWebhook($request->all(), $gateway);

            if ($result['success'] && isset($result['payment'])) {
                $payment = $result['payment'];
                
                // Atualizar status do pedido se o payment está relacionado a um order
                if ($payment->payable_type === 'App\\Models\\Order' && $payment->payable_id) {
                    $order = Order::find($payment->payable_id);
                    
                    if ($order) {
                        $this->updateOrderStatus($order, $payment);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Webhook processado com sucesso'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar webhook'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar status de pagamento (polling)
     */
    public function checkPaymentStatus(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $transactionId = $request->input('transaction_id');

            if (!$orderId && !$transactionId) {
                return response()->json([
                    'success' => false,
                    'error' => 'order_id ou transaction_id é obrigatório'
                ], 400);
            }

            $payment = null;

            if ($transactionId) {
                $payment = Payment::where('transaction_id', $transactionId)->first();
            } elseif ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    $payment = $order->payment;
                }
            }

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'error' => 'Pagamento não encontrado'
                ], 404);
            }

            // Verificar status atualizado no gateway
            $statusResult = $this->paymentManager->checkPaymentStatus($payment);

            if ($statusResult['success']) {
                $payment->refresh();
                
                // Atualizar order se necessário
                if ($payment->payable_type === 'App\\Models\\Order' && $payment->payable_id) {
                    $order = Order::find($payment->payable_id);
                    if ($order) {
                        $this->updateOrderStatus($order, $payment);
                    }
                }

                return response()->json([
                    'success' => true,
                    'payment' => [
                        'status' => $payment->status,
                        'transaction_id' => $payment->transaction_id,
                        'amount' => $payment->amount,
                        'paid_at' => $payment->paid_at?->toIso8601String(),
                        'payment_method' => $payment->payment_method,
                    ],
                    'order' => $order ? [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'payment_status' => $order->payment_status,
                        'status' => $order->status,
                    ] : null
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Erro ao verificar status do pagamento'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Erro ao verificar status de pagamento', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar status do pedido baseado no status do pagamento
     */
    private function updateOrderStatus(Order $order, Payment $payment)
    {
        $statusMap = [
            Payment::STATUS_APPROVED => 'paid',
            Payment::STATUS_PENDING => 'pending',
            Payment::STATUS_REJECTED => 'failed',
            Payment::STATUS_CANCELLED => 'cancelled',
            Payment::STATUS_REFUNDED => 'refunded',
        ];

        $paymentStatus = $statusMap[$payment->status] ?? 'pending';

        $order->update([
            'payment_status' => $paymentStatus
        ]);

        // Se pagamento aprovado, mudar status do pedido para processing
        if ($payment->status === Payment::STATUS_APPROVED && $order->status === 'pending') {
            $order->update([
                'status' => 'processing'
            ]);
        }

        Log::info('Order status atualizado', [
            'order_id' => $order->id,
            'payment_status' => $paymentStatus,
            'payment_gateway_status' => $payment->status
        ]);
    }
}
