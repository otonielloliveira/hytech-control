<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseEnrollmentResource\Pages;
use App\Models\CourseEnrollment;
use App\Models\Course;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class CourseEnrollmentResource extends Resource
{
    protected static ?string $model = CourseEnrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Matrículas';
    
    protected static ?string $modelLabel = 'Matrícula';
    
    protected static ?string $pluralModelLabel = 'Matrículas';
    
    protected static ?string $navigationGroup = 'Cursos';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações da Matrícula')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label('Cliente')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->required(),
                            
                        Forms\Components\Select::make('course_id')
                            ->label('Curso')
                            ->relationship('course', 'title')
                            ->searchable()
                            ->required(),
                            
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Ativo',
                                'completed' => 'Concluído',
                                'cancelled' => 'Cancelado',
                                'expired' => 'Expirado',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make('Informações de Pagamento')
                    ->schema([
                        Forms\Components\TextInput::make('paid_amount')
                            ->label('Valor Pago')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0),
                            
                        Forms\Components\TextInput::make('payment_method')
                            ->label('Método de Pagamento')
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('payment_transaction_id')
                            ->label('ID da Transação')
                            ->maxLength(255),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make('Progresso e Datas')
                    ->schema([
                        Forms\Components\TextInput::make('progress_percentage')
                            ->label('Progresso (%)')
                            ->numeric()
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0),
                            
                        Forms\Components\DateTimePicker::make('started_at')
                            ->label('Iniciado em')
                            ->displayFormat('d/m/Y H:i'),
                            
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Concluído em')
                            ->displayFormat('d/m/Y H:i'),
                            
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expira em')
                            ->displayFormat('d/m/Y H:i'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Certificado')
                    ->schema([
                        Forms\Components\DateTimePicker::make('certificate_issued_at')
                            ->label('Certificado Emitido em')
                            ->displayFormat('d/m/Y H:i'),
                            
                        Forms\Components\TextInput::make('certificate_number')
                            ->label('Número do Certificado')
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('final_score')
                            ->label('Nota Final')
                            ->numeric()
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make('Observações')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Observações')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('course.title')
                    ->label('Curso')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'primary',
                        'cancelled' => 'danger',
                        'expired' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Ativo',
                        'completed' => 'Concluído',
                        'cancelled' => 'Cancelado',
                        'expired' => 'Expirado',
                    }),
                    
                Tables\Columns\TextColumn::make('progress_percentage')
                    ->label('Progresso')
                    ->suffix('%')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 100 => 'success',
                        $state >= 75 => 'primary',
                        $state >= 50 => 'warning',
                        $state >= 25 => 'info',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Valor Pago')
                    ->money('BRL')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('certificate_number')
                    ->label('Certificado')
                    ->placeholder('Não emitido')
                    ->copyable()
                    ->copyMessage('Número copiado!')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('started_at')
                    ->label('Iniciado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Concluído em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Em andamento')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expira em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Sem expiração')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Matrícula em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Ativo',
                        'completed' => 'Concluído',
                        'cancelled' => 'Cancelado',
                        'expired' => 'Expirado',
                    ]),
                    
                Tables\Filters\SelectFilter::make('course')
                    ->label('Curso')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\Filter::make('progress_range')
                    ->form([
                        Forms\Components\TextInput::make('progress_from')
                            ->label('Progresso mínimo (%)')
                            ->numeric(),
                        Forms\Components\TextInput::make('progress_to')
                            ->label('Progresso máximo (%)')
                            ->numeric(),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['progress_from'], fn ($q, $progress) => $q->where('progress_percentage', '>=', $progress))
                            ->when($data['progress_to'], fn ($q, $progress) => $q->where('progress_percentage', '<=', $progress));
                    }),
                    
                Tables\Filters\TernaryFilter::make('has_certificate')
                    ->label('Certificado')
                    ->placeholder('Todos')
                    ->trueLabel('Com certificado')
                    ->falseLabel('Sem certificado')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('certificate_issued_at'),
                        false: fn ($query) => $query->whereNull('certificate_issued_at'),
                    ),
                    
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Matrícula de'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Matrícula até'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('issue_certificate')
                    ->label('Emitir Certificado')
                    ->icon('heroicon-o-academic-cap')
                    ->color('success')
                    ->visible(fn (CourseEnrollment $record) => 
                        $record->status === 'completed' && 
                        !$record->certificate_issued_at && 
                        $record->canIssueCertificate()
                    )
                    ->action(function (CourseEnrollment $record) {
                        $record->issueCertificate();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Emitir Certificado')
                    ->modalDescription('Tem certeza que deseja emitir o certificado para este aluno?'),
                    
                Tables\Actions\Action::make('extend_access')
                    ->label('Estender Acesso')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->visible(fn (CourseEnrollment $record) => $record->expires_at)
                    ->form([
                        Forms\Components\DateTimePicker::make('new_expiry')
                            ->label('Nova Data de Expiração')
                            ->required()
                            ->displayFormat('d/m/Y H:i')
                            ->default(fn (CourseEnrollment $record) => 
                                $record->expires_at ? $record->expires_at->addMonths(3) : now()->addMonths(3)
                            ),
                    ])
                    ->action(function (CourseEnrollment $record, array $data) {
                        $record->update(['expires_at' => $data['new_expiry']]);
                    }),
                    
                Tables\Actions\Action::make('reset_progress')
                    ->label('Resetar Progresso')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->action(function (CourseEnrollment $record) {
                        $record->lessonProgress()->delete();
                        $record->update([
                            'progress_percentage' => 0,
                            'completed_at' => null,
                            'certificate_issued_at' => null,
                            'certificate_number' => null,
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Resetar Progresso')
                    ->modalDescription('Esta ação irá apagar todo o progresso do aluno no curso. Esta ação não pode ser desfeita.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('mark_completed')
                        ->label('Marcar como Concluído')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function (CourseEnrollment $record) {
                                if ($record->status === 'active') {
                                    $record->markAsCompleted();
                                }
                            });
                        })
                        ->requiresConfirmation(),
                        
                    Tables\Actions\BulkAction::make('extend_access')
                        ->label('Estender Acesso')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->form([
                            Forms\Components\DateTimePicker::make('new_expiry')
                                ->label('Nova Data de Expiração')
                                ->required()
                                ->displayFormat('d/m/Y H:i')
                                ->default(now()->addMonths(3)),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(function (CourseEnrollment $record) use ($data) {
                                $record->update(['expires_at' => $data['new_expiry']]);
                            });
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações da Matrícula')
                    ->schema([
                        Infolists\Components\TextEntry::make('client.name')
                            ->label('Cliente'),
                        Infolists\Components\TextEntry::make('course.title')
                            ->label('Curso'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge(),
                        Infolists\Components\TextEntry::make('progress_percentage')
                            ->label('Progresso')
                            ->suffix('%'),
                    ])
                    ->columns(2),
                    
                Infolists\Components\Section::make('Datas Importantes')
                    ->schema([
                        Infolists\Components\TextEntry::make('started_at')
                            ->label('Iniciado em')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('completed_at')
                            ->label('Concluído em')
                            ->dateTime()
                            ->placeholder('Em andamento'),
                        Infolists\Components\TextEntry::make('expires_at')
                            ->label('Expira em')
                            ->dateTime()
                            ->placeholder('Sem expiração'),
                        Infolists\Components\TextEntry::make('certificate_issued_at')
                            ->label('Certificado Emitido em')
                            ->dateTime()
                            ->placeholder('Não emitido'),
                    ])
                    ->columns(2),
                    
                Infolists\Components\Section::make('Informações de Pagamento')
                    ->schema([
                        Infolists\Components\TextEntry::make('paid_amount')
                            ->label('Valor Pago')
                            ->money('BRL'),
                        Infolists\Components\TextEntry::make('payment_method')
                            ->label('Método de Pagamento')
                            ->placeholder('Não informado'),
                        Infolists\Components\TextEntry::make('payment_transaction_id')
                            ->label('ID da Transação')
                            ->placeholder('Não informado'),
                        Infolists\Components\TextEntry::make('certificate_number')
                            ->label('Número do Certificado')
                            ->placeholder('Não emitido')
                            ->copyable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourseEnrollments::route('/'),
            'create' => Pages\CreateCourseEnrollment::route('/create'),
            'edit' => Pages\EditCourseEnrollment::route('/{record}/edit'),
        ];
    }
}
