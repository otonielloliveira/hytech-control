<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-6 bg-white rounded-lg shadow-sm border">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">🎥 Como Adicionar Vídeos aos Posts</h2>
            
            <div class="space-y-6">
                <!-- Método 1: Vídeo Principal -->
                <div class="border-l-4 border-blue-500 pl-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">1. Vídeo Principal do Post</h3>
                    <p class="text-gray-600 mb-3">Configure um vídeo principal na aba "Conteúdo" > seção "Mídia":</p>
                    <ul class="list-disc pl-6 space-y-1 text-gray-600">
                        <li><strong>Tipo de Vídeo:</strong> Escolha YouTube, Vimeo ou Código Personalizado</li>
                        <li><strong>URL do Vídeo:</strong> Cole a URL completa (ex: https://www.youtube.com/watch?v=ABC123)</li>
                        <li><strong>Exibir no conteúdo:</strong> Ative para mostrar automaticamente no início do post</li>
                    </ul>
                </div>

                <!-- Método 2: Shortcodes -->
                <div class="border-l-4 border-green-500 pl-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">2. Vídeos no Conteúdo (Shortcodes)</h3>
                    <p class="text-gray-600 mb-3">Use shortcodes para inserir vídeos em qualquer lugar do conteúdo:</p>
                    
                    <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                        <div>
                            <strong class="text-blue-600">[video url="URL_COMPLETA"]</strong>
                            <p class="text-sm text-gray-600">Para qualquer URL do YouTube ou Vimeo</p>
                            <code class="text-xs bg-gray-200 px-2 py-1 rounded">
                                [video url="https://www.youtube.com/watch?v=dQw4w9WgXcQ"]
                            </code>
                        </div>
                        
                        <div>
                            <strong class="text-red-600">[youtube id="VIDEO_ID"]</strong>
                            <p class="text-sm text-gray-600">Para YouTube usando apenas o ID do vídeo</p>
                            <code class="text-xs bg-gray-200 px-2 py-1 rounded">
                                [youtube id="dQw4w9WgXcQ"]
                            </code>
                        </div>
                        
                        <div>
                            <strong class="text-purple-600">[vimeo id="VIDEO_ID"]</strong>
                            <p class="text-sm text-gray-600">Para Vimeo usando apenas o ID do vídeo</p>
                            <code class="text-xs bg-gray-200 px-2 py-1 rounded">
                                [vimeo id="123456789"]
                            </code>
                        </div>
                    </div>
                </div>

                <!-- Exemplos -->
                <div class="border-l-4 border-yellow-500 pl-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">3. Exemplos Práticos</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-600 mb-2"><strong>No conteúdo do post, você pode escrever:</strong></p>
                        <pre class="text-sm bg-white p-3 rounded border overflow-x-auto"><code>&lt;p&gt;Assista ao vídeo tutorial:&lt;/p&gt;

[youtube id="dQw4w9WgXcQ"]

&lt;p&gt;E também este outro exemplo:&lt;/p&gt;

[video url="https://vimeo.com/123456789"]

&lt;p&gt;Os vídeos serão exibidos automaticamente!&lt;/p&gt;</code></pre>
                    </div>
                </div>

                <!-- Dicas -->
                <div class="border-l-4 border-indigo-500 pl-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">💡 Dicas Importantes</h3>
                    <ul class="list-disc pl-6 space-y-2 text-gray-600">
                        <li><strong>Responsivo:</strong> Todos os vídeos são automaticamente responsivos</li>
                        <li><strong>YouTube ID:</strong> Copie apenas a parte após "v=" na URL</li>
                        <li><strong>Vimeo ID:</strong> Copie apenas os números da URL</li>
                        <li><strong>Posicionamento:</strong> Os shortcodes funcionam em qualquer lugar do conteúdo</li>
                        <li><strong>Múltiplos vídeos:</strong> Você pode usar quantos shortcodes quiser no mesmo post</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
