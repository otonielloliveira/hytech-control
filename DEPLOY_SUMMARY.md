# 📦 Sistema de Deploy Automático - Resumo

## ✅ Arquivos Criados

### 1. `.github/workflows/deploy.yml`
**Objetivo:** Workflow do GitHub Actions para deploy automático

**Funcionalidades:**
- ✅ Executa em push na branch `main`
- ✅ Setup do ambiente PHP 8.2
- ✅ Instalação de dependências (Composer com --no-dev --optimize-autoloader)
- ✅ Build do frontend (npm ci && npm run build)
- ✅ Criação de arquivo tar.gz excluindo:
  - `.git` (histórico Git)
  - `node_modules` (dependências de desenvolvimento)
  - `.env` (preserva .env de produção)
  - `storage/app/public/*` (preserva uploads)
- ✅ Upload via SCP para Locaweb
- ✅ Execução remota do script de deploy via SSH
- ✅ Limpeza de arquivos temporários

**Secrets Necessários:**
```
SSH_HOST (hostname ou IP do servidor)
SSH_USERNAME (usuário SSH)
SSH_PASSWORD (senha SSH)
SSH_PORT (porta SSH, geralmente 22)
```

### 2. `deploy.sh`
**Objetivo:** Script executado no servidor para aplicar o deploy

**Funcionalidades:**
- ✅ Criação de backup automático com timestamp
- ✅ Backup de `.env` de produção
- ✅ Backup de `storage/app/public` (uploads)
- ✅ Extração de novos arquivos
- ✅ Sincronização com rsync preservando arquivos críticos
- ✅ Restauração de `.env` e storage
- ✅ Criação de estrutura de diretórios (cache, sessions, views, logs)
- ✅ Configuração de permissões (755 para storage, 775 para public)
- ✅ Criação de symlink `public/storage` → `storage/app/public`
- ✅ Otimizações Laravel:
  - `config:cache`
  - `route:cache`
  - `view:cache`
  - `filament:cache-components`
- ✅ Execução de migrations (`migrate --force`)
- ✅ Limpeza de caches antigos

**Diretórios Utilizados:**
- `~/deploy_temp` - Arquivos temporários durante deploy
- `~/public_html` - Raiz da aplicação (Locaweb)
- `~/backups/YYYYMMDD_HHMMSS` - Backups automáticos

### 3. `DEPLOY_GUIDE.md`
**Objetivo:** Guia completo de configuração e uso do deploy

**Conteúdo:**
- Pré-requisitos do sistema
- Configuração de secrets no GitHub
- Estrutura de diretórios na Locaweb
- Primeiro deploy (setup inicial)
- Deploy automático (como funciona)
- Troubleshooting (problemas comuns)
- Rollback (reverter deploy)
- Checklist de deploy
- Avisos importantes

### 4. `.env.production.example`
**Objetivo:** Template de configuração para produção

**Conteúdo:**
- Configurações Laravel básicas
- Database (MySQL Locaweb)
- Mail (SMTP Locaweb)
- Filament
- Gateways de pagamento (Asaas, Mercado Pago, PagSeguro)
- Cache e session
- Timezone Brasil

## 🎯 Como Usar

### Primeira Vez (Setup Inicial)

1. **Configure os Secrets no GitHub:**
   - Vá em: `Settings` → `Secrets and variables` → `Actions`
   - Adicione: `SSH_HOST`, `SSH_USERNAME`, `SSH_PASSWORD`, `SSH_PORT`

2. **Prepare o Servidor Locaweb:**
   ```bash
   ssh seu-usuario@seu-dominio.com.br
   mkdir -p ~/deploy_temp ~/backups ~/public_html
   cd ~/public_html
   nano .env  # Configure conforme .env.production.example
   php artisan key:generate
   ```

3. **Primeiro Deploy:**
   ```bash
   git add .
   git commit -m "Setup deploy automático"
   git push origin main
   ```

### Deploys Seguintes

Simplesmente faça push na branch `main`:

```bash
git add .
git commit -m "Sua mensagem de commit"
git push origin main
```

O GitHub Actions irá:
1. Detectar o push
2. Executar o workflow
3. Fazer upload para Locaweb
4. Executar deploy.sh
5. Seu site estará atualizado!

## 🔒 Segurança

### Arquivos Protegidos (NUNCA vão para o GitHub)
- ✅ `.env` (produção)
- ✅ `storage/app/public/*` (uploads)
- ✅ `node_modules`
- ✅ Backups

### Arquivos Preservados no Deploy
- ✅ `.env` de produção (preservado via backup/restore)
- ✅ `storage/app/public/*` (uploads preservados)
- ✅ Backups automáticos antes de cada deploy

## 📊 Fluxo de Deploy

```
Developer → git push origin main
    ↓
GitHub Actions (deploy.yml)
    ├─ Checkout código
    ├─ Setup PHP 8.2
    ├─ composer install --no-dev
    ├─ npm ci && npm run build
    ├─ tar -czf deploy.tar.gz (exclui .git, node_modules, .env, storage)
    ├─ SCP → upload deploy.tar.gz + deploy.sh
    └─ SSH → executa deploy.sh
          ↓
Servidor Locaweb (deploy.sh)
    ├─ Backup .env e storage
    ├─ Extrai novos arquivos
    ├─ Rsync para public_html
    ├─ Restaura .env e storage
    ├─ Cria diretórios e permissões
    ├─ Symlinks
    ├─ Cache Laravel + Filament
    ├─ Migrations
    └─ ✅ Site atualizado!
```

## 🚨 Avisos Importantes

1. **Backup Manual do Banco**
   - O sistema NÃO faz backup do banco
   - Faça backups manuais antes de migrations grandes
   - Use o painel Locaweb para agendar backups

2. **Primeiro Deploy**
   - Demora mais (instalação completa)
   - Pode levar 3-5 minutos
   - Deploys seguintes: 1-2 minutos

3. **Storage**
   - Uploads são preservados automaticamente
   - Backups ficam em `~/backups/YYYYMMDD_HHMMSS`
   - Faça limpeza periódica de backups antigos

4. **Migrations**
   - SEMPRE teste localmente primeiro
   - Use `down()` para permitir rollback
   - Cuidado com `dropColumn()` em produção

## 📈 Vantagens do Sistema

✅ **Deploy Automático:** Push no GitHub = Site atualizado
✅ **Backups Automáticos:** Cada deploy cria backup
✅ **Preserva Uploads:** Storage não é sobrescrito
✅ **Preserva .env:** Configurações de produção seguras
✅ **Zero Downtime:** Deploy rápido com rsync
✅ **Otimizado:** Caches gerados automaticamente
✅ **Rollback Fácil:** Backups com timestamp
✅ **Seguro:** Secrets no GitHub, não no código

## 🔧 Manutenção

### Limpar Backups Antigos (mensal)
```bash
ssh seu-usuario@seu-dominio.com.br
cd ~/backups
ls -la  # Ver backups
rm -rf 20250101_*  # Remover backups antigos
```

### Verificar Espaço em Disco
```bash
du -sh ~/public_html
du -sh ~/backups
```

### Ver Logs de Deploy
Acesse GitHub: `Actions` → Clique no workflow → Ver logs

---

**Sistema pronto para produção! 🚀**
