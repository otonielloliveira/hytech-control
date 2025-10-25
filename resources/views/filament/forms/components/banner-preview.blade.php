<div>
    <div class="banner-preview-component">
        <div class="preview-header">
            <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #374151;">
                <i class="fas fa-eye"></i> Preview do Banner
            </h3>
            <div class="preview-actions">
                <button type="button" class="preview-toggle-btn" onclick="togglePreviewSize()">
                    <i class="fas fa-expand"></i> Tela Cheia
                </button>
                <button type="button" class="preview-refresh-btn" onclick="refreshPreview()">
                    <i class="fas fa-sync-alt"></i> Atualizar
                </button>
            </div>
        </div>
        
        <div class="preview-container" id="bannerPreviewContainer">
            <div class="preview-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Carregando preview...</p>
            </div>
            <div class="preview-content" id="bannerPreviewContent" style="display: none;">
                <!-- Preview será renderizado aqui -->
            </div>
        </div>
        
        <div class="preview-info">
            <small style="color: #6b7280;">
                <i class="fas fa-info-circle"></i> 
                O preview é atualizado automaticamente ao modificar os campos. 
                <strong>Clique em "Atualizar"</strong> se não atualizar sozinho.
            </small>
        </div>
    </div>

    <style>
        .banner-preview-component {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        
        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .preview-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .preview-toggle-btn,
        .preview-refresh-btn {
            background: #fff;
            border: 1px solid #d1d5db;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            color: #374151;
        }
        
        .preview-toggle-btn:hover,
        .preview-refresh-btn:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
        }
        
        .preview-container {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            min-height: 400px;
            position: relative;
            overflow: hidden;
        }
        
        .preview-container.fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            border-radius: 0;
            min-height: 100vh;
        }
        
        .preview-loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 400px;
            color: #9ca3af;
        }
        
        .preview-loading i {
            font-size: 32px;
            margin-bottom: 1rem;
        }
        
        .preview-content {
            width: 100%;
            height: 100%;
        }
        
        .preview-info {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }
        
        /* Estilos do preview do banner */
        .banner-preview-wrapper {
            position: relative;
            width: 100%;
            background: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .banner-preview-item {
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            display: flex;
        }
        
        .banner-preview-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }
        
        .banner-preview-content {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            color: white;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .preview-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            
            .preview-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>

    <script>
        let isFullscreen = false;
        let previewRefreshTimeout;
        
        function togglePreviewSize() {
            const container = document.getElementById('bannerPreviewContainer');
            const btn = document.querySelector('.preview-toggle-btn');
            
            isFullscreen = !isFullscreen;
            
            if (isFullscreen) {
                container.classList.add('fullscreen');
                btn.innerHTML = '<i class="fas fa-compress"></i> Sair da Tela Cheia';
            } else {
                container.classList.remove('fullscreen');
                btn.innerHTML = '<i class="fas fa-expand"></i> Tela Cheia';
            }
        }
        
        function refreshPreview() {
            const btn = document.querySelector('.preview-refresh-btn');
            const icon = btn.querySelector('i');
            
            icon.classList.add('fa-spin');
            
            setTimeout(() => {
                renderBannerPreview();
                icon.classList.remove('fa-spin');
            }, 500);
        }
        
        function renderBannerPreview() {
            const loadingEl = document.querySelector('.preview-loading');
            const contentEl = document.getElementById('bannerPreviewContent');
            
            // Coletar dados do formulário
            const formData = collectFormData();
            
            // Renderizar preview
            const previewHTML = generatePreviewHTML(formData);
            
            contentEl.innerHTML = previewHTML;
            loadingEl.style.display = 'none';
            contentEl.style.display = 'block';
        }
        
        function collectFormData() {
            return {
                background_image: getFieldValue('background_image'),
                background_color: getFieldValue('background_color') || '#1a202c',
                background_position: getFieldValue('background_position') || 'center center',
                background_size: getFieldValue('background_size') || 'cover',
                overlay_color: getFieldValue('overlay_color'),
                overlay_opacity: getFieldValue('overlay_opacity') || 0,
                banner_height: getFieldValue('banner_height') || 500,
                content_alignment: getFieldValue('content_alignment') || 'center',
                layers: getFieldValue('layers') || []
            };
        }
        
        function getFieldValue(fieldName) {
            // Tenta encontrar o campo no formulário Filament
            const field = document.querySelector(`[wire\\:model*="${fieldName}"], [name="${fieldName}"]`);
            if (!field) return null;
            
            if (field.type === 'checkbox') {
                return field.checked;
            }
            
            return field.value;
        }
        
        function generatePreviewHTML(data) {
            const height = data.banner_height;
            const bgImage = data.background_image ? `url('/storage/${data.background_image}')` : 'none';
            const bgColor = data.background_color;
            const bgPosition = data.background_position;
            const bgSize = data.background_size;
            const overlayColor = data.overlay_color;
            const overlayOpacity = data.overlay_opacity / 100;
            const alignment = data.content_alignment;
            
            let layersHTML = '';
            
            if (data.layers && Array.isArray(data.layers)) {
                data.layers.forEach(layer => {
                    layersHTML += renderLayer(layer);
                });
            }
            
            // Se não há layers, mostra mensagem
            if (!layersHTML) {
                layersHTML = `
                    <div style="text-align: center; padding: 3rem;">
                        <i class="fas fa-layer-group" style="font-size: 48px; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <p style="font-size: 18px; opacity: 0.7;">Adicione camadas na aba "Content Layers"</p>
                    </div>
                `;
            }
            
            return `
                <div class="banner-preview-wrapper" style="height: ${height}px;">
                    <div class="banner-preview-item" style="
                        background-image: ${bgImage};
                        background-color: ${bgColor};
                        background-position: ${bgPosition};
                        background-size: ${bgSize};
                        align-items: ${alignment};
                    ">
                        ${overlayColor && overlayOpacity > 0 ? `
                            <div class="banner-preview-overlay" style="
                                background-color: ${overlayColor};
                                opacity: ${overlayOpacity};
                            "></div>
                        ` : ''}
                        
                        <div class="banner-preview-content">
                            ${layersHTML}
                        </div>
                    </div>
                </div>
            `;
        }
        
        function renderLayer(layer) {
            if (!layer || !layer.type) return '';
            
            switch (layer.type) {
                case 'text':
                    return renderTextLayer(layer.data);
                case 'button':
                    return renderButtonLayer(layer.data);
                case 'image':
                    return renderImageLayer(layer.data);
                case 'badge':
                    return renderBadgeLayer(layer.data);
                case 'spacer':
                    return renderSpacerLayer(layer.data);
                default:
                    return '';
            }
        }
        
        function renderTextLayer(data) {
            const tag = data.tag || 'p';
            const content = data.content || '';
            const color = data.color || '#ffffff';
            const fontSize = data.font_size || 16;
            const fontWeight = data.font_weight || '400';
            const textAlign = data.text_align || 'center';
            const marginTop = data.margin_top || 0;
            const marginBottom = data.margin_bottom || 0;
            
            return `
                <div style="text-align: ${textAlign}; margin-top: ${marginTop}px; margin-bottom: ${marginBottom}px;">
                    <${tag} style="
                        color: ${color};
                        font-size: ${fontSize}px;
                        font-weight: ${fontWeight};
                        margin: 0;
                        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
                    ">
                        ${content}
                    </${tag}>
                </div>
            `;
        }
        
        function renderButtonLayer(data) {
            const text = data.text || 'Saiba Mais';
            const bgColor = data.bg_color || '#c41e3a';
            const textColor = data.text_color || '#ffffff';
            const size = data.size || 'md';
            const align = data.align || 'center';
            const borderRadius = data.border_radius || 5;
            const fullWidth = data.full_width || false;
            
            const sizeMap = {
                sm: '8px 20px',
                md: '12px 30px',
                lg: '16px 40px'
            };
            
            return `
                <div style="text-align: ${align}; margin-top: 1.5rem;">
                    <a href="#" style="
                        display: ${fullWidth ? 'block' : 'inline-block'};
                        background-color: ${bgColor};
                        color: ${textColor};
                        padding: ${sizeMap[size]};
                        border-radius: ${borderRadius}px;
                        text-decoration: none;
                        font-weight: 600;
                        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                        transition: all 0.3s ease;
                    ">
                        ${text}
                    </a>
                </div>
            `;
        }
        
        function renderImageLayer(data) {
            if (!data.image) return '';
            
            const width = data.width ? `${data.width}px` : 'auto';
            const height = data.height ? `${data.height}px` : 'auto';
            const align = data.align || 'center';
            
            return `
                <div style="text-align: ${align}; margin: 1rem 0;">
                    <img src="/storage/${data.image}" alt="Layer Image" style="
                        width: ${width};
                        height: ${height};
                        max-width: 100%;
                        height: auto;
                    ">
                </div>
            `;
        }
        
        function renderBadgeLayer(data) {
            const text = data.text || 'NOVO';
            const bgColor = data.bg_color || '#c41e3a';
            const textColor = data.text_color || '#ffffff';
            const align = data.align || 'center';
            const marginBottom = data.margin_bottom || 15;
            
            return `
                <div style="text-align: ${align}; margin-bottom: ${marginBottom}px;">
                    <span style="
                        display: inline-block;
                        background-color: ${bgColor};
                        color: ${textColor};
                        padding: 0.4rem 1rem;
                        border-radius: 20px;
                        font-size: 12px;
                        font-weight: 700;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                    ">
                        ${text}
                    </span>
                </div>
            `;
        }
        
        function renderSpacerLayer(data) {
            const height = data.height || 30;
            return `<div style="height: ${height}px;"></div>`;
        }
        
        // Auto-refresh quando campos mudarem (debounced)
        document.addEventListener('DOMContentLoaded', function() {
            // Renderiza preview inicial
            setTimeout(renderBannerPreview, 1000);
            
            // Observa mudanças no formulário
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('input', function() {
                    clearTimeout(previewRefreshTimeout);
                    previewRefreshTimeout = setTimeout(renderBannerPreview, 1000);
                });
                
                form.addEventListener('change', function() {
                    clearTimeout(previewRefreshTimeout);
                    previewRefreshTimeout = setTimeout(renderBannerPreview, 500);
                });
            }
        });
        
        // Refresh quando Livewire atualizar
        document.addEventListener('livewire:load', function() {
            Livewire.hook('message.processed', () => {
                clearTimeout(previewRefreshTimeout);
                previewRefreshTimeout = setTimeout(renderBannerPreview, 500);
            });
        });
    </script>
</div>
