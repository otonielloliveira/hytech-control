<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assinaturas da Petição: {{ $post->title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #d32f2f;
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        
        .header h2 {
            color: #666;
            margin: 10px 0 0 0;
            font-size: 14px;
            font-weight: normal;
        }
        
        .petition-info {
            background-color: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .petition-info h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 14px;
        }
        
        .petition-info p {
            margin: 5px 0;
            font-size: 11px;
        }
        
        .stats {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #e8f5e8;
            border-radius: 5px;
        }
        
        .stats h3 {
            color: #2e7d32;
            margin: 0;
            font-size: 16px;
        }
        
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .signatures-table th {
            background-color: #d32f2f;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }
        
        .signatures-table td {
            padding: 6px 5px;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
        }
        
        .signatures-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .signatures-table tr:hover {
            background-color: #f0f0f0;
        }
        
        .whatsapp {
            color: #25d366;
            font-weight: bold;
        }
        
        .email {
            color: #1976d2;
        }
        
        .location {
            color: #666;
            font-style: italic;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .state-group {
            margin-bottom: 20px;
        }
        
        .state-header {
            background-color: #2196f3;
            color: white;
            padding: 8px;
            font-weight: bold;
            font-size: 12px;
        }
        
        .signature-count {
            background-color: #fff3e0;
            padding: 5px 10px;
            margin: 5px 0;
            border-left: 4px solid #ff9800;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LISTA DE ASSINATURAS DA PETIÇÃO</h1>
        <h2>{{ $post->title }}</h2>
    </div>
    
    <div class="petition-info">
        <h3>Informações da Petição</h3>
        <p><strong>Título:</strong> {{ $post->title }}</p>
        <p><strong>Categoria:</strong> {{ $post->category->name ?? 'Não informada' }}</p>
        <p><strong>Autor:</strong> {{ $post->user->name ?? 'Não informado' }}</p>
        <p><strong>Data de Publicação:</strong> {{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : 'Não publicada' }}</p>
        <p><strong>Status:</strong> {{ ucfirst($post->status) }}</p>
        @if($post->excerpt)
            <p><strong>Resumo:</strong> {{ $post->excerpt }}</p>
        @endif
    </div>
    
    <div class="stats">
        <h3>Total de Assinaturas: {{ $total }}</h3>
        <p>Documento gerado em {{ now()->format('d/m/Y \à\s H:i') }}</p>
    </div>
    
    @if($signatures->count() > 0)
        @php
            $groupedByState = $signatures->groupBy('estado');
        @endphp
        
        @foreach($groupedByState as $estado => $stateSignatures)
            <div class="state-group">
                <div class="state-header">
                    {{ $estado }} - {{ $stateSignatures->count() }} assinatura{{ $stateSignatures->count() > 1 ? 's' : '' }}
                </div>
                
                <table class="signatures-table">
                    <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 25%">Nome</th>
                            <th style="width: 25%">Email</th>
                            <th style="width: 15%">WhatsApp</th>
                            <th style="width: 15%">Cidade</th>
                            <th style="width: 15%">Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stateSignatures as $index => $signature)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $signature->nome }}</strong></td>
                                <td class="email">{{ $signature->email }}</td>
                                <td class="whatsapp">
                                    @php
                                        $phone = preg_replace('/\D/', '', $signature->tel_whatsapp);
                                        if (strlen($phone) === 11) {
                                            echo preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
                                        } else {
                                            echo $signature->tel_whatsapp;
                                        }
                                    @endphp
                                </td>
                                <td class="location">{{ $signature->cidade }}</td>
                                <td>{{ $signature->signed_at ? $signature->signed_at->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                            @if($signature->observacao)
                                <tr>
                                    <td></td>
                                    <td colspan="5" style="font-style: italic; color: #666; font-size: 8px; padding-left: 15px;">
                                        <strong>Obs:</strong> {{ $signature->observacao }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Só quebra página se há mais de 20 assinaturas no estado E não é o último --}}
            @if($stateSignatures->count() > 20 && !$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
        
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p>Nenhuma assinatura encontrada para esta petição.</p>
        </div>
    @endif
    
    <div class="footer">
        <p>
            <strong>Relatório gerado automaticamente pelo sistema</strong><br>
            Data/Hora: {{ now()->format('d/m/Y H:i:s') }}<br>
            Total de registros: {{ $total }}
        </p>
        
        @if($signatures->where('link_facebook', '!=', null)->count() > 0 || $signatures->where('link_instagram', '!=', null)->count() > 0)
            <p style="margin-top: 15px;">
                <strong>Estatísticas de Redes Sociais:</strong><br>
                Facebook: {{ $signatures->where('link_facebook', '!=', null)->count() }} assinantes<br>
                Instagram: {{ $signatures->where('link_instagram', '!=', null)->count() }} assinantes
            </p>
        @endif
    </div>
</body>
</html>