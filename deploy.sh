#!/bin/bash

set -e

echo "🚀 Iniciando deploy do Laravel na Locaweb..."

# Definir diretórios
ROOT_DIR=/home/cehdec1
DEPLOY_TEMP=$ROOT_DIR/deploy_temp
APP_DIR=$ROOT_DIR/app
PUBLIC_HTML=$ROOT_DIR/public_html
BACKUP_DIR=$ROOT_DIR/backups/$(date +%Y%m%d_%H%M%S)

# Criar diretório de backup
echo "📦 Criando backup..."
mkdir -p $BACKUP_DIR

# Backup de arquivos críticos (exceto storage e .env que serão preservados)
if [ -d "$APP_DIR" ]; then
    # Backup do .env atual
    if [ -f "$APP_DIR/.env" ]; then
        cp $APP_DIR/.env $BACKUP_DIR/.env
        echo "✅ Backup do .env criado"
    fi
    
    # Backup da pasta storage (preservar uploads e arquivos)
    if [ -d "$APP_DIR/storage/app/public" ]; then
        cp -r $APP_DIR/storage/app/public $BACKUP_DIR/storage_public
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
mkdir -p $APP_DIR

# Sincronizar arquivos (exceto storage/app/public)
echo "🔄 Sincronizando arquivos..."
rsync -av --delete \
    --exclude='.env' \
    --exclude='storage/app/public/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='storage/logs/*' \
    --exclude='composer.phar' \
    ./ $APP_DIR/

# Restaurar storage/app/public se existir backup
if [ -d "$BACKUP_DIR/storage_public" ] && [ "$(ls -A $BACKUP_DIR/storage_public 2>/dev/null)" ]; then
    mkdir -p $APP_DIR/storage/app/public
    cp -r $BACKUP_DIR/storage_public/* $APP_DIR/storage/app/public/
    echo "✅ Storage restaurado"
else
    echo "ℹ️  Nenhum backup de storage encontrado (primeiro deploy)"
fi

# Criar diretórios necessários
echo "📁 Criando estrutura de diretórios..."
cd $APP_DIR
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
php83 /home/cehdec1/composer.phar install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Criar symlink do storage se não existir
if [ ! -L "$APP_DIR/public/storage" ]; then
    ln -s $APP_DIR/storage/app/public $APP_DIR/public/storage
    echo "✅ Symlink do storage criado"
fi

# Criar symlink do public_html para a pasta public do Laravel
echo "🔗 Criando symlink public_html → app/public..."
cd $ROOT_DIR
# Remover public_html se for diretório comum (não symlink)
if [ -d "$PUBLIC_HTML" ] && [ ! -L "$PUBLIC_HTML" ]; then
    rm -rf $PUBLIC_HTML
fi
# Criar symlink se não existir
if [ ! -L "$PUBLIC_HTML" ]; then
    ln -s $APP_DIR/public $PUBLIC_HTML
    echo "✅ Symlink public_html criado"
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
echo "   - Aplicação em: $APP_DIR"
echo "   - Public HTML: $PUBLIC_HTML → $APP_DIR/public"
echo "   - Storage preservado: ✅"
echo "   - .env preservado: ✅"
