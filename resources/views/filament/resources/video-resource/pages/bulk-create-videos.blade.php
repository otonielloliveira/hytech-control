<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header com instruções -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        Como usar o upload em lote
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Cole os links completos dos vídeos (YouTube, Vimeo, etc.)</li>
                            <li>O sistema extrairá automaticamente as informações básicas</li>
                            <li>Preencha títulos e categorias para cada vídeo</li>
                            <li>Use as configurações globais para aplicar valores padrão</li>
                            <li>Clique em "Adicionar Outro Vídeo" para incluir mais vídeos</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulário -->
        <form wire:submit.prevent="create">
            {{ $this->form }}
            
            <div class="flex justify-end space-x-2 pt-6">
                {{ $this->getFormActions() }}
            </div>
        </form>
    </div>

    <style>
        .fi-fo-repeater-item {
            border: 1px solid rgb(209 213 219);
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .dark .fi-fo-repeater-item {
            border-color: rgb(55 65 81);
        }
        
        .fi-fo-repeater-item:hover {
            border-color: rgb(99 102 241);
        }
        
        .dark .fi-fo-repeater-item:hover {
            border-color: rgb(129 140 248);
        }
    </style>
</x-filament-panels::page>