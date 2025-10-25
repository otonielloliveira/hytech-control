<div class="fi-global-search-field" x-data="{ open: false }">
    <!-- Campo de busca -->
    <div class="relative">
        <input 
            type="text" 
            wire:model.live.debounce.300ms="search"
            x-on:focus="open = true"
            x-on:keydown.escape="open = false"
            placeholder=" Buscar no menu... (Ctrl+K)"
            class="w-full px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
        />
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        
        @if(strlen($search) > 0)
            <button 
                wire:click="$set('search', '')"
                x-on:click="open = false"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        @endif
    </div>

    <!-- Resultados -->
    @if(strlen($search) >= 2 && count($results) > 0)
        <div 
            x-show="open"
            x-on:click.away="open = false"
            x-transition
            class="absolute z-50 w-full mt-2 bg-white rounded-lg shadow-xl dark:bg-gray-800 max-h-96 overflow-y-auto"
        >
            <div class="p-2">
                <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">
                    {{ count($results) }} {{ count($results) === 1 ? 'resultado encontrado' : 'resultados encontrados' }}
                </div>
                
                @php
                    $groupedResults = collect($results)->groupBy('group');
                @endphp
                
                @foreach($groupedResults as $group => $items)
                    @if($group)
                        <div class="px-3 py-1 mt-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
                            📁 {{ $group }}
                        </div>
                    @endif
                    
                    @foreach($items as $result)
                        <a 
                            href="{{ $result['url'] }}"
                            x-on:click="open = false"
                            wire:click="$set('search', '')"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group"
                        >
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-900 dark:text-primary-400 group-hover:scale-110 transition-transform">
                                @if($result['icon'])
                                    <x-filament::icon 
                                        :icon="$result['icon']"
                                        class="w-5 h-5"
                                    />
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                @endif
                            </div>
                            
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                    {{ $result['label'] }}
                                </div>
                                @if(!$group && isset($result['group']))
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $result['group'] }}
                                    </div>
                                @endif
                            </div>
                            
                            @if($result['badge'])
                                <span class="px-2 py-1 text-xs font-semibold text-white bg-primary-600 rounded-full">
                                    {{ $result['badge'] }}
                                </span>
                            @endif
                            
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @endforeach
                @endforeach
            </div>
        </div>
    @elseif(strlen($search) >= 2 && count($results) === 0)
        <div 
            x-show="open"
            x-on:click.away="open = false"
            x-transition
            class="absolute z-50 w-full mt-2 bg-white rounded-lg shadow-xl dark:bg-gray-800"
        >
            <div class="p-6 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Nenhum resultado encontrado para <strong>"{{ $search }}"</strong>
                </p>
            </div>
        </div>
    @endif
    
    <script>
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                document.querySelector('[wire\\:model\\.live\\.debounce\\.300ms="search"]')?.focus();
            }
        });
    </script>

    <style>
        .fi-global-search-field {
            position: relative;
            width: 100%;
            max-width: 400px;
        }
    </style>
</div>
