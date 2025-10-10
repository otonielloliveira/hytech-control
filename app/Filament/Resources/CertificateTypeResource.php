<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificateTypeResource\Pages;
use App\Models\CertificateType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CertificateTypeResource extends Resource
{
    protected static ?string $model = CertificateType::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    
    protected static ?string $navigationLabel = 'Tipos de Certificado';
    
    protected static ?string $modelLabel = 'Tipo de Certificado';
    
    protected static ?string $pluralModelLabel = 'Tipos de Certificado';
    
    protected static ?string $navigationGroup = 'Cursos';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Básicas')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->rows(3),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Configurações do Template')
                    ->schema([
                        Forms\Components\FileUpload::make('template_file')
                            ->label('Arquivo do Template')
                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                            ->directory('certificate-templates'),
                            
                        Forms\Components\KeyValue::make('template_config')
                            ->label('Configurações do Template')
                            ->keyLabel('Propriedade')
                            ->valueLabel('Valor')
                            ->hint('Configurações como posições, fontes, cores, etc.'),
                    ]),
                    
                Forms\Components\Section::make('Critérios de Emissão')
                    ->schema([
                        Forms\Components\TextInput::make('min_completion_percentage')
                            ->label('% Mínimo de Conclusão')
                            ->numeric()
                            ->default(100)
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100),
                            
                        Forms\Components\Toggle::make('requires_exam')
                            ->label('Requer Prova Final')
                            ->default(false),
                            
                        Forms\Components\TextInput::make('min_exam_score')
                            ->label('Nota Mínima na Prova')
                            ->numeric()
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100)
                            ->visible(fn (callable $get) => $get('requires_exam')),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true),
                            
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordem de Exibição')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('min_completion_percentage')
                    ->label('% Mínimo')
                    ->suffix('%')
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('requires_exam')
                    ->label('Requer Prova')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('min_exam_score')
                    ->label('Nota Mínima')
                    ->suffix('%')
                    ->placeholder('N/A'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('courses_count')
                    ->label('Cursos')
                    ->counts('courses')
                    ->badge(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->trueLabel('Ativos')
                    ->falseLabel('Inativos'),
                    
                Tables\Filters\TernaryFilter::make('requires_exam')
                    ->label('Tipo')
                    ->placeholder('Todos')
                    ->trueLabel('Com Prova')
                    ->falseLabel('Sem Prova'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificateTypes::route('/'),
            'create' => Pages\CreateCertificateType::route('/create'),
            'edit' => Pages\EditCertificateType::route('/{record}/edit'),
        ];
    }
}
