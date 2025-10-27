<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
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
                                            ->relationship('user', 'name', fn (Builder $query) => $query->where('is_author', true)->orWhere('is_admin', true))
                                            ->default(function () {
                                                return Auth::id();
                                            })
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->disabled(fn () => !Auth::user()->is_admin && Auth::user()->can_edit_own_posts_only)
                                            ->dehydrated(),
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
                                            ->options(function () {
                                                return \App\Models\SectionConfig::active()
                                                    ->ordered()
                                                    ->pluck('section_name', 'section_key')
                                                    ->toArray();
                                            })
                                            ->searchable()
                                            ->live()
                                            ->helperText('Define em qual seção esta postagem será exibida no site'),
                                        
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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->size('sm')
                    ->alignCenter(),
                
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
                        'noticias_mundiais' => 'cyan',
                        'noticias_nacionais' => 'indigo',
                        'noticias_regionais' => 'violet',
                        'politica' => 'orange',
                        'economia' => 'emerald',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'artigos' => 'Artigos',
                        'peticoes' => 'Petições',
                        'ultimas_noticias' => 'Últimas Notícias',
                        'noticias_mundiais' => 'Notícias Mundiais',
                        'noticias_nacionais' => 'Notícias Nacionais',
                        'noticias_regionais' => 'Notícias Regionais',
                        'politica' => 'Política',
                        'economia' => 'Economia',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('petition_signatures_count')
                    ->label('Assinaturas')
                    ->counts('petitionSignatures')
                    ->alignCenter()
                    ->color('success')
                    ->visible(fn ($record) => $record && $record->destination === 'peticoes')
                    ->badge(),
                
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
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->visible(fn (Post $record) => Auth::user()->canEditPost($record)),
                Tables\Actions\Action::make('signatures')
                    ->label('Assinaturas')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->url(fn ($record) => static::getUrl('view', ['record' => $record, 'activeRelationManager' => 1]))
                    ->visible(fn ($record) => $record && $record->destination === 'peticoes'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->visible(fn (Post $record) => Auth::user()->canDeletePost($record)),
                    
                // Ações de status para posts
                Tables\Actions\Action::make('publish')
                    ->label('Publicar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (Post $record) => $record->update(['status' => 'published', 'published_at' => $record->published_at ?? now()]))
                    ->requiresConfirmation()
                    ->visible(fn (Post $record) => 
                        Auth::user()->canPublishPosts() && 
                        $record->status !== 'published'
                    ),
                    
                Tables\Actions\Action::make('unpublish')
                    ->label('Despublicar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->action(fn (Post $record) => $record->update(['status' => 'draft']))
                    ->requiresConfirmation()
                    ->visible(fn (Post $record) => 
                        Auth::user()->canPublishPosts() && 
                        $record->status === 'published'
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()->canDeletePosts()),
                    Tables\Actions\RestoreBulkAction::make()
                        ->visible(fn () => Auth::user()->canEditPosts()),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->visible(fn () => Auth::user()->is_admin),
                        
                    // Ações em massa para status
                    Tables\Actions\BulkAction::make('publish_selected')
                        ->label('Publicar Selecionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['status' => 'published', 'published_at' => $record->published_at ?? now()])))
                        ->requiresConfirmation()
                        ->visible(fn () => Auth::user()->canPublishPosts()),
                        
                    Tables\Actions\BulkAction::make('draft_selected')
                        ->label('Marcar como Rascunho')
                        ->icon('heroicon-o-document')
                        ->color('warning')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['status' => 'draft'])))
                        ->requiresConfirmation()
                        ->visible(fn () => Auth::user()->canPublishPosts()),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações do Post')
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\TextEntry::make('title')
                                        ->label('Título')
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                        ->weight('bold')
                                        ->columnSpanFull(),
                                    
                                    Infolists\Components\TextEntry::make('category.name')
                                        ->label('Categoria')
                                        ->badge()
                                        ->color('primary'),
                                    
                                    Infolists\Components\IconEntry::make('is_published')
                                        ->label('Publicado')
                                        ->boolean()
                                        ->trueIcon('heroicon-o-check-circle')
                                        ->falseIcon('heroicon-o-x-mark')
                                        ->trueColor('success')
                                        ->falseColor('danger'),
                                    
                                    Infolists\Components\TextEntry::make('created_at')
                                        ->label('Criado em')
                                        ->dateTime('d/m/Y H:i'),
                                    
                                    Infolists\Components\TextEntry::make('updated_at')
                                        ->label('Atualizado em')
                                        ->dateTime('d/m/Y H:i'),
                                ]),
                            
                            Infolists\Components\ImageEntry::make('featured_image')
                                ->label('Imagem Destacada')
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
                
                Infolists\Components\Section::make('Resumo')
                    ->schema([
                        Infolists\Components\TextEntry::make('excerpt')
                            ->label('')
                            ->placeholder('Nenhum resumo fornecido'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentsRelationManager::class,
            RelationManagers\PetitionSignaturesRelationManager::class,
        ];
    }
    
    public static function getRelationsForRecord($record): array
    {
        $relations = [RelationManagers\CommentsRelationManager::class];
        
        if ($record->destination === 'peticoes') {
            $relations[] = RelationManagers\PetitionSignaturesRelationManager::class;
        }
        
        return $relations;
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
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
            
        // Se o usuário não é admin e tem restrição de editar apenas próprios posts
        if (!Auth::user()->is_admin && Auth::user()->can_edit_own_posts_only) {
            $query->where('user_id', Auth::id());
        }
        
        return $query;
    }
    
    public static function canCreate(): bool
    {
        return Auth::user()->canCreatePosts();
    }
    
    public static function canEdit(Model $record): bool
    {
        return Auth::user()->canEditPost($record);
    }
    
    public static function canDelete(Model $record): bool
    {
        return Auth::user()->canDeletePost($record);
    }
    
    public static function canDeleteAny(): bool
    {
        return Auth::user()->canDeletePosts();
    }
    
    public static function canAccess(): bool
    {
        return Auth::user()->canCreatePosts() || 
               Auth::user()->canEditPosts() || 
               Auth::user()->canDeletePosts() ||
               Auth::user()->is_admin;
    }
}
