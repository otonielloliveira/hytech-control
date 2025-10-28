# ⚡ Quick Start - Deploy Automático

## 🚀 Setup em 3 Passos

### 1️⃣ Configure os Secrets no GitHub

```
Repositório → Settings → Secrets and variables → Actions → New secret
```

Adicione 4 secrets:
- `SSH_HOST` = seu-dominio.com.br (ou IP)
- `SSH_USERNAME` = seu_usuario_ssh
- `SSH_PASSWORD` = sua_senha_ssh
- `SSH_PORT` = 22

### 2️⃣ Prepare o Servidor (primeira vez apenas)

SSH na Locaweb:
```bash
ssh seu-usuario@seu-dominio.com.br
mkdir -p ~/deploy_temp ~/backups ~/public_html
cd ~/public_html
cp ../.env.production.example .env
nano .env  # Configure suas credenciais
php artisan key:generate
chmod -R 755 storage bootstrap/cache
```

### 3️⃣ Deploy!

```bash
git add .
git commit -m "Seu commit"
git push origin main
```

✅ Pronto! O deploy acontece automaticamente.

## 📱 Acompanhar Deploy

GitHub → Actions → Ver workflow em execução

Tempo médio: **1-2 minutos**

## 🆘 Problemas?

### Deploy falhou?
1. Verifique os logs no GitHub Actions
2. Confira se os secrets estão corretos
3. Teste SSH manual: `ssh usuario@host`

### Site com erro 500?
```bash
ssh seu-usuario@seu-dominio.com.br
cd ~/public_html
php artisan optimize:clear
chmod -R 755 storage
```

### Storage não funciona?
```bash
cd ~/public_html
ln -s ~/public_html/storage/app/public ~/public_html/public/storage
chmod -R 775 storage/app/public
```

## 📖 Documentação Completa

- `DEPLOY_GUIDE.md` - Guia detalhado
- `DEPLOY_SUMMARY.md` - Resumo técnico
- `.env.production.example` - Template de configuração

---

**Dúvidas? Consulte DEPLOY_GUIDE.md** 📚
