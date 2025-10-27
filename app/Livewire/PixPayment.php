<?php

namespace App\Livewire;

use Livewire\Component;

class PixPayment extends Component
{
    public $model; // Donation, Order, ou CourseEnrollment
    public $modelType; // Tipo do modelo
    public $pixData = null;
    public $loading = false;
    public $error = null;

    public function mount($model, $modelType)
    {
        $this->model = $model;
        $this->modelType = $modelType;
        
        // Se já tem código PIX, carregar
        if ($model->hasPixCode()) {
            $this->loadPixData();
        }
    }

    public function generatePixCode()
    {
        $this->loading = true;
        $this->error = null;

        try {
            $pixData = $this->model->generatePixCode();
            $this->pixData = $pixData;
            
            session()->flash('success', 'Código PIX gerado com sucesso!');
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            session()->flash('error', 'Erro ao gerar código PIX: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function loadPixData()
    {
        $this->pixData = [
            'payload' => $this->model->pix_code ?? $this->model->getPixPayload(),
            'qr_code_base64' => $this->model->getPixQrCode(),
        ];

        // Adicionar informações extras se for PIX manual
        if (method_exists($this->model, 'payment_data') && isset($this->model->payment_data['pix_key'])) {
            $this->pixData['pix_key'] = $this->model->payment_data['pix_key'];
            $this->pixData['beneficiary_name'] = $this->model->payment_data['beneficiary_name'];
        } elseif (method_exists($this->model, 'pix_data') && isset($this->model->pix_data['pix_key'])) {
            $this->pixData['pix_key'] = $this->model->pix_data['pix_key'];
            $this->pixData['beneficiary_name'] = $this->model->pix_data['beneficiary_name'];
        }
    }

    public function copyPixCode()
    {
        $this->dispatch('copy-to-clipboard', ['text' => $this->pixData['payload']]);
        session()->flash('success', 'Código PIX copiado para a área de transferência!');
    }

    public function render()
    {
        return view('livewire.pix-payment');
    }
}
