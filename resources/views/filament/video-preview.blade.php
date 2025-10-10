<div class="video-preview">
    <div class="video-header mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ $video->title }}
        </h3>
        @if($video->description)
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                {{ $video->description }}
            </p>
        @endif
    </div>
    
    <div class="video-container mb-4">
        @if($video->embed_url)
            <div class="aspect-video bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden">
                <iframe 
                    src="{{ $video->embed_url }}" 
                    class="w-full h-full" 
                    frameborder="0" 
                    allowfullscreen
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                </iframe>
            </div>
        @else
            <div class="aspect-video bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Preview não disponível</p>
                </div>
            </div>
        @endif
    </div>
    
    <div class="video-meta grid grid-cols-2 gap-4 text-sm">
        <div>
            <span class="font-medium text-gray-700 dark:text-gray-300">Plataforma:</span>
            <span class="ml-1 capitalize">{{ $video->video_platform }}</span>
        </div>
        
        @if($video->duration)
        <div>
            <span class="font-medium text-gray-700 dark:text-gray-300">Duração:</span>
            <span class="ml-1">{{ $video->duration }}</span>
        </div>
        @endif
        
        @if($video->category)
        <div>
            <span class="font-medium text-gray-700 dark:text-gray-300">Categoria:</span>
            <span class="ml-1 capitalize">{{ $video->category }}</span>
        </div>
        @endif
        
        <div>
            <span class="font-medium text-gray-700 dark:text-gray-300">Visualizações:</span>
            <span class="ml-1">{{ number_format($video->views_count) }}</span>
        </div>
        
        @if($video->published_date)
        <div>
            <span class="font-medium text-gray-700 dark:text-gray-300">Publicado:</span>
            <span class="ml-1">{{ $video->formatted_published_date }}</span>
        </div>
        @endif
        
        <div>
            <span class="font-medium text-gray-700 dark:text-gray-300">Status:</span>
            <span class="ml-1">
                @if($video->is_active)
                    <span class="text-green-600 dark:text-green-400">Ativo</span>
                @else
                    <span class="text-red-600 dark:text-red-400">Inativo</span>
                @endif
            </span>
        </div>
    </div>
    
    @if($video->tags && count($video->tags) > 0)
    <div class="video-tags mt-4">
        <span class="font-medium text-gray-700 dark:text-gray-300 block mb-2">Tags:</span>
        <div class="flex flex-wrap gap-1">
            @foreach($video->tags as $tag)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    {{ $tag }}
                </span>
            @endforeach
        </div>
    </div>
    @endif
</div>