<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class DonationController extends Controller
{
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'amount' => 'required|numeric|min:1|max:10000',
            'message' => 'nullable|string|max:500',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Por favor, insira um email válido.',
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
            'amount' => $request->amount,
            'message' => $request->message,
            'expires_at' => Carbon::now()->addMinutes(30), // Expira em 30 minutos
            'ip_address' => $request->ip(),
        ]);

        // Gerar código PIX
        $pixCode = $donation->generatePixCode();

        return redirect()->route('donations.payment', $donation->id)
                        ->with('success', 'Doação criada com sucesso! Use o QR Code para realizar o pagamento.');
    }

    /**
     * Exibe a página de pagamento com QR Code
     */
    public function payment($id)
    {
        $donation = Donation::findOrFail($id);

        // Verificar se não expirou
        if ($donation->isExpired()) {
            $donation->markAsExpired();
            return redirect()->route('donations.index')
                           ->with('error', 'Esta doação expirou. Por favor, faça uma nova doação.');
        }

        // Gerar QR Code
        $qrCode = null;
        if ($donation->pix_code) {
            $qrCode = QrCode::size(300)
                           ->style('round')
                           ->eye('circle')
                           ->margin(1)
                           ->generate($donation->pix_code);
        }

        return view('donations.payment', compact('donation', 'qrCode'));
    }

    /**
     * Verifica o status do pagamento (AJAX)
     */
    public function checkStatus($id)
    {
        $donation = Donation::findOrFail($id);

        // Aqui você integraria com seu gateway de pagamento para verificar o status
        // Por enquanto, vou simular uma verificação básica
        
        if ($donation->isExpired()) {
            $donation->markAsExpired();
        }

        return response()->json([
            'status' => $donation->status,
            'status_label' => $donation->status_label,
            'is_paid' => $donation->isPaid(),
            'is_expired' => $donation->isExpired(),
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
        // Aqui você processaria as notificações do seu gateway de pagamento
        // Exemplo para diferentes gateways:
        
        $paymentId = $request->input('payment_id');
        $status = $request->input('status');
        
        if ($paymentId && $status === 'approved') {
            $donation = Donation::where('payment_id', $paymentId)->first();
            if ($donation && $donation->isPending()) {
                $donation->markAsPaid();
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
