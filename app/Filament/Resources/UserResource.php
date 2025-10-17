<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Support\Enums\FontWeight;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Usuários';
    
    protected static ?string $modelLabel = 'Usuário';
    
    protected static ?string $pluralModelLabel = 'Usuários';
    
    protected static ?string $navigationGroup = 'Gerenciamento';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('User Management')
                    ->tabs([
                        Tabs\Tab::make('Informações Básicas')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Dados Pessoais')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nome Completo')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(2),
                                            
                                        Forms\Components\TextInput::make('email')
                                            ->label('E-mail')
                                            ->email()
                                            ->required()
                                            ->unique(User::class, 'email', ignoreRecord: true)
                                            ->maxLength(255)
                                            ->columnSpan(2),
                                            
                                        Forms\Components\FileUpload::make('avatar')
                                            ->label('Avatar')
                                            ->image()
                                            ->directory('avatars')
                                            ->maxSize(2048)
                                            ->imageEditor()
                                            ->columnSpan(2),
                                            
                                        Forms\Components\Textarea::make('bio')
                                            ->label('Biografia')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])->columns(2),
                                    
                                Section::make('Configurações de Conta')
                                    ->schema([
                                        Forms\Components\TextInput::make('password')
                                            ->label('Senha')
                                            ->password()
                                            ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null)
                                            ->dehydrated(fn ($state) => filled($state))
                                            ->required(fn (string $context): bool => $context === 'create')
                                            ->maxLength(255),
                                            
                                        Forms\Components\TextInput::make('password_confirmation')
                                            ->label('Confirmar Senha')
                                            ->password()
                                            ->same('password')
                                            ->required(fn (string $context): bool => $context === 'create'),
                                            
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Usuário Ativo')
                                            ->default(true)
                                            ->helperText('Usuários inativos não podem acessar o sistema'),
                                    ])->columns(2),
                            ]),
                            
                        Tabs\Tab::make('Perfil e Funções')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Section::make('Tipos de Usuário')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_admin')
                                            ->label('Administrador')
                                            ->helperText('Administradores têm acesso total ao sistema')
                                            ->reactive()
                                            ->columnSpan(2),
                                            
                                        Forms\Components\Toggle::make('is_author')
                                            ->label('Autor/Editor')
                                            ->helperText('Pode acessar a área de gerenciamento de conteúdo')
                                            ->reactive()
                                            ->columnSpan(2),
                                    ])->columns(2),
                                    
                                Section::make('Perfis Pré-definidos')
                                    ->schema([
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('set_editor')
                                                ->label('Aplicar Perfil: Editor')
                                                ->icon('heroicon-o-pencil-square')
                                                ->color('info')
                                                ->action(function ($livewire, $state) {
                                                    $livewire->data['is_author'] = true;
                                                    $livewire->data['can_create_posts'] = true;
                                                    $livewire->data['can_edit_posts'] = true;
                                                    $livewire->data['can_publish_posts'] = false;
                                                    $livewire->data['can_delete_posts'] = false;
                                                    $livewire->data['can_edit_own_posts_only'] = true;
                                                })
                                                ->hidden(fn ($get) => $get('is_admin')),
                                                
                                            Forms\Components\Actions\Action::make('set_author')
                                                ->label('Aplicar Perfil: Autor')
                                                ->icon('heroicon-o-user-plus')
                                                ->color('success')
                                                ->action(function ($livewire, $state) {
                                                    $livewire->data['is_author'] = true;
                                                    $livewire->data['can_create_posts'] = true;
                                                    $livewire->data['can_edit_posts'] = true;
                                                    $livewire->data['can_publish_posts'] = true;
                                                    $livewire->data['can_delete_posts'] = true;
                                                    $livewire->data['can_edit_own_posts_only'] = true;
                                                })
                                                ->hidden(fn ($get) => $get('is_admin')),
                                                
                                            Forms\Components\Actions\Action::make('set_moderator')
                                                ->label('Aplicar Perfil: Moderador')
                                                ->icon('heroicon-o-shield-check')
                                                ->color('warning')
                                                ->action(function ($livewire, $state) {
                                                    $livewire->data['is_author'] = true;
                                                    $livewire->data['can_create_posts'] = true;
                                                    $livewire->data['can_edit_posts'] = true;
                                                    $livewire->data['can_publish_posts'] = true;
                                                    $livewire->data['can_delete_posts'] = true;
                                                    $livewire->data['can_edit_own_posts_only'] = false;
                                                    $livewire->data['can_moderate_comments'] = true;
                                                    $livewire->data['can_manage_categories'] = true;
                                                    $livewire->data['can_manage_tags'] = true;
                                                })
                                                ->hidden(fn ($get) => $get('is_admin')),
                                        ])
                                    ])->hidden(fn ($get) => $get('is_admin')),
                            ]),
                            
                        Tabs\Tab::make('Permissões de Posts')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Gerenciamento de Posts')
                                    ->schema([
                                        Forms\Components\Toggle::make('can_create_posts')
                                            ->label('Criar Posts')
                                            ->helperText('Pode criar novos posts'),
                                            
                                        Forms\Components\Toggle::make('can_edit_posts')
                                            ->label('Editar Posts')
                                            ->helperText('Pode editar posts'),
                                            
                                        Forms\Components\Toggle::make('can_delete_posts')
                                            ->label('Excluir Posts')
                                            ->helperText('Pode excluir posts'),
                                            
                                        Forms\Components\Toggle::make('can_publish_posts')
                                            ->label('Publicar Posts')
                                            ->helperText('Pode publicar e despublicar posts'),
                                            
                                        Forms\Components\Toggle::make('can_edit_own_posts_only')
                                            ->label('Editar Apenas Próprios Posts')
                                            ->helperText('Se ativo, só pode editar/excluir seus próprios posts')
                                            ->default(true),
                                            
                                        Forms\Components\Toggle::make('can_moderate_comments')
                                            ->label('Moderar Comentários')
                                            ->helperText('Pode aprovar, rejeitar e excluir comentários'),
                                    ])->columns(2),
                            ])
                            ->hidden(fn ($get) => $get('is_admin')),
                            
                        Tabs\Tab::make('Outras Permissões')
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Section::make('Gerenciamento de Conteúdo')
                                    ->schema([
                                        Forms\Components\Toggle::make('can_manage_categories')
                                            ->label('Gerenciar Categorias')
                                            ->helperText('Pode criar, editar e excluir categorias'),
                                            
                                        Forms\Components\Toggle::make('can_manage_tags')
                                            ->label('Gerenciar Tags')
                                            ->helperText('Pode criar, editar e excluir tags'),
                                    ])->columns(2),
                                    
                                Section::make('Sistemas Específicos')
                                    ->schema([
                                        Forms\Components\Toggle::make('can_manage_users')
                                            ->label('Gerenciar Usuários')
                                            ->helperText('Pode criar, editar e excluir usuários'),
                                            
                                        Forms\Components\Toggle::make('can_manage_settings')
                                            ->label('Gerenciar Configurações')
                                            ->helperText('Pode alterar configurações do sistema'),
                                            
                                        Forms\Components\Toggle::make('can_manage_store')
                                            ->label('Gerenciar Loja')
                                            ->helperText('Pode gerenciar produtos, pedidos e configurações da loja'),
                                            
                                        Forms\Components\Toggle::make('can_manage_donations')
                                            ->label('Gerenciar Doações')
                                            ->helperText('Pode visualizar e gerenciar doações'),
                                            
                                        Forms\Components\Toggle::make('can_manage_courses')
                                            ->label('Gerenciar Cursos')
                                            ->helperText('Pode criar e gerenciar cursos e módulos'),
                                    ])->columns(2),
                            ])
                            ->hidden(fn ($get) => $get('is_admin')),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(fn () => asset('images/default-avatar.png')),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->description(fn (User $record): ?string => $record->email),
                    
                Tables\Columns\TextColumn::make('role')
                    ->label('Função')
                    ->badge()
                    ->color(fn (User $record): string => match (true) {
                        $record->is_admin => 'danger',
                        $record->is_author => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (User $record): string => match (true) {
                        $record->is_admin => 'Administrador',
                        $record->is_author => 'Autor/Editor',
                        default => 'Usuário',
                    }),
                    
                Tables\Columns\TextColumn::make('posts_count')
                    ->label('Posts')
                    ->counts('posts')
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Último Acesso')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Nunca acessou')
                    ->color(fn ($state) => $state && $state->diffInDays(now()) <= 7 ? 'success' : 'warning'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Função')
                    ->options([
                        'admin' => 'Administradores',
                        'author' => 'Autores/Editores',
                        'user' => 'Usuários',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'admin',
                            fn (Builder $query): Builder => $query->where('is_admin', true),
                        )->when(
                            $data['value'] === 'author',
                            fn (Builder $query): Builder => $query->where('is_author', true)->where('is_admin', false),
                        )->when(
                            $data['value'] === 'user',
                            fn (Builder $query): Builder => $query->where('is_author', false)->where('is_admin', false),
                        );
                    }),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->trueLabel('Apenas ativos')
                    ->falseLabel('Apenas inativos'),
                    
                Tables\Filters\Filter::make('recent_login')
                    ->label('Acessaram recentemente')
                    ->query(fn (Builder $query): Builder => $query->where('last_login_at', '>=', now()->subDays(30))),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hiddenLabel()
                    ->tooltip('Editar usuário'),
                Tables\Actions\Action::make('toggle_status')
                    ->icon(fn (User $record) => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn (User $record) => $record->is_active ? 'warning' : 'success')
                    ->action(fn (User $record) => $record->update(['is_active' => !$record->is_active]))
                    ->requiresConfirmation()
                    ->hiddenLabel()
                    ->tooltip(fn (User $record) => $record->is_active ? 'Desativar usuário' : 'Ativar usuário'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->hidden(fn () => !Auth::user()->canManageUsers()),
                        
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Ativar selecionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->requiresConfirmation(),
                        
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desativar selecionados')
                        ->icon('heroicon-o-pause')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PostsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function canAccess(): bool
    {
        return Auth::user()->canManageUsers();
    }
}
