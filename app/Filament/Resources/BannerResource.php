<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Filament\Resources\BannerResource\RelationManagers;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    
    protected static ?string $navigationLabel = 'Banners';
    
    protected static ?string $modelLabel = 'Banner';
    
    protected static ?string $pluralModelLabel = 'Banners';
    
    protected static ?string $navigationGroup = 'Blog';
    
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('📚 Guia Rápido')
                    ->description('Sistema de criação de banners com camadas (layers) estilo WordPress. Crie banners profissionais com textos, imagens, botões e mais!')
                    ->schema([
                        Forms\Components\Placeholder::make('help')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div style="line-height: 1.8;">
                                    <h4 style="margin-bottom: 1rem; color: #c41e3a;">
                                        <strong>🎨 Como usar o Editor de Banners:</strong>
                                    </h4>
                                    <ol style="margin-left: 1.5rem;">
                                        <li><strong>Background & Layout:</strong> Configure a imagem de fundo, cores, altura e overlay</li>
                                        <li><strong>Content Layers:</strong> Adicione camadas de conteúdo (textos, botões, imagens, badges)</li>
                                        <li><strong>Configurações:</strong> Defina título interno, status e ordem de exibição</li>
                                    </ol>
                                    <h4 style="margin: 1.5rem 0 1rem; color: #c41e3a;">
                                        <strong>💡 Dicas:</strong>
                                    </h4>
                                    <ul style="margin-left: 1.5rem;">
                                        <li>Use os <strong>templates prontos</strong> acima para começar rapidamente</li>
                                        <li><strong>Arraste as camadas</strong> para reordenar (drag & drop)</li>
                                        <li>Use <strong>Espaçadores</strong> para criar distância entre elementos</li>
                                        <li>Recomendação: Imagens de fundo com <strong>1920x800px</strong></li>
                                        <li>Altura ideal do banner: <strong>400-600px</strong> para desktop</li>
                                        <li>O <strong>preview ao vivo</strong> atualiza automaticamente!</li>
                                    </ul>
                                </div>
                            ')),
                    ])
                    ->collapsed()
                    ->persistCollapsed()
                    ->columnSpanFull(),
                
                Forms\Components\Tabs::make('Banner Editor')
                    ->tabs([
                        // Tab 1: Background & Layout
                        Forms\Components\Tabs\Tab::make('🎨 Background & Layout')
                            ->schema([
                                Forms\Components\Section::make('Imagem de Fundo')
                                    ->schema([
                                        Forms\Components\FileUpload::make('background_image')
                                            ->label('Imagem de Fundo')
                                            ->image()
                                            ->imageEditor()
                                            ->directory('blog/banners/backgrounds')
                                            ->visibility('public')
                                            ->columnSpanFull()
                                            ->helperText('Recomendado: 1920x800px'),
                                        
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\ColorPicker::make('background_color')
                                                    ->label('Cor de Fundo')
                                                    ->helperText('Exibida quando não houver imagem'),
                                                
                                                Forms\Components\Select::make('background_position')
                                                    ->label('Posição do Fundo')
                                                    ->options([
                                                        'top left' => 'Superior Esquerda',
                                                        'top center' => 'Superior Centro',
                                                        'top right' => 'Superior Direita',
                                                        'center left' => 'Centro Esquerda',
                                                        'center center' => 'Centro',
                                                        'center right' => 'Centro Direita',
                                                        'bottom left' => 'Inferior Esquerda',
                                                        'bottom center' => 'Inferior Centro',
                                                        'bottom right' => 'Inferior Direita',
                                                    ])
                                                    ->default('center center'),
                                                
                                                Forms\Components\Select::make('background_size')
                                                    ->label('Tamanho do Fundo')
                                                    ->options([
                                                        'cover' => 'Cobrir (Cover)',
                                                        'contain' => 'Conter (Contain)',
                                                        'auto' => 'Automático',
                                                        '100% 100%' => 'Esticar (100%)',
                                                    ])
                                                    ->default('cover'),
                                            ]),
                                    ]),
                                
                                Forms\Components\Section::make('Overlay')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\ColorPicker::make('overlay_color')
                                                    ->label('Cor do Overlay')
                                                    ->helperText('Sobreposição de cor sobre a imagem'),
                                                
                                                Forms\Components\TextInput::make('overlay_opacity')
                                                    ->label('Opacidade do Overlay (%)')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->maxValue(100)
                                                    ->default(0)
                                                    ->suffix('%'),
                                            ]),
                                    ])
                                    ->collapsed(),
                                
                                Forms\Components\Section::make('Dimensões e Alinhamento')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('banner_height')
                                                    ->label('Altura do Banner (px)')
                                                    ->numeric()
                                                    ->default(500)
                                                    ->suffix('px')
                                                    ->helperText('Desktop: 400-600px recomendado'),
                                                
                                                Forms\Components\Select::make('content_alignment')
                                                    ->label('Alinhamento do Conteúdo')
                                                    ->options([
                                                        'flex-start' => 'Topo',
                                                        'center' => 'Centro',
                                                        'flex-end' => 'Base',
                                                    ])
                                                    ->default('center'),
                                            ]),
                                    ]),
                            ]),
                        
                        // Tab 2: Content Layers
                        Forms\Components\Tabs\Tab::make('📝 Content Layers')
                            ->schema([
                                Forms\Components\Section::make('🎭 Preview em Tempo Real')
                                    ->description('Visualize como seu banner ficará antes de salvar!')
                                    ->schema([
                                        Forms\Components\Placeholder::make('preview_placeholder')
                                            ->label('')
                                            ->content(new \Illuminate\Support\HtmlString('
                                                <div style="background: #f0fdf4; border: 2px dashed #10b981; border-radius: 8px; padding: 1.5rem; text-align: center;">
                                                    <p style="margin: 0; color: #059669; font-weight: 600;">
                                                        <i class="fas fa-magic"></i> 
                                                        O preview acima atualiza automaticamente conforme você edita!
                                                    </p>
                                                    <p style="margin: 0.5rem 0 0; color: #047857; font-size: 13px;">
                                                        Arraste as camadas abaixo para reordenar. A ordem define como aparecem no banner.
                                                    </p>
                                                </div>
                                            ')),
                                    ])
                                    ->collapsed(),
                                
                                Forms\Components\Section::make('Camadas de Conteúdo')
                                    ->description('🎨 Adicione textos, imagens, botões e mais. ⬆️⬇️ Arraste para reordenar.')
                                    ->schema([
                                        Forms\Components\Builder::make('layers')
                                            ->label('')
                                            ->blocks([
                                                // Text Block
                                                Forms\Components\Builder\Block::make('text')
                                                    ->label('📄 Texto')
                                                    ->icon('heroicon-o-document-text')
                                                    ->schema([
                                                        Forms\Components\RichEditor::make('content')
                                                            ->label('Conteúdo')
                                                            ->required()
                                                            ->columnSpanFull(),
                                                        
                                                        Forms\Components\Grid::make(4)
                                                            ->schema([
                                                                Forms\Components\Select::make('tag')
                                                                    ->label('Tag HTML')
                                                                    ->options([
                                                                        'h1' => 'Título H1',
                                                                        'h2' => 'Título H2',
                                                                        'h3' => 'Título H3',
                                                                        'h4' => 'Título H4',
                                                                        'p' => 'Parágrafo',
                                                                        'span' => 'Span',
                                                                    ])
                                                                    ->default('p'),
                                                                
                                                                Forms\Components\ColorPicker::make('color')
                                                                    ->label('Cor do Texto'),
                                                                
                                                                Forms\Components\TextInput::make('font_size')
                                                                    ->label('Tamanho da Fonte')
                                                                    ->numeric()
                                                                    ->suffix('px')
                                                                    ->default(16),
                                                                
                                                                Forms\Components\Select::make('font_weight')
                                                                    ->label('Peso da Fonte')
                                                                    ->options([
                                                                        '300' => 'Leve (300)',
                                                                        '400' => 'Normal (400)',
                                                                        '500' => 'Médio (500)',
                                                                        '600' => 'Semi-Bold (600)',
                                                                        '700' => 'Bold (700)',
                                                                        '800' => 'Extra-Bold (800)',
                                                                    ])
                                                                    ->default('400'),
                                                            ]),
                                                        
                                                        Forms\Components\Grid::make(3)
                                                            ->schema([
                                                                Forms\Components\Select::make('text_align')
                                                                    ->label('Alinhamento')
                                                                    ->options([
                                                                        'left' => 'Esquerda',
                                                                        'center' => 'Centro',
                                                                        'right' => 'Direita',
                                                                    ])
                                                                    ->default('center'),
                                                                
                                                                Forms\Components\TextInput::make('margin_top')
                                                                    ->label('Margem Superior')
                                                                    ->numeric()
                                                                    ->default(0)
                                                                    ->suffix('px'),
                                                                
                                                                Forms\Components\TextInput::make('margin_bottom')
                                                                    ->label('Margem Inferior')
                                                                    ->numeric()
                                                                    ->default(0)
                                                                    ->suffix('px'),
                                                            ]),
                                                    ]),
                                                
                                                // Button Block
                                                Forms\Components\Builder\Block::make('button')
                                                    ->label('🔘 Botão')
                                                    ->icon('heroicon-o-cursor-arrow-rays')
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('text')
                                                                    ->label('Texto do Botão')
                                                                    ->required()
                                                                    ->default('Saiba Mais'),
                                                                
                                                                Forms\Components\TextInput::make('url')
                                                                    ->label('URL')
                                                                    ->url()
                                                                    ->required(),
                                                            ]),
                                                        
                                                        Forms\Components\Grid::make(4)
                                                            ->schema([
                                                                Forms\Components\ColorPicker::make('bg_color')
                                                                    ->label('Cor de Fundo')
                                                                    ->default('#c41e3a'),
                                                                
                                                                Forms\Components\ColorPicker::make('text_color')
                                                                    ->label('Cor do Texto')
                                                                    ->default('#ffffff'),
                                                                
                                                                Forms\Components\Select::make('size')
                                                                    ->label('Tamanho')
                                                                    ->options([
                                                                        'sm' => 'Pequeno',
                                                                        'md' => 'Médio',
                                                                        'lg' => 'Grande',
                                                                    ])
                                                                    ->default('md'),
                                                                
                                                                Forms\Components\Select::make('align')
                                                                    ->label('Alinhamento')
                                                                    ->options([
                                                                        'left' => 'Esquerda',
                                                                        'center' => 'Centro',
                                                                        'right' => 'Direita',
                                                                    ])
                                                                    ->default('center'),
                                                            ]),
                                                        
                                                        Forms\Components\Grid::make(3)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('border_radius')
                                                                    ->label('Borda Arredondada')
                                                                    ->numeric()
                                                                    ->default(5)
                                                                    ->suffix('px'),
                                                                
                                                                Forms\Components\Toggle::make('target_blank')
                                                                    ->label('Abrir em Nova Aba')
                                                                    ->default(false),
                                                                
                                                                Forms\Components\Toggle::make('full_width')
                                                                    ->label('Largura Total')
                                                                    ->default(false),
                                                            ]),
                                                    ]),
                                                
                                                // Image Block
                                                Forms\Components\Builder\Block::make('image')
                                                    ->label('🖼️ Imagem')
                                                    ->icon('heroicon-o-photo')
                                                    ->schema([
                                                        Forms\Components\FileUpload::make('image')
                                                            ->label('Imagem')
                                                            ->image()
                                                            ->imageEditor()
                                                            ->directory('blog/banners/layers')
                                                            ->visibility('public')
                                                            ->required()
                                                            ->columnSpanFull(),
                                                        
                                                        Forms\Components\Grid::make(3)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('width')
                                                                    ->label('Largura')
                                                                    ->numeric()
                                                                    ->suffix('px')
                                                                    ->helperText('Deixe vazio para automático'),
                                                                
                                                                Forms\Components\TextInput::make('height')
                                                                    ->label('Altura')
                                                                    ->numeric()
                                                                    ->suffix('px')
                                                                    ->helperText('Deixe vazio para automático'),
                                                                
                                                                Forms\Components\Select::make('align')
                                                                    ->label('Alinhamento')
                                                                    ->options([
                                                                        'left' => 'Esquerda',
                                                                        'center' => 'Centro',
                                                                        'right' => 'Direita',
                                                                    ])
                                                                    ->default('center'),
                                                            ]),
                                                    ]),
                                                
                                                // Spacer Block
                                                Forms\Components\Builder\Block::make('spacer')
                                                    ->label('↕️ Espaçador')
                                                    ->icon('heroicon-o-arrows-up-down')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('height')
                                                            ->label('Altura do Espaço')
                                                            ->numeric()
                                                            ->default(30)
                                                            ->suffix('px')
                                                            ->helperText('Cria um espaço vertical entre elementos'),
                                                    ]),
                                                
                                                // Badge Block
                                                Forms\Components\Builder\Block::make('badge')
                                                    ->label('🏷️ Badge/Tag')
                                                    ->icon('heroicon-o-tag')
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('text')
                                                                    ->label('Texto')
                                                                    ->required()
                                                                    ->default('NOVO'),
                                                                
                                                                Forms\Components\ColorPicker::make('bg_color')
                                                                    ->label('Cor de Fundo')
                                                                    ->default('#c41e3a'),
                                                            ]),
                                                        
                                                        Forms\Components\Grid::make(3)
                                                            ->schema([
                                                                Forms\Components\ColorPicker::make('text_color')
                                                                    ->label('Cor do Texto')
                                                                    ->default('#ffffff'),
                                                                
                                                                Forms\Components\Select::make('align')
                                                                    ->label('Alinhamento')
                                                                    ->options([
                                                                        'left' => 'Esquerda',
                                                                        'center' => 'Centro',
                                                                        'right' => 'Direita',
                                                                    ])
                                                                    ->default('center'),
                                                                
                                                                Forms\Components\TextInput::make('margin_bottom')
                                                                    ->label('Margem Inferior')
                                                                    ->numeric()
                                                                    ->default(15)
                                                                    ->suffix('px'),
                                                            ]),
                                                    ]),
                                            ])
                                            ->collapsible()
                                            ->cloneable()
                                            ->reorderable()
                                            ->blockNumbers(false)
                                            ->addActionLabel('➕ Adicionar Elemento')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        
                        // Tab 3: Settings
                        Forms\Components\Tabs\Tab::make('⚙️ Configurações')
                            ->schema([
                                Forms\Components\Section::make('Informações Básicas')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Título do Banner')
                                            ->required()
                                            ->helperText('Apenas para identificação administrativa')
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\Textarea::make('description')
                                            ->label('Descrição Interna')
                                            ->rows(2)
                                            ->helperText('Descrição para uso interno')
                                            ->columnSpanFull(),
                                    ]),
                                
                                Forms\Components\Section::make('Status e Ordem')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Toggle::make('is_active')
                                                    ->label('Banner Ativo')
                                                    ->default(true)
                                                    ->helperText('Desative para ocultar temporariamente'),
                                                
                                                Forms\Components\TextInput::make('sort_order')
                                                    ->label('Ordem de Exibição')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->helperText('Menor número = maior prioridade'),
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagem')
                    ->size(80)
                    ->defaultImageUrl(asset('images/default-no-image.png')),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                
                Tables\Columns\TextColumn::make('subtitle')
                    ->label('Subtítulo')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('link_type')
                    ->label('Tipo de Link')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'url' => 'success',
                        'post' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'url' => 'URL Externa',
                        'post' => 'Post do Blog',
                    }),
                
                Tables\Columns\TextColumn::make('post.title')
                    ->label('Post Vinculado')
                    ->limit(30)
                    ->placeholder('—'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->sortable()
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->label('Apenas Ativos')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
                
                Tables\Filters\SelectFilter::make('link_type')
                    ->label('Tipo de Link')
                    ->options([
                        'url' => 'URL Externa',
                        'post' => 'Post do Blog',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order');
    }

    
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações do Banner')
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\TextEntry::make('title')
                                        ->label('Título')
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                        ->weight('bold')
                                        ->columnSpanFull(),
                                    
                                    Infolists\Components\TextEntry::make('type')
                                        ->label('Tipo')
                                        ->badge()
                                        ->color('primary'),
                                    
                                    Infolists\Components\IconEntry::make('is_active')
                                        ->label('Ativo')
                                        ->boolean()
                                        ->trueIcon('heroicon-o-check-circle')
                                        ->falseIcon('heroicon-o-x-mark')
                                        ->trueColor('success')
                                        ->falseColor('danger'),
                                    
                                    Infolists\Components\TextEntry::make('priority')
                                        ->label('Prioridade')
                                        ->numeric()
                                        ->badge()
                                        ->color(fn ($state) => match (true) {
                                            $state >= 80 => 'success',
                                            $state >= 50 => 'warning',
                                            default => 'danger',
                                        }),
                                ]),
                            
                            Infolists\Components\ImageEntry::make('image')
                                ->label('Imagem')
                                ->size(200)
                                ->grow(false),
                        ])->from('lg'),
                    ]),
                
                Infolists\Components\Section::make('Conteúdo')
                    ->schema([
                        Infolists\Components\TextEntry::make('content')
                            ->label('')
                            ->html()
                            ->placeholder('Nenhum conteúdo fornecido'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'view' => Pages\ViewBanner::route('/{record}'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
