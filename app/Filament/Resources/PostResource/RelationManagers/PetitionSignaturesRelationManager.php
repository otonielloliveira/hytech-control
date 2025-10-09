<?php

namespace App\Filament\Resources\PostResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class PetitionSignaturesRelationManager extends RelationManager
{
    protected static string $relationship = 'petitionSignatures';
    
    protected static ?string $title = 'Assinaturas da Petição';
    
    protected static ?string $label = 'Assinatura';
    
    protected static ?string $pluralLabel = 'Assinaturas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dados do Assinante')
                    ->schema([
                        Forms\Components\TextInput::make('nome')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('tel_whatsapp')
                            ->label('WhatsApp')
                            ->required()
                            ->maxLength(20),
                    ])->columns(3),
                
                Forms\Components\Section::make('Localização')
                    ->schema([
                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->required()
                            ->options([
                                'AC' => 'Acre',
                                'AL' => 'Alagoas',
                                'AP' => 'Amapá',
                                'AM' => 'Amazonas',
                                'BA' => 'Bahia',
                                'CE' => 'Ceará',
                                'DF' => 'Distrito Federal',
                                'ES' => 'Espírito Santo',
                                'GO' => 'Goiás',
                                'MA' => 'Maranhão',
                                'MT' => 'Mato Grosso',
                                'MS' => 'Mato Grosso do Sul',
                                'MG' => 'Minas Gerais',
                                'PA' => 'Pará',
                                'PB' => 'Paraíba',
                                'PR' => 'Paraná',
                                'PE' => 'Pernambuco',
                                'PI' => 'Piauí',
                                'RJ' => 'Rio de Janeiro',
                                'RN' => 'Rio Grande do Norte',
                                'RS' => 'Rio Grande do Sul',
                                'RO' => 'Rondônia',
                                'RR' => 'Roraima',
                                'SC' => 'Santa Catarina',
                                'SP' => 'São Paulo',
                                'SE' => 'Sergipe',
                                'TO' => 'Tocantins',
                            ]),
                        
                        Forms\Components\TextInput::make('cidade')
                            ->label('Cidade')
                            ->required()
                            ->maxLength(100),
                    ])->columns(2),
                
                Forms\Components\Section::make('Redes Sociais')
                    ->schema([
                        Forms\Components\TextInput::make('link_facebook')
                            ->label('Facebook')
                            ->url()
                            ->maxLength(500),
                        
                        Forms\Components\TextInput::make('link_instagram')
                            ->label('Instagram')
                            ->url()
                            ->maxLength(500),
                    ])->columns(2),
                
                Forms\Components\Section::make('Observação')
                    ->schema([
                        Forms\Components\Textarea::make('observacao')
                            ->label('Observação')
                            ->rows(3)
                            ->maxLength(1000),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        // Verificar se o post é uma petição, se não for, retornar tabela vazia
        if ($this->getOwnerRecord()->destination !== 'peticoes') {
            return $table
                ->emptyStateHeading('Esta funcionalidade é exclusiva para petições')
                ->emptyStateDescription('As assinaturas só estão disponíveis para posts com destino "Petições".')
                ->emptyStateIcon('heroicon-o-document-text');
        }
        
        return $table
            ->recordTitleAttribute('nome')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->size('sm'),
                
                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                
                Tables\Columns\TextColumn::make('tel_whatsapp')
                    ->label('WhatsApp')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone')
                    ->formatStateUsing(function ($state) {
                        $phone = preg_replace('/\D/', '', $state);
                        if (strlen($phone) === 11) {
                            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
                        }
                        return $state;
                    }),
                
                Tables\Columns\TextColumn::make('cidade')
                    ->label('Cidade')
                    ->searchable()
                    ->formatStateUsing(fn ($record) => $record->cidade . '/' . $record->estado),
                
                Tables\Columns\IconColumn::make('link_facebook')
                    ->label('FB')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->getStateUsing(fn ($record) => !empty($record->link_facebook)),
                
                Tables\Columns\IconColumn::make('link_instagram')
                    ->label('IG')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->getStateUsing(fn ($record) => !empty($record->link_instagram)),
                
                Tables\Columns\TextColumn::make('signed_at')
                    ->label('Data da Assinatura')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color('gray')
                    ->size('sm'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'AC' => 'Acre',
                        'AL' => 'Alagoas',
                        'AP' => 'Amapá',
                        'AM' => 'Amazonas',
                        'BA' => 'Bahia',
                        'CE' => 'Ceará',
                        'DF' => 'Distrito Federal',
                        'ES' => 'Espírito Santo',
                        'GO' => 'Goiás',
                        'MA' => 'Maranhão',
                        'MT' => 'Mato Grosso',
                        'MS' => 'Mato Grosso do Sul',
                        'MG' => 'Minas Gerais',
                        'PA' => 'Pará',
                        'PB' => 'Paraíba',
                        'PR' => 'Paraná',
                        'PE' => 'Pernambuco',
                        'PI' => 'Piauí',
                        'RJ' => 'Rio de Janeiro',
                        'RN' => 'Rio Grande do Norte',
                        'RS' => 'Rio Grande do Sul',
                        'RO' => 'Rondônia',
                        'RR' => 'Roraima',
                        'SC' => 'Santa Catarina',
                        'SP' => 'São Paulo',
                        'SE' => 'Sergipe',
                        'TO' => 'Tocantins',
                    ]),
                
                Tables\Filters\Filter::make('com_redes_sociais')
                    ->label('Com Redes Sociais')
                    ->query(fn (Builder $query): Builder => $query->where(function ($q) {
                        $q->whereNotNull('link_facebook')
                          ->orWhereNotNull('link_instagram');
                    })),
                
                Tables\Filters\Filter::make('data_assinatura')
                    ->form([
                        Forms\Components\DatePicker::make('de')
                            ->label('De'),
                        Forms\Components\DatePicker::make('ate')
                            ->label('Até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['de'],
                                fn (Builder $query, $date): Builder => $query->whereDate('signed_at', '>=', $date),
                            )
                            ->when(
                                $data['ate'],
                                fn (Builder $query, $date): Builder => $query->whereDate('signed_at', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportar_pdf')
                    ->label('Exportar PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function () {
                        $post = $this->getOwnerRecord();
                        $signatures = $post->petitionSignatures()->orderBy('signed_at')->get();
                        
                        $pdf = Pdf::loadView('filament.exports.petition-signatures', [
                            'post' => $post,
                            'signatures' => $signatures,
                            'total' => $signatures->count()
                        ]);
                        
                        return Response::streamDownload(
                            fn () => print($pdf->output()),
                            'assinaturas-peticao-' . Str::slug($post->title) . '.pdf'
                        );
                    }),
                
                Tables\Actions\CreateAction::make()
                    ->label('Nova Assinatura')
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('exportar_selecionados')
                        ->label('Exportar Selecionados (PDF)')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function ($records) {
                            $post = $this->getOwnerRecord();
                            
                            $pdf = Pdf::loadView('filament.exports.petition-signatures', [
                                'post' => $post,
                                'signatures' => $records,
                                'total' => $records->count()
                            ]);
                            
                            return Response::streamDownload(
                                fn () => print($pdf->output()),
                                'assinaturas-selecionadas-' . Str::slug($post->title) . '.pdf'
                            );
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('signed_at', 'desc')
            ->poll('30s')
            ->emptyStateHeading('Nenhuma assinatura encontrada')
            ->emptyStateDescription('Esta petição ainda não possui assinaturas.')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
