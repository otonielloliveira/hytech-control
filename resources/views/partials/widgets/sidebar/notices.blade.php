<div class="notices-widget">
    @php
        // Notices simples até integrar com sistema completo
        $notices = [
            [
                'title' => 'Bem-vindo!',
                'content' => 'Explore nosso conteúdo e fique por dentro das novidades.',
                'type' => 'info'
            ],
            [
                'title' => 'Álbuns de Fotos',
                'content' => 'Confira nossa nova galeria de fotos dos eventos!',
                'type' => 'success'
            ],
            [
                'title' => 'Vídeos',
                'content' => 'Assista aos nossos vídeos e conteúdos exclusivos.',
                'type' => 'primary'
            ]
        ];
    @endphp
    
    @foreach($notices as $notice)
        <div class="alert alert-{{ $notice['type'] }} alert-sm mb-2 p-2">
            <strong class="small">{{ $notice['title'] }}</strong><br>
            <span class="small">{{ $notice['content'] }}</span>
        </div>
    @endforeach
</div>