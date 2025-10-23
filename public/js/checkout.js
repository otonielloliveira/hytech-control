// Checkout - Script principal
// Gerencia todo o comportamento do checkout incluindo endereços, pagamentos e validações

// ========================================
// PAYMENT METHODS (Global scope for inline onclick)
// ========================================
window.selectPayment = function (element, gateway) {
    document
        .querySelectorAll("#payment-method-list .list-group-item")
        .forEach((item) => {
            item.classList.remove("active");
        });

    element.classList.add("active");

    const radio = element.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;

    // Show/hide payment fields
    const cardFields = document.getElementById("card-payment-fields");
    const boletoFields = document.getElementById("boleto-payment-fields");
    const pixFields = document.getElementById("pix-payment-fields");

    if (cardFields) cardFields.style.display = "none";
    if (boletoFields) boletoFields.style.display = "none";
    if (pixFields) pixFields.style.display = "none";

    if (
        gateway === "card" ||
        gateway === "credit_card" ||
        gateway === "card_gateway"
    ) {
        if (cardFields) cardFields.style.display = "block";
    } else if (gateway === "boleto" || gateway === "bank_slip") {
        if (boletoFields) boletoFields.style.display = "block";
    } else if (gateway === "pix") {
        if (pixFields) pixFields.style.display = "block";
    }
};

document.addEventListener("DOMContentLoaded", function () {
    const checkoutForm = document.getElementById("checkoutForm");

    // ========================================
    // TOAST NOTIFICATIONS
    // ========================================
    function showToast(message, type = "info", timeout = 3000) {
        let container = document.getElementById("toast-container-js");
        if (!container) {
            container = document.createElement("div");
            container.id = "toast-container-js";
            container.style.position = "fixed";
            container.style.top = "1rem";
            container.style.right = "1rem";
            container.style.zIndex = 11000;
            document.body.appendChild(container);
        }

        const toastEl = document.createElement("div");
        const bgClass =
            type === "error"
                ? "danger"
                : type === "success"
                ? "success"
                : type === "warning"
                ? "warning text-dark"
                : "primary";
        toastEl.className =
            "toast align-items-center text-bg-" + bgClass + " border-0";
        toastEl.setAttribute("role", "alert");
        toastEl.setAttribute("aria-live", "assertive");
        toastEl.setAttribute("aria-atomic", "true");
        toastEl.style.minWidth = "220px";

        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        container.appendChild(toastEl);
        const bsToast = new bootstrap.Toast(toastEl, { delay: timeout });
        bsToast.show();
        toastEl.addEventListener("hidden.bs.toast", () => toastEl.remove());
    }

    // Expose globally
    window.showToast = showToast;

    // Show server-side errors
    if (window.SERVER_ERROR) {
        showToast(window.SERVER_ERROR, "error", 5000);
    }
    if (Array.isArray(window.SERVER_ERRORS) && window.SERVER_ERRORS.length) {
        window.SERVER_ERRORS.forEach((msg) => showToast(msg, "error", 5000));
    }

    // ========================================
    // INPUT MASKS
    // ========================================
    function applyPhoneMask(el) {
        if (!el) return;
        el.addEventListener("input", function (e) {
            let value = e.target.value.replace(/\D/g, "");
            if (value.length > 10) {
                value = value.replace(/(\d{2})(\d{5})(\d{4})/, "($1) $2-$3");
            } else {
                value = value.replace(/(\d{2})(\d{4})(\d{4})/, "($1) $2-$3");
            }
            e.target.value = value;
        });
    }

    function applyCepMask(el) {
        if (!el) return;
        el.addEventListener("input", function (e) {
            let value = e.target.value.replace(/\D/g, "");
            if (value.length > 5) {
                value = value.replace(/(\d{5})(\d{1,3})/, "$1-$2");
            }
            e.target.value = value;
        });
    }

    const billingPhone = document.getElementById("billing_phone");
    const billingZip = document.getElementById("billing_zip");
    applyPhoneMask(billingPhone);
    applyCepMask(billingZip);

    // ========================================
    // CEP LOOKUP (ViaCEP)
    // ========================================
    function setupCepLookup(inputEl, onSuccess) {
        if (!inputEl) return;

        function lookup() {
            const cep = inputEl.value.replace(/\D/g, "");
            if (cep.length !== 8) return;
            inputEl.classList.remove("is-invalid");
            inputEl.classList.add("is-valid");
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then((r) => r.json())
                .then((data) => {
                    if (data.erro) {
                        inputEl.classList.add("is-invalid");
                        inputEl.classList.remove("is-valid");
                        return;
                    }
                    inputEl.classList.remove("is-invalid");
                    inputEl.classList.add("is-valid");
                    onSuccess(data);
                })
                .catch(() => {
                    inputEl.classList.add("is-invalid");
                    inputEl.classList.remove("is-valid");
                });
        }

        inputEl.addEventListener("keyup", lookup);
        inputEl.addEventListener("blur", lookup);
    }

    setupCepLookup(billingZip, function (data) {
        const street = data.logradouro || "";
        const city = data.localidade || "";
        const state = data.uf || "";
        const newStreet = document.getElementById("new_street");
        const newCity = document.getElementById("new_city");
        const newState = document.getElementById("new_state");
        if (street && newStreet) newStreet.value = street;
        if (city && newCity) newCity.value = city;
        if (state && newState) newState.value = state;
    });

    const newPostalCode = document.getElementById("new_postal_code");
    setupCepLookup(newPostalCode, function (data) {
        document.getElementById("new_street").value = data.logradouro || "";
        document.getElementById("new_neighborhood").value = data.bairro || "";
        document.getElementById("new_city").value = data.localidade || "";
        document.getElementById("new_state").value = data.uf || "";
        document.getElementById("new_number").focus();
    });

    // ========================================
    // ADDRESS SELECTION
    // ========================================
    const addressList = document.getElementById("address-list");
    if (addressList && addressList.children.length > 0) {
        [
            "billing_address",
            "billing_city",
            "billing_state",
            "billing_zip",
        ].forEach((id) => {
            const el = document.getElementById(id);
            if (el) el.value = "";
        });
    }

    if (addressList) {
        addressList.addEventListener("click", function (ev) {
            const target = ev.target.closest(".address-select");
            if (!target) return;

            const street = target.getAttribute("data-street") || "";
            const number = target.getAttribute("data-number") || "";
            const complement = target.getAttribute("data-complement") || "";
            const neighborhood = target.getAttribute("data-neighborhood") || "";
            let fullAddress = street;
            if (number) fullAddress += ", " + number;
            if (complement) fullAddress += " - " + complement;
            if (neighborhood) fullAddress += " - " + neighborhood;

            document.getElementById("billing_address").value = fullAddress;
            document.getElementById("billing_city").value =
                target.getAttribute("data-city") || "";
            document.getElementById("billing_state").value =
                target.getAttribute("data-state") || "";
            document.getElementById("billing_zip").value =
                target.getAttribute("data-postal-code") || "";

            const radio = target.querySelector("input[type=radio]");
            if (radio) radio.checked = true;
        });
    }

    // ========================================
    // SAVE ADDRESS AJAX
    // ========================================
    const saveAddressBtn = document.getElementById("saveAddressBtn");
    if (saveAddressBtn) {
        saveAddressBtn.addEventListener("click", function () {
            const form = document.getElementById("newAddressForm");
            const requiredFields = [
                "name",
                "new_postal_code",
                "new_street",
                "new_number",
                "new_neighborhood",
                "new_city",
                "new_state",
            ];
            let isValid = true;

            requiredFields.forEach((field) => {
                const input = form.querySelector(`[name="${field}"]`);
                if (!input || !input.value.trim()) {
                    isValid = false;
                    if (input) input.classList.add("is-invalid");
                } else {
                    input.classList.remove("is-invalid");
                }
            });

            if (!isValid) {
                showToast(
                    "Por favor, preencha todos os campos obrigatórios.",
                    "warning",
                    3500
                );
                return;
            }

            const payload = {
                name: form.querySelector('[name="name"]').value,
                postal_code: form.querySelector('[name="postal_code"]').value,
                street: form.querySelector('[name="street"]').value,
                number: form.querySelector('[name="number"]').value,
                complement: form.querySelector('[name="complement"]').value,
                neighborhood: form.querySelector('[name="neighborhood"]').value,
                city: form.querySelector('[name="city"]').value,
                state: form.querySelector('[name="state"]').value,
                is_default: form.querySelector('[name="is_default"]').checked
                    ? 1
                    : 0,
            };

            this.disabled = true;
            this.innerHTML =
                '<i class="fas fa-spinner fa-spin me-1"></i>Salvando...';

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                showToast("Erro: Token CSRF não encontrado", "error", 4000);
                this.disabled = false;
                this.innerHTML =
                    '<i class="fas fa-save me-1"></i>Salvar Endereço';
                return;
            }

            fetch("/client/addresses", {
                method: "POST",
                body: JSON.stringify(payload),
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken.content,
                    Accept: "application/json",
                },
            })
                .then((r) => r.json())
                .then((data) => {
                    if (data && data.success) {
                        showToast(
                            "Endereço adicionado com sucesso!",
                            "success",
                            1200
                        );
                        setTimeout(() => window.location.reload(), 1200);
                    } else {
                        showToast(
                            "Erro ao salvar endereço: " +
                                (data.message || "Erro desconhecido"),
                            "error",
                            4000
                        );
                    }
                })
                .catch((err) => {
                    console.error(err);
                    showToast(
                        "Erro ao salvar endereço. Tente novamente.",
                        "error",
                        4000
                    );
                })
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML =
                        '<i class="fas fa-save me-1"></i>Salvar Endereço';
                });
        });
    }

    // ========================================
    // INITIALIZE PAYMENT METHODS
    // ========================================
    // Initialize first payment method
    const firstMethod = document.querySelector(
        "#payment-method-list .list-group-item.active"
    );
    if (firstMethod) {
        const gateway = firstMethod.getAttribute("data-gateway");
        window.selectPayment(firstMethod, gateway);
    }

    // Card input masks
    const cardNumber = document.getElementById("card_number");
    if (cardNumber) {
        cardNumber.addEventListener("input", function (e) {
            let value = e.target.value.replace(/\D/g, "");
            value = value.replace(/(\d{4})(?=\d)/g, "$1 ");
            e.target.value = value;
        });
    }

    const cardExpiry = document.getElementById("card_expiry");
    if (cardExpiry) {
        cardExpiry.addEventListener("input", function (e) {
            let value = e.target.value.replace(/\D/g, "");
            if (value.length >= 2) {
                value = value.substring(0, 2) + "/" + value.substring(2, 4);
            }
            e.target.value = value;
        });
    }

    const cardCvv = document.getElementById("card_cvv");
    if (cardCvv) {
        cardCvv.addEventListener("input", function (e) {
            e.target.value = e.target.value.replace(/\D/g, "");
        });
    }

    // ========================================
    // FORM SUBMISSION
    // ========================================
    if (checkoutForm) {
        checkoutForm.addEventListener("submit", function (e) {
            // Validate terms
            const termsAccepted = document.getElementById("terms_accepted");
            if (termsAccepted && !termsAccepted.checked) {
                e.preventDefault();
                showToast(
                    "Você deve aceitar os termos de uso para continuar.",
                    "warning",
                    3500
                );
                return;
            }

            // Serialize payment data
            const selected = document.querySelector(
                "input[name=payment_method_id]:checked"
            );
            const gateway = selected
                ? selected.getAttribute("data-gateway")
                : null;
            const paymentDataField = document.getElementById("payment_data");

            if (
                gateway === "card" ||
                gateway === "credit_card" ||
                gateway === "card_gateway"
            ) {
                const cardHolder = document.getElementById("card_holder");
                const cardNumberEl = document.getElementById("card_number");
                const cardExpiryEl = document.getElementById("card_expiry");
                const cardCvvEl = document.getElementById("card_cvv");
                const cardInstallments =
                    document.getElementById("card_installments");

                if (
                    !cardHolder ||
                    !cardNumberEl ||
                    !cardExpiryEl ||
                    !cardCvvEl
                ) {
                    return;
                }

                if (
                    !cardHolder.value ||
                    !cardNumberEl.value ||
                    !cardExpiryEl.value ||
                    !cardCvvEl.value
                ) {
                    e.preventDefault();
                    showToast(
                        "Por favor, preencha todos os dados do cartão.",
                        "warning",
                        3500
                    );
                    return;
                }

                const card = {
                    card: {
                        holder: cardHolder.value,
                        number: cardNumberEl.value.replace(/\s/g, ""),
                        expiry: cardExpiryEl.value,
                        cvv: cardCvvEl.value,
                        installments: parseInt(cardInstallments.value) || 1,
                    },
                };

                if (paymentDataField) {
                    paymentDataField.value = JSON.stringify(card);
                }
            } else if (paymentDataField) {
                paymentDataField.value = "";
            }

            console.log("Form submitting...", {
                gateway: gateway,
                payment_data: paymentDataField ? paymentDataField.value : "N/A",
            });
        });
    }
});
