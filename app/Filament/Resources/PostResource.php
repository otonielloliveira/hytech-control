<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Filament\Support\Enums\FontWeight;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'Posts';
    
    protected static ?string $modelLabel = 'Post';
    
    protected static ?string $pluralModelLabel = 'Posts';
    
    protected static ?string $navigationGroup = 'Blog';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Post')
                    ->tabs([
                        Tabs\Tab::make('Conteúdo')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Informações Básicas')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Título')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (string $context, $state, callable $set) => $context === 'create' ? $set('slug', Str::slug($state)) : null)
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\TextInput::make('slug')
                                            ->label('Slug')
                                            ->required()
                                            ->unique(Post::class, 'slug', ignoreRecord: true)
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\Select::make('category_id')
                                            ->label('Categoria')
                                            ->relationship('category', 'name')
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nome')
                                                    ->required(),
                                                Forms\Components\TextInput::make('slug')
                                                    ->label('Slug')
                                                    ->required(),
                                                Forms\Components\Textarea::make('description')
                                                    ->label('Descrição'),
                                                Forms\Components\ColorPicker::make('color')
                                                    ->label('Cor')
                                                    ->default('#3B82F6'),
                                            ])
                                            ->searchable()
                                            ->preload(),
                                        
                                        Forms\Components\Select::make('user_id')
                                            ->label('Autor')
                                            ->relationship('user', 'name')
                                            ->default(auth()->id())
                                            ->required()
                                            ->searchable()
                                            ->preload(),
                                    ])->columns(2),
                                
                                Section::make('Conteúdo do Post')
                                    ->schema([
                                        Forms\Components\Textarea::make('excerpt')
                                            ->label('Resumo')
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->hint('Se deixar vazio, será gerado automaticamente'),
                                        
                                        Forms\Components\RichEditor::make('content')
                                            ->label('Conteúdo')
                                            ->required()
                                            ->columnSpanFull()
                                            ->toolbarButtons([
                                                'attachFiles',
                                                'blockquote',
                                                'bold',
                                                'bulletList',
                                                'codeBlock',
                                                'h2',
                                                'h3',
                                                'italic',
                                                'link',
                                                'orderedList',
                                                'redo',
                                                'strike',
                                                'underline',
                                                'undo',
                                            ])
                                            ->helperText('💡 **Shortcodes de Vídeo**: 
• [video url="https://youtube.com/watch?v=ID"] - Para qualquer URL
• [youtube id="VIDEO_ID"] - YouTube direto  
• [vimeo id="VIDEO_ID"] - Vimeo direto'),
                                    ]),
                                
                                Section::make('Mídia')
                                    ->schema([
                                        Forms\Components\FileUpload::make('featured_image')
                                            ->label('Imagem Destacada')
                                            ->image()
                                            ->imageEditor()
                                            ->directory('blog/images')
                                            ->visibility('public')
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\Select::make('video_type')
                                            ->label('Tipo de Vídeo')
                                            ->options([
                                                'none' => 'Nenhum',
                                                'youtube' => 'YouTube',
                                                'vimeo' => 'Vimeo',
                                                'custom' => 'Código Personalizado',
                                            ])
                                            ->default('none')
                                            ->reactive(),
                                        
                                        Forms\Components\TextInput::make('video_url')
                                            ->label('URL do Vídeo')
                                            ->url()
                                            ->placeholder('https://www.youtube.com/watch?v=...')
                                            ->hidden(fn ($get) => $get('video_type') === 'none' || $get('video_type') === 'custom')
                                            ->helperText('Cole a URL completa do YouTube ou Vimeo'),
                                        
                                        Forms\Components\Textarea::make('video_embed_code')
                                            ->label('Código de Incorporação')
                                            ->rows(4)
                                            ->placeholder('<iframe src="..." ...></iframe>')
                                            ->hidden(fn ($get) => $get('video_type') !== 'custom')
                                            ->helperText('Cole o código iframe completo do vídeo'),
                                        
                                        Forms\Components\Toggle::make('show_video_in_content')
                                            ->label('Exibir vídeo no conteúdo')
                                            ->helperText('Se ativado, o vídeo será exibido automaticamente no início do conteúdo do post')
                                            ->hidden(fn ($get) => $get('video_type') === 'none'),
                                        
                                        Forms\Components\TagsInput::make('tags')
                                            ->label('Tags')
                                            ->placeholder('Digite as tags...')
                                            ->columnSpanFull(),
                                    ])->columns(2),
                            ]),
                        
                        Tabs\Tab::make('Configurações')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make('Status e Publicação')
                                    ->schema([
                                        Forms\Components\Select::make('status')
                                            ->label('Status')
                                            ->required()
                                            ->options([
                                                'draft' => 'Rascunho',
                                                'published' => 'Publicado',
                                                'scheduled' => 'Agendado',
                                                'archived' => 'Arquivado',
                                            ])
                                            ->default('draft')
                                            ->live(),
                                        
                                        Forms\Components\Select::make('destination')
                                            ->label('Destino da Postagem')
                                            ->required()
                                            ->options([
                                                'artigos' => 'Artigos - Página Principal',
                                                'peticoes' => 'Petições - Bloco especial',
                                                'ultimas_noticias' => 'Últimas Notícias - Destaque',
                                                'confira_mais_destaque_1' => 'Confira Mais Destaque 1',
                                                'ultimos_destaques' => 'Últimos Destaques',
                                                'faixa_titulo' => 'Faixa Título - Widget mais vista',
                                                'nos_apoiamos' => 'Nós Apoiamos',
                                                'amigos_parceiros' => 'Amigos e Parceiros - Footer'
                                            ])
                                            ->default('artigos')
                                            ->live()
                                            ->helperText('Define onde esta postagem será exibida no site'),
                                        
                                        Forms\Components\DateTimePicker::make('published_at')
                                            ->label('Data de Publicação')
                                            ->visible(fn (callable $get) => in_array($get('status'), ['published', 'scheduled']))
                                            ->default(now()),
                                        
                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('Post em Destaque')
                                            ->default(false),
                                        
                                        Forms\Components\TextInput::make('reading_time')
                                            ->label('Tempo de Leitura (minutos)')
                                            ->numeric()
                                            ->default(1)
                                            ->hint('Será calculado automaticamente se deixar vazio'),
                                    ])->columns(2),
                            ]),
                        
                        Tabs\Tab::make('Petições')
                            ->icon('heroicon-o-document-text')
                            ->visible(fn ($get) => $get('destination') === 'peticoes')
                            ->schema([
                                Section::make('Vídeos da Petição')
                                    ->description('Adicione URLs de vídeos relacionados à petição')
                                    ->schema([
                                        Forms\Components\Repeater::make('petition_videos')
                                            ->label('Vídeos')
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Título do Vídeo')
                                                    ->required(),
                                                Forms\Components\TextInput::make('url')
                                                    ->label('URL do Vídeo')
                                                    ->url()
                                                    ->required()
                                                    ->placeholder('https://www.youtube.com/watch?v=...'),
                                                Forms\Components\Textarea::make('description')
                                                    ->label('Descrição (opcional)')
                                                    ->rows(2),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(0)
                                            ->addActionLabel('Adicionar Vídeo')
                                            ->collapsible(),
                                    ]),
                                
                                Section::make('Grupos WhatsApp')
                                    ->description('Configure grupos WhatsApp por região')
                                    ->schema([
                                        Forms\Components\Repeater::make('whatsapp_groups')
                                            ->label('Grupos WhatsApp')
                                            ->schema([
                                                Forms\Components\TextInput::make('cidade')
                                                    ->label('Cidade')
                                                    ->required(),
                                                Forms\Components\TextInput::make('estado')
                                                    ->label('Estado')
                                                    ->required(),
                                                Forms\Components\TextInput::make('nome_grupo')
                                                    ->label('Nome do Grupo')
                                                    ->required(),
                                                Forms\Components\TextInput::make('link_grupo')
                                                    ->label('Link do WhatsApp')
                                                    ->url()
                                                    ->required()
                                                    ->placeholder('https://chat.whatsapp.com/...'),
                                                Forms\Components\Select::make('status')
                                                    ->label('Status')
                                                    ->options([
                                                        'ativo' => 'Ativo',
                                                        'inativo' => 'Inativo',
                                                        'cheio' => 'Grupo Cheio',
                                                    ])
                                                    ->default('ativo')
                                                    ->required(),
                                            ])
                                            ->columns(5)
                                            ->defaultItems(0)
                                            ->addActionLabel('Adicionar Grupo WhatsApp')
                                            ->collapsible(),
                                    ]),
                            ]),
                        
                        Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Section::make('Meta Tags')
                                    ->schema([
                                        Forms\Components\TextInput::make('meta_title')
                                            ->label('Título SEO')
                                            ->hint('Deixe vazio para usar o título do post')
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\Textarea::make('meta_description')
                                            ->label('Descrição SEO')
                                            ->rows(3)
                                            ->hint('Deixe vazio para usar o resumo do post')
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\TagsInput::make('meta_keywords')
                                            ->label('Palavras-chave SEO')
                                            ->placeholder('Digite as palavras-chave...')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Img')
                    ->size(40)
                    ->circular()
                    ->defaultImageUrl(asset('images/default-no-image.png')),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->title)
                    ->description(fn ($record) => Str::limit($record->excerpt ?? '', 60)),
                
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->badge()
                    ->color(fn ($record) => $record->category?->color ?? 'gray')
                    ->size('sm'),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Autor')
                    ->sortable()
                    ->searchable()
                    ->limit(15)
                    ->tooltip(fn ($record) => $record->user?->name),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->size('sm')
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'scheduled' => 'warning',
                        'archived' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Rascunho',
                        'published' => 'Publicado',
                        'scheduled' => 'Agendado',
                        'archived' => 'Arquivado',
                    }),
                
                Tables\Columns\TextColumn::make('destination')
                    ->label('Destino')
                    ->badge()
                    ->size('sm')
                    ->color(fn (string $state): string => match ($state) {
                        'artigos' => 'blue',
                        'peticoes' => 'red',
                        'ultimas_noticias' => 'green',
                        'confira_mais_destaque_1' => 'yellow',
                        'ultimos_destaques' => 'purple',
                        'faixa_titulo' => 'orange',
                        'nos_apoiamos' => 'pink',
                        'amigos_parceiros' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'artigos' => 'Artigos',
                        'peticoes' => 'Petições',
                        'ultimas_noticias' => 'Últimas Notícias',
                        'confira_mais_destaque_1' => 'Destaque 1',
                        'ultimos_destaques' => 'Últimos Destaques',
                        'faixa_titulo' => 'Faixa Título',
                        'nos_apoiamos' => 'Nós Apoiamos',
                        'amigos_parceiros' => 'Amigos/Parceiros',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('')
                    ->trueColor('warning')
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => number_format($state))
                    ->color('gray')
                    ->size('sm'),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicado')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—')
                    ->color('gray')
                    ->size('sm'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Rascunho',
                        'published' => 'Publicado',
                        'scheduled' => 'Agendado',
                        'archived' => 'Arquivado',
                    ]),
                
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoria')
                    ->relationship('category', 'name')
                    ->searchable(),
                
                Tables\Filters\Filter::make('is_featured')
                    ->label('Posts em Destaque')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\TrashedFilter::make(),
            ])
            ->recordAction(fn (Model $record): string => "view")
            ->recordUrl(fn (Model $record): string => static::getUrl('view', ['record' => $record]))
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->color('warning'),
                    Tables\Actions\Action::make('duplicate')
                        ->label('Duplicar')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->action(function ($record) {
                            $newPost = $record->replicate();
                            $newPost->title = $record->title . ' (Cópia)';
                            $newPost->slug = $record->slug . '-copia';
                            $newPost->status = 'draft';
                            $newPost->published_at = null;
                            $newPost->save();
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Post duplicado com sucesso!')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash'),
                    Tables\Actions\RestoreAction::make()
                        ->icon('heroicon-o-arrow-path'),
                ])
                ->tooltip('Ações')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
