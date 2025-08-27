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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;

class VoiceflowSessionResource extends Resource
{
    protected static ?string $model = VoiceflowSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $modelLabel = 'Voiceflow Session';

    protected static ?string $pluralModelLabel = 'Voiceflow Sessions';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable(['name', 'email'])
                    ->preload()
                    ->required(),
                
                Forms\Components\TextInput::make('session_id')
                    ->label('Voiceflow User ID')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('project_id')
                    ->label('Project ID (localStorage key)')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\Select::make('status')
                    ->options([
                        'ACTIVE' => 'Active',
                        'INACTIVE' => 'Inactive',
                        'CHATTING' => 'Chatting',
                        'COMPLETED' => 'Completed',
                        'UPDATED' => 'Updated',
                    ])
                    ->required(),
                
                Forms\Components\TextInput::make('source')
                    ->label('Source')
                    ->maxLength(255)
                    ->default('manual'),
                
                Forms\Components\KeyValue::make('value_data')
                    ->label('Session Value Data')
                    ->keyLabel('Key')
                    ->valueLabel('Value')
                    ->reorderable(false)
                    ->columnSpanFull(),
                
                Forms\Components\DateTimePicker::make('session_created_at')
                    ->label('Session Created At')
                    ->displayFormat('M j, Y g:i A'),
                
                Forms\Components\DateTimePicker::make('session_updated_at')
                    ->label('Session Updated At')
                    ->displayFormat('M j, Y g:i A'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),
                
                Tables\Columns\TextColumn::make('session_id')
                    ->label('Voiceflow User ID')
                    ->searchable()
                    ->copyable()
                    ->limit(20)
                    ->tooltip(function (VoiceflowSession $record): string {
                        return $record->session_id;
                    }),
                
                Tables\Columns\TextColumn::make('project_id')
                    ->label('Project ID')
                    ->searchable()
                    ->copyable()
                    ->limit(20)
                    ->tooltip(function (VoiceflowSession $record): string {
                        return $record->project_id;
                    }),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ACTIVE' => 'success',
                        'CHATTING' => 'warning', 
                        'UPDATED' => 'primary',
                        'INACTIVE' => 'secondary',
                        'COMPLETED' => 'success',
                        default => 'secondary',
                    }),
                
                Tables\Columns\TextColumn::make('source')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'localStorage_sync' => 'primary',
                        'migrated_from_json' => 'secondary',
                        'migrated_from_legacy_json' => 'warning',
                        'manual' => 'success',
                        default => 'secondary',
                    }),
                
                Tables\Columns\TextColumn::make('data_size')
                    ->label('Data Size')
                    ->state(function (VoiceflowSession $record): string {
                        return number_format($record->getDataSize()) . ' bytes';
                    })
                    ->sortable(false),
                
                Tables\Columns\TextColumn::make('session_created_at')
                    ->label('Session Created')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('session_updated_at')
                    ->label('Last Activity')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recorded At')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'ACTIVE' => 'Active',
                        'INACTIVE' => 'Inactive', 
                        'CHATTING' => 'Chatting',
                        'COMPLETED' => 'Completed',
                        'UPDATED' => 'Updated',
                    ]),
                
                Tables\Filters\SelectFilter::make('source')
                    ->options([
                        'localStorage_sync' => 'LocalStorage Sync',
                        'migrated_from_json' => 'Migrated from JSON',
                        'migrated_from_legacy_json' => 'Legacy Migration',
                        'manual' => 'Manual',
                    ]),
                
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\Filter::make('recent')
                    ->query(fn (Builder $query): Builder => $query->where('session_updated_at', '>=', now()->subDays(7)))
                    ->label('Recent (Last 7 days)'),
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
            ->defaultSort('session_updated_at', 'desc')
            ->poll('30s'); // Auto-refresh every 30 seconds
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
            'view' => Pages\ViewVoiceflowSession::route('/{record}'),
            'edit' => Pages\EditVoiceflowSession::route('/{record}/edit'),
        ];
    }
}
