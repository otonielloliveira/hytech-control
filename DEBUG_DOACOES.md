# DEBUG - Sistema de Doações

## ✅ Status de Verificação

### Rotas
- ✅ `GET /doacoes` - donations.index
- ✅ `POST /doacoes` - donations.store
- ✅ `GET /doacoes/{id}/pagamento` - donations.payment
- ✅ `GET /doacoes/{id}/status` - donations.status
- ✅ `GET /doacoes/{id}/sucesso` - donations.success

### Configuração
- ✅ Gateway PIX Manual está ATIVO
- ✅ Outros gateways (asaas, mercadopago, etc) estão INATIVOS

### Arquivos
- ✅ Controller: `app/Http/Controllers/DonationController.php`
- ✅ View Formulário: `resources/views/donations/index.blade.php`
- ✅ View Pagamento: `resources/views/donations/payment.blade.php`
- ✅ Model: `app/Models/Donation.php`
- ✅ PaymentManager: `app/Services/Payment/PaymentManager.php`

## 🔍 Fluxo do Processo

### 1. Usuário preenche formulário (`/doacoes`)
- Nome
- Email
- Telefone (opcional)
- Valor
- Mensagem (opcional)

### 2. Submit do formulário → `DonationController@store`
```php
1. Valida os dados
2. Cria registro na tabela `donations`
3. Chama PaymentManager::createPixPayment()
4. Se sucesso: redireciona para /doacoes/{id}/pagamento
5. Se erro: volta para o formulário com mensagem de erro
```

### 3. Página de pagamento (`/doacoes/{id}/pagamento`)
- Mostra QR Code PIX
- Mostra código PIX para copiar
- Verifica status do pagamento via AJAX

## 🐛 Possíveis Problemas

### 1. PaymentManager não está criando o pagamento
**Solução**: Verificar logs em `storage/logs/laravel.log`

### 2. Redirecionamento não acontece
**Sintomas**:
- Form submit mas fica na mesma página
- Nenhuma mensagem de erro aparece
- Nenhuma mensagem de sucesso aparece

**Causas Possíveis**:
- Erro silencioso no controller
- Exceção não capturada
- Problema com o PaymentManager

### 3. Gateway não configurado corretamente
**Verificar**:
```php
DB::table('payment_gateway_configs')
    ->where('gateway', 'pix_manual')
    ->where('is_active', true)
    ->first();
```

## 🧪 Como Testar

### Teste 1: Criar doação direto no tinker
```php
php artisan tinker

$donation = \App\Models\Donation::create([
    'name' => 'Teste',
    'email' => 'teste@teste.com',
    'phone' => '(11) 99999-9999',
    'amount' => 50.00,
    'message' => 'Doação teste',
    'status' => 'pending',
    'expires_at' => now()->addMinutes(30),
    'ip_address' => '127.0.0.1'
]);

echo "ID: {$donation->id}\n";
echo "URL: " . route('donations.payment', $donation->id);
```

### Teste 2: Acessar diretamente a URL de pagamento
```
http://hytech-control.test/doacoes/6/pagamento
```

### Teste 3: Verificar logs em tempo real
```bash
tail -f storage/logs/laravel.log
```
Depois submeter o formulário e ver o que aparece.

## 📋 Checklist de Debug

1. [ ] Abrir o console do navegador (F12)
2. [ ] Preencher o formulário de doação
3. [ ] Clicar em "Gerar PIX para Doação"
4. [ ] Ver se aparece algum erro no console
5. [ ] Ver se aparece algum erro na página
6. [ ] Verificar os logs: `tail -f storage/logs/laravel.log`
7. [ ] Verificar se a doação foi criada: `select * from donations order by id desc limit 1;`
8. [ ] Verificar se o pagamento foi criado: `select * from payments order by id desc limit 1;`

## 🔧 Melhorias Implementadas

### Formulário
- ✅ Adicionado exibição de todos os erros no topo
- ✅ Adicionado spinner de loading no botão
- ✅ Botão desabilitado durante o processamento
- ✅ Feedback visual durante o submit

### Controller
- ✅ Adicionado try/catch para capturar exceções
- ✅ Adicionado logs detalhados
- ✅ Mensagens de erro mais claras

## 💡 Próximos Passos

Se ainda não estiver funcionando:

1. **Verificar no navegador**:
   - Abrir DevTools (F12)
   - Ir para aba Network
   - Submeter formulário
   - Ver se requisição POST acontece
   - Ver qual é a resposta (200, 302, 500, etc)

2. **Verificar no banco**:
   ```sql
   SELECT * FROM donations ORDER BY id DESC LIMIT 5;
   SELECT * FROM payments ORDER BY id DESC LIMIT 5;
   ```

3. **Verificar logs**:
   ```bash
   tail -100 storage/logs/laravel.log | grep -i "error\|exception\|doação"
   ```

4. **Teste direto no tinker**:
   ```php
   $manager = app(\App\Services\Payment\PaymentManager::class);
   $donation = \App\Models\Donation::latest()->first();
   $result = $manager->createPixPayment(
       $donation,
       ['name' => 'Teste', 'email' => 'teste@test.com'],
       50.00
   );
   dd($result);
   ```
