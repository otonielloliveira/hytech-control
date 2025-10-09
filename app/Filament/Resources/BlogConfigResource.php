<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogConfigResource\Pages;
use App\Models\BlogConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;

class BlogConfigResource extends Resource
{
    protected static ?string $model = BlogConfig::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationLabel = 'Configurações do Blog';
    
    protected static ?string $modelLabel = 'Configuração';
    
    protected static ?string $pluralModelLabel = 'Configurações';
    
    protected static ?string $navigationGroup = 'Blog';
    
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Configurações do Blog')
                    ->tabs([
                        Tabs\Tab::make('Geral')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Section::make('Informações do Site')
                                    ->schema([
                                        Forms\Components\TextInput::make('site_name')
                                            ->label('Nome do Site')
                                            ->required()
                                            ->placeholder('HyTech Control Blog'),
                                        
                                        Forms\Components\Textarea::make('site_description')
                                            ->label('Descrição do Site')
                                            ->rows(3)
                                            ->placeholder('Blog oficial da HyTech Control - Tecnologia e Inovação')
                                            ->columnSpanFull(),
                                    ])->columns(1),
                                
                                Section::make('Logotipo e Ícone')
                                    ->schema([
                                        Forms\Components\FileUpload::make('site_logo')
                                            ->label('Logotipo do Site')
                                            ->image()
                                            ->imageEditor()
                                            ->directory('blog/config')
                                            ->visibility('public')
                                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/svg+xml'])
                                            ->maxSize(2048),
                                        
                                        Forms\Components\FileUpload::make('site_favicon')
                                            ->label('Favicon')
                                            ->image()
                                            ->directory('blog/config')
                                            ->visibility('public')
                                            ->acceptedFileTypes(['image/x-icon', 'image/png'])
                                            ->maxSize(512),
                                    ])->columns(2),
                            ]),
                        
                        Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Section::make('Meta Tags Globais')
                                    ->schema([
                                        Forms\Components\TextInput::make('meta_title')
                                            ->label('Título SEO')
                                            ->placeholder('Deixe vazio para usar o nome do site')
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\Textarea::make('meta_description')
                                            ->label('Descrição SEO')
                                            ->rows(3)
                                            ->placeholder('Deixe vazio para usar a descrição do site')
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\TagsInput::make('meta_keywords')
                                            ->label('Palavras-chave SEO')
                                            ->placeholder('Digite as palavras-chave...')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        
                        Tabs\Tab::make('Contato')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make('Informações de Contato')
                                    ->schema([
                                        Forms\Components\TextInput::make('contact_email')
                                            ->label('E-mail de Contato')
                                            ->email()
                                            ->placeholder('contato@hytech.com'),
                                        
                                        Forms\Components\TextInput::make('contact_phone')
                                            ->label('Telefone de Contato')
                                            ->tel()
                                            ->placeholder('(11) 99999-9999'),
                                        
                                        Forms\Components\Textarea::make('address')
                                            ->label('Endereço')
                                            ->rows(3)
                                            ->placeholder('Rua, número, bairro, cidade - Estado')
                                            ->columnSpanFull(),
                                    ])->columns(2),
                            ]),
                        
                        Tabs\Tab::make('Redes Sociais')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Section::make('Links das Redes Sociais')
                                    ->schema([
                                        Forms\Components\KeyValue::make('social_links')
                                            ->label('Redes Sociais')
                                            ->keyLabel('Plataforma')
                                            ->valueLabel('URL')
                                            ->default([
                                                'facebook' => '',
                                                'twitter' => '',
                                                'instagram' => '',
                                                'linkedin' => '',
                                                'youtube' => '',
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        
                        Tabs\Tab::make('Footer')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Rodapé do Site')
                                    ->schema([
                                        Forms\Components\RichEditor::make('footer_text')
                                            ->label('Texto do Rodapé')
                                            ->placeholder('© ' . date('Y') . ' HyTech Control. Todos os direitos reservados.')
                                            ->columnSpanFull()
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'link',
                                            ]),
                                    ]),
                            ]),
                        
                        Tabs\Tab::make('Sidebar')
                            ->icon('heroicon-o-bars-3-bottom-right')
                            ->schema([
                                Section::make('Configurações da Sidebar')
                                    ->schema([
                                        Forms\Components\Toggle::make('show_sidebar')
                                            ->label('Exibir Sidebar')
                                            ->default(true)
                                            ->reactive(),
                                        
                                        Forms\Components\Select::make('sidebar_position')
                                            ->label('Posição da Sidebar')
                                            ->options([
                                                'right' => 'Direita',
                                                'left' => 'Esquerda',
                                            ])
                                            ->default('right')
                                            ->visible(fn (Forms\Get $get) => $get('show_sidebar')),
                                        
                                        Forms\Components\TextInput::make('sidebar_width')
                                            ->label('Largura da Sidebar')
                                            ->default('350px')
                                            ->helperText('Ex: 300px, 25%')
                                            ->visible(fn (Forms\Get $get) => $get('show_sidebar')),
                                    ])
                                    ->columns(3),
                                
                                Section::make('Cores Padrão dos Widgets')
                                    ->schema([
                                        Forms\Components\ColorPicker::make('default_widget_title_color')
                                            ->label('Cor do Título')
                                            ->default('#1e40af'),
                                        
                                        Forms\Components\ColorPicker::make('default_widget_background_color')
                                            ->label('Cor de Fundo')
                                            ->default('#ffffff'),
                                        
                                        Forms\Components\ColorPicker::make('default_widget_text_color')
                                            ->label('Cor do Texto')
                                            ->default('#1f2937'),
                                    ])
                                    ->columns(3)
                                    ->visible(fn (Forms\Get $get) => $get('show_sidebar')),
                                
                                Section::make('YouTube Integration')
                                    ->schema([
                                        Forms\Components\Toggle::make('show_youtube_widget')
                                            ->label('Exibir Widget do YouTube')
                                            ->default(false)
                                            ->reactive(),
                                        
                                        Forms\Components\TextInput::make('youtube_channel_name')
                                            ->label('Nome do Canal')
                                            ->placeholder('Meu Canal')
                                            ->visible(fn (Forms\Get $get) => $get('show_youtube_widget')),
                                        
                                        Forms\Components\TextInput::make('youtube_channel_url')
                                            ->label('URL do Canal')
                                            ->url()
                                            ->placeholder('https://www.youtube.com/@meucanal')
                                            ->visible(fn (Forms\Get $get) => $get('show_youtube_widget')),
                                    ])
                                    ->columns(3)
                                    ->visible(fn (Forms\Get $get) => $get('show_sidebar')),
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
                Tables\Columns\TextColumn::make('site_name')
                    ->label('Nome do Site')
                    ->searchable(),
                
                Tables\Columns\ImageColumn::make('site_logo')
                    ->label('Logo')
                    ->size(60),
                
                Tables\Columns\TextColumn::make('contact_email')
                    ->label('E-mail')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Atualização')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Removido para evitar exclusão acidental
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBlogConfigs::route('/'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return BlogConfig::count() === 0;
    }
}
