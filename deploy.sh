#!/bin/bash

set -e

echo "🚀 Iniciando deploy do Laravel na Locaweb..."

# Definir diretórios
ROOT_DIR=/home/cehdec1
DEPLOY_TEMP=$ROOT_DIR/deploy_temp
PUBLIC_HTML=$ROOT_DIR/public_html
BACKUP_DIR=$ROOT_DIR/backups/$(date +%Y%m%d_%H%M%S)

# Criar diretório de backup
echo "📦 Criando backup..."
mkdir -p $BACKUP_DIR

# Backup de arquivos críticos (exceto storage e .env que serão preservados)
if [ -d "$PUBLIC_HTML" ]; then
    # Backup do .env atual
    if [ -f "$PUBLIC_HTML/.env" ]; then
        cp $PUBLIC_HTML/.env $BACKUP_DIR/.env
        echo "✅ Backup do .env criado"
    fi
    
    # Backup da pasta storage (preservar uploads e arquivos)
    if [ -d "$PUBLIC_HTML/storage/app/public" ]; then
        cp -r $PUBLIC_HTML/storage/app/public $BACKUP_DIR/storage_public
        echo "✅ Backup do storage criado"
    fi
fi

# Extrair arquivos novos
echo "📂 Extraindo arquivos novos..."
cd $DEPLOY_TEMP
tar -xzf deploy.tar.gz

# Preservar .env de produção
if [ -f "$BACKUP_DIR/.env" ]; then
    cp $BACKUP_DIR/.env .env
    echo "✅ .env de produção restaurado"
fi

# Criar estrutura de diretórios se não existir
mkdir -p $PUBLIC_HTML

# Sincronizar arquivos (exceto storage/app/public)
echo "🔄 Sincronizando arquivos..."
rsync -av --delete \
    --exclude='.env' \
    --exclude='storage/app/public/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='storage/logs/*' \
    ./ $PUBLIC_HTML/

# Restaurar storage/app/public se existir backup
if [ -d "$BACKUP_DIR/storage_public" ]; then
    mkdir -p $PUBLIC_HTML/storage/app/public
    cp -r $BACKUP_DIR/storage_public/* $PUBLIC_HTML/storage/app/public/
    echo "✅ Storage restaurado"
fi

# Criar diretórios necessários
echo "📁 Criando estrutura de diretórios..."
cd $PUBLIC_HTML
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p bootstrap/cache

# Configurar permissões
echo "🔐 Configurando permissões..."
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/public

# Instalar dependências do Composer
echo "📦 Instalando dependências do Composer..."
php83 composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Criar symlink do storage se não existir
if [ ! -L "$PUBLIC_HTML/public/storage" ]; then
    ln -s $PUBLIC_HTML/storage/app/public $PUBLIC_HTML/public/storage
    echo "✅ Symlink do storage criado"
fi

# Otimizações do Laravel
echo "⚡ Otimizando aplicação..."
php83 artisan config:cache
php83 artisan route:cache
php83 artisan view:cache
php83 artisan filament:cache-components

# Executar migrations (com cuidado)
echo "🗄️  Executando migrations..."
php83 artisan migrate --force

# Limpar caches antigos
php83 artisan cache:clear
php83 artisan optimize:clear

echo "✅ Deploy concluído com sucesso!"
echo "📊 Estatísticas:"
echo "   - Backup salvo em: $BACKUP_DIR"
echo "   - Aplicação em: $PUBLIC_HTML"
echo "   - Storage preservado: ✅"
echo "   - .env preservado: ✅"
