<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoiceflowSessionResource\Pages;
use App\Filament\Resources\VoiceflowSessionResource\RelationManagers;
use App\Models\VoiceflowSession;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VoiceflowSessionResource extends Resource
{
    protected static ?string $model = VoiceflowSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationLabel = 'Sessions';
    
    protected static ?string $modelLabel = 'Session';
    
    protected static ?string $pluralModelLabel = 'Sessions';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Session Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Components\TextInput::make('name')
                            ->label('Session Name')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('session_id')
                            ->label('Session ID')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('project_id')
                            ->label('Project ID')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'ACTIVE' => 'Active',
                                'COMPLETED' => 'Completed',
                                'PAUSED' => 'Paused',
                                'TERMINATED' => 'Terminated',
                            ])
                            ->default('ACTIVE'),
                        
                        Forms\Components\TextInput::make('source')
                            ->label('Source')
                            ->maxLength(255),
                    ])->columns(2),
                
                Forms\Components\Section::make('Feedback')
                    ->schema([
                        Forms\Components\Select::make('feedback_rating')
                            ->label('Feedback Rating')
                            ->options([
                                'positive' => 'Positive',
                                'negative' => 'Negative',
                            ])
                            ->nullable(),
                        
                        Forms\Components\Textarea::make('feedback_comment')
                            ->label('Feedback Comment')
                            ->rows(3)
                            ->nullable(),
                        
                        Forms\Components\DateTimePicker::make('feedback_submitted_at')
                            ->label('Feedback Submitted At')
                            ->nullable(),
                    ])->columns(1),
                
                Forms\Components\Section::make('Timestamps')
                    ->schema([
                        Forms\Components\DateTimePicker::make('session_created_at')
                            ->label('Session Created At')
                            ->nullable(),
                        
                        Forms\Components\DateTimePicker::make('session_updated_at')
                            ->label('Session Updated At')
                            ->nullable(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Session Data')
                    ->schema([
                        Forms\Components\Textarea::make('value_data')
                            ->label('Value Data (JSON)')
                            ->rows(10)
                            ->hint('This field contains the raw session data from Voiceflow')
                            ->disabled()
                            ->dehydrated(false),
                    ])->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Session Name')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                
                Tables\Columns\TextColumn::make('session_id')
                    ->label('Session ID')
                    ->searchable()
                    ->copyable()
                    ->limit(15)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        return $column->getState();
                    }),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'ACTIVE',
                        'warning' => 'PAUSED',
                        'primary' => 'COMPLETED',
                        'danger' => 'TERMINATED',
                    ]),
                
                Tables\Columns\BadgeColumn::make('feedback_rating')
                    ->label('Feedback')
                    ->colors([
                        'success' => 'positive',
                        'danger' => 'negative',
                    ])
                    ->formatStateUsing(fn (string $state = null): string => match ($state) {
                        'positive' => 'Positive',
                        'negative' => 'Negative',
                        default => 'No feedback'
                    })
                    ->default('No feedback'),
                
                Tables\Columns\TextColumn::make('value_data')
                    ->label('Messages')
                    ->getStateUsing(function ($record) {
                        $data = $record->value_data;
                        return is_array($data) && isset($data['turns']) ? count($data['turns']) : 0;
                    })
                    ->sortable(false),
                
                Tables\Columns\TextColumn::make('session_created_at')
                    ->label('Created')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('feedback_submitted_at')
                    ->label('Feedback Date')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'ACTIVE' => 'Active',
                        'COMPLETED' => 'Completed',
                        'PAUSED' => 'Paused',
                        'TERMINATED' => 'Terminated',
                    ]),
                
                Tables\Filters\SelectFilter::make('feedback_rating')
                    ->label('Feedback')
                    ->options([
                        'positive' => 'Positive',
                        'negative' => 'Negative',
                    ]),
                
                Tables\Filters\Filter::make('has_feedback')
                    ->label('Has Feedback')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('feedback_rating')),
                
                Tables\Filters\Filter::make('no_feedback')
                    ->label('No Feedback')
                    ->query(fn (Builder $query): Builder => $query->whereNull('feedback_rating')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('view_feedback')
                    ->label('View Feedback')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('info')
                    ->visible(fn ($record) => $record->hasFeedback())
                    ->modalHeading(fn ($record) => 'Feedback for Session: ' . ($record->name ?: 'Unnamed'))
                    ->modalDescription(fn ($record) => 'User: ' . $record->user->name)
                    ->modalContent(function ($record) {
                        return view('filament.session-feedback-modal', [
                            'session' => $record
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('session_created_at', 'desc');
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
            'index' => Pages\ListVoiceflowSessions::route('/'),
            'create' => Pages\CreateVoiceflowSession::route('/create'),
            'edit' => Pages\EditVoiceflowSession::route('/{record}/edit'),
        ];
    }
}
