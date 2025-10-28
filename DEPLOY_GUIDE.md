# 🚀 Guia de Deploy - Locaweb via GitHub Actions

## 📋 Pré-requisitos

1. **Repositório GitHub** com o código do projeto
2. **Acesso SSH à Locaweb** (hostname, usuário, senha, porta)
3. **PHP 8.2+** no servidor Locaweb
4. **Composer** instalado no servidor
5. **Banco de dados MySQL** configurado

## 🔐 Configuração de Secrets no GitHub

Acesse: `Settings` → `Secrets and variables` → `Actions` → `New repository secret`

Configure os seguintes secrets:

```
SSH_HOST=seu-dominio.com.br (ou IP do servidor)
SSH_USERNAME=seu-usuario-ssh
SSH_PASSWORD=sua-senha-ssh
SSH_PORT=22 (ou porta customizada da Locaweb)
```

## 📁 Estrutura de Diretórios na Locaweb

```
~/
├── public_html/              # Raiz web da Locaweb
│   ├── .env                  # ⚠️ Configuração de produção
│   ├── index.php             # Entry point do Laravel
│   ├── storage/              # ⚠️ Arquivos de upload
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   └── vendor/
├── deploy_temp/              # Temp durante deploy
└── backups/                  # Backups automáticos
    └── YYYYMMDD_HHMMSS/
        ├── .env
        └── storage_public/
```

## ⚙️ Primeiro Deploy (Configuração Inicial)

### 1. Preparar o Servidor Locaweb

Conecte via SSH:

```bash
ssh seu-usuario@seu-dominio.com.br
```

### 2. Criar Estrutura de Diretórios

```bash
mkdir -p ~/deploy_temp
mkdir -p ~/backups
mkdir -p ~/public_html
```

### 3. Configurar .env de Produção

Na Locaweb, crie o arquivo `.env`:

```bash
cd ~/public_html
nano .env
```

Configure com seus dados de produção:

```env
APP_NAME="HyTech Control"
APP_ENV=production
APP_KEY=base64:SUA_CHAVE_AQUI
APP_DEBUG=false
APP_URL=https://seu-dominio.com.br

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=seu_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

# Filament
FILAMENT_PATH=admin

# Asaas (se usar)
ASAAS_API_KEY=sua_chave
ASAAS_ENVIRONMENT=production

# Storage
FILESYSTEM_DISK=public

# Mail (configure conforme Locaweb)
MAIL_MAILER=smtp
MAIL_HOST=smtp.seu-dominio.com.br
MAIL_PORT=587
MAIL_USERNAME=contato@seu-dominio.com.br
MAIL_PASSWORD=senha-email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=contato@seu-dominio.com.br
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Gerar APP_KEY (se não tiver)

```bash
php artisan key:generate
```

### 5. Configurar Permissões

```bash
chmod -R 755 ~/public_html/storage
chmod -R 755 ~/public_html/bootstrap/cache
```

## 🔄 Deploy Automático

### Disparar Deploy

O deploy acontece automaticamente quando você faz push na branch `main`:

```bash
git add .
git commit -m "Deploy para produção"
git push origin main
```

### Acompanhar Deploy

Acesse: `Actions` → Clique no workflow em execução

Você verá:
1. ✅ Checkout do código
2. ✅ Setup do PHP 8.2
3. ✅ Instalação de dependências (Composer)
4. ✅ Build do frontend (npm)
5. ✅ Upload via SCP
6. ✅ Execução do script de deploy
7. ✅ Otimizações do Laravel

## 📊 O Que o Deploy Faz

1. **Backup Automático**
   - `.env` atual → `~/backups/YYYYMMDD_HHMMSS/.env`
   - `storage/app/public` → `~/backups/YYYYMMDD_HHMMSS/storage_public`

2. **Sincronização de Arquivos**
   - Copia novos arquivos do GitHub
   - **Preserva** `.env` de produção
   - **Preserva** `storage/app/public` (uploads)

3. **Estrutura de Diretórios**
   - Cria `storage/framework/cache`
   - Cria `storage/framework/sessions`
   - Cria `storage/framework/views`
   - Cria `bootstrap/cache`

4. **Permissões**
   - `chmod 755` em storage e bootstrap/cache
   - `chmod 775` em storage/app/public

5. **Symlinks**
   - `public/storage` → `storage/app/public`

6. **Otimizações Laravel**
   - `config:cache` (cache de configurações)
   - `route:cache` (cache de rotas)
   - `view:cache` (cache de views)
   - `filament:cache-components` (cache do Filament)

7. **Migrations**
   - Executa `migrate --force` (novas tabelas/campos)

## 🔧 Troubleshooting

### Erro: "Permission denied"

```bash
ssh seu-usuario@seu-dominio.com.br
chmod 755 ~/deploy_temp
chmod 755 ~/public_html
```

### Erro: "Storage not writable"

```bash
chmod -R 775 ~/public_html/storage
chmod -R 775 ~/public_html/bootstrap/cache
```

### Erro: "Class not found" ou "Route not found"

```bash
cd ~/public_html
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Erro: "Too many redirects"

Verifique se o `.htaccess` está em `~/public_html/public/.htaccess`

### Arquivos de Upload Sumindo

Verifique se `storage/app/public` está preservado:

```bash
ls -la ~/public_html/storage/app/public
ls -la ~/backups/*/storage_public
```

## 🎯 Rollback (Reverter Deploy)

Se algo der errado, você pode reverter:

```bash
ssh seu-usuario@seu-dominio.com.br

# Listar backups
ls -la ~/backups/

# Escolher backup (exemplo: 20250128_143000)
BACKUP_DIR=~/backups/20250128_143000

# Restaurar .env
cp $BACKUP_DIR/.env ~/public_html/.env

# Restaurar storage
cp -r $BACKUP_DIR/storage_public/* ~/public_html/storage/app/public/

# Limpar caches
cd ~/public_html
php artisan optimize:clear
```

## 📝 Checklist de Deploy

Antes do primeiro deploy:
- [ ] Secrets configurados no GitHub
- [ ] `.env` criado manualmente na Locaweb
- [ ] `APP_KEY` gerado
- [ ] Banco de dados criado
- [ ] Credenciais de email configuradas
- [ ] Domínio apontando para Locaweb

Para cada deploy:
- [ ] Código testado localmente
- [ ] Migrations testadas
- [ ] Seeders removidos (ou ajustados para produção)
- [ ] `.env.example` atualizado (se necessário)
- [ ] Dependências atualizadas (`composer.json`)

## 🚨 Avisos Importantes

1. **NUNCA** commite o `.env` no GitHub
2. **SEMPRE** teste migrations localmente primeiro
3. **Backups** são feitos automaticamente, mas mantenha backups do banco também
4. **Storage** é preservado, mas faça backup manual periódico
5. **Primeiro deploy** pode demorar (instalação de dependências)

## 📞 Suporte Locaweb

- Painel: https://painel.locaweb.com.br
- Suporte: https://suporte.locaweb.com.br
- Telefone: 3544-0000

## ✅ Deploy Bem-Sucedido!

Após o deploy, verifique:

1. Acesse seu site: `https://seu-dominio.com.br`
2. Acesse o admin: `https://seu-dominio.com.br/admin`
3. Teste uploads de arquivos
4. Verifique logs: `storage/logs/laravel.log`

---

**Desenvolvido para Laravel 11 + Filament 3 na Locaweb** 🚀
