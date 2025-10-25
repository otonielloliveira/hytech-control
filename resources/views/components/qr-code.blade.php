@php
    // Component expects one of:
    // - $payment (Payment model)
    // - $paymentResult (array) possibly containing 'qr_code' (array or string), 'payment' => Payment
    // - $qrCode (pre-rendered HTML)
    $qrHtml = null;
    $pixCode = null;
    $paymentModel = $payment ?? ($paymentResult['payment'] ?? null);

    if (isset($qrCode) && $qrCode) {
        $qrHtml = $qrCode;
    } elseif (!empty($paymentModel)) {
        if (!empty($paymentModel->qr_code_base64)) {
            $qrHtml =
                '<img src="data:image/png;base64,' .
                $paymentModel->qr_code_base64 .
                '" alt="QR Code PIX" class="img-fluid" />';
            $pixCode = $paymentModel->pix_code ?? '';
        } elseif (!empty($paymentModel->qr_code_url)) {
            $qrHtml = '<img src="' . $paymentModel->qr_code_url . '" alt="QR Code PIX" class="img-fluid" />';
            $pixCode = $paymentModel->pix_code ?? '';
        } elseif (!empty($paymentModel->pix_code)) {
            $qrSvg = \SimpleSoftwareIO\QrCode::size(300)
                ->style('round')
                ->eye('circle')
                ->margin(1)
                ->generate($paymentModel->pix_code);
            $qrHtml = $qrSvg;
            $pixCode = $paymentModel->pix_code;
        }
    } elseif (!empty($paymentResult)) {
        // Check several possible shapes returned in paymentResult
        // 1) paymentResult['payment'] => ['qr_code_base64'|'qr_code_url'|'pix_code']
        if (!empty($paymentResult['payment'])) {
            $pr = $paymentResult['payment'];
            $qrBase64 = is_array($pr) ? $pr['qr_code_base64'] ?? null : $pr->qr_code_base64 ?? null;
            $qrUrl = is_array($pr) ? $pr['qr_code_url'] ?? null : $pr->qr_code_url ?? null;
            $pix = is_array($pr) ? $pr['pix_code'] ?? null : $pr->pix_code ?? null;

            if ($qrBase64) {
                $qrHtml = '<img src="data:image/png;base64,' . $qrBase64 . '" alt="QR Code PIX" class="img-fluid" />';
                $pixCode = $pix;
            } elseif ($qrUrl) {
                $qrHtml = '<img src="' . $qrUrl . '" alt="QR Code PIX" class="img-fluid" />';
                $pixCode = $pix;
            } elseif ($pix) {
                $qrSvg = \SimpleSoftwareIO\QrCode::size(300)->style('round')->eye('circle')->margin(1)->generate($pix);
                $qrHtml = $qrSvg;
                $pixCode = $pix;
            }
        }

        // 2) paymentResult may have top-level keys
        if (!$qrHtml) {
            if (!empty($paymentResult['qr_code_base64'])) {
                $qrHtml =
                    '<img src="data:image/png;base64,' .
                    $paymentResult['qr_code_base64'] .
                    '" alt="QR Code PIX" class="img-fluid" />';
                $pixCode = $paymentResult['pix_code'] ?? $pixCode;
            } elseif (!empty($paymentResult['qr_code_url'])) {
                $qrHtml = '<img src="' . $paymentResult['qr_code_url'] . '" alt="QR Code PIX" class="img-fluid" />';
                $pixCode = $paymentResult['pix_code'] ?? $pixCode;
            } elseif (!empty($paymentResult['pix_code'])) {
                $qrSvg = \SimpleSoftwareIO\QrCode::size(300)
                    ->style('round')
                    ->eye('circle')
                    ->margin(1)
                    ->generate($paymentResult['pix_code']);
                $qrHtml = $qrSvg;
                $pixCode = $paymentResult['pix_code'];
            } elseif (
                !empty($paymentResult['qr_code']) &&
                is_array($paymentResult['qr_code']) &&
                !empty($paymentResult['qr_code']['qr_code_image'])
            ) {
                $qrHtml =
                    '<img src="' .
                    ($paymentResult['qr_code']['qr_code_image'] ?? '') .
                    '" alt="QR Code PIX" class="img-fluid" />';
                $pixCode = $paymentResult['pix_code'] ?? $pixCode;
            }
        }
    }
@endphp

@if ($qrHtml)
    <div class="qr-code-wrapper">
        <div class="qr-code-container mb-3">
            {!! $qrHtml !!}
        </div>

        <div class="mb-3">
            <label class="form-label small text-muted">Código PIX (Copiar e Colar)</label>
            <div class="input-group">
                <input type="text" class="form-control text-center small" id="pixCode" value="{{ $pixCode ?? '' }}"
                    readonly>
                <button class="btn btn-outline-primary" type="button" onclick="copyPixCode(event)"
                    title="Copiar código PIX">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            // Define copyPixCode only if not already defined
            if (typeof copyPixCode === 'undefined') {
                function copyPixCode(evt) {
                    try {
                        const pixInput = document.getElementById('pixCode');
                        if (!pixInput) return;
                        pixInput.select();
                        pixInput.setSelectionRange(0, 99999);

                        navigator.clipboard.writeText(pixInput.value).then(function() {
                            const btn = evt ? (evt.currentTarget || evt.target) : null;
                            const button = btn && btn.tagName === 'BUTTON' ? btn : (btn ? btn.closest('button') : null);
                            if (button) {
                                const originalHTML = button.innerHTML;
                                button.innerHTML = '<i class="fas fa-check"></i>';
                                button.classList.remove('btn-outline-primary');
                                button.classList.add('btn-success');
                                setTimeout(() => {
                                    button.innerHTML = originalHTML;
                                    button.classList.remove('btn-success');
                                    button.classList.add('btn-outline-primary');
                                }, 1500);
                            }

                            // optional toast
                            if (typeof showToast === 'function') {
                                showToast('Código PIX copiado!', 'success');
                            }
                        });
                    } catch (e) {
                        console.error(e);
                    }
                }
            }
        </script>
    @endsection
@endif
