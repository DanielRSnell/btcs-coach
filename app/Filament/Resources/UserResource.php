<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\PiBehavioralPattern;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Select::make('role')
                            ->options([
                                'admin' => 'Administrator',
                                'member' => 'Member',
                            ])
                            ->required()
                            ->default('member'),
                        Forms\Components\DateTimePicker::make('email_verified_at'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Password')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8)
                            ->same('passwordConfirmation')
                            ->dehydrated(fn ($state) => filled($state)),
                        Forms\Components\TextInput::make('passwordConfirmation')
                            ->password()
                            ->label('Password Confirmation')
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8)
                            ->dehydrated(false),
                    ])->columns(2)
                    ->collapsible(),
                
                Forms\Components\Section::make('PI Assessment')
                    ->schema([
                        Forms\Components\Select::make('pi_behavioral_pattern_id')
                            ->label('PI Behavioral Pattern')
                            ->options(PiBehavioralPattern::active()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a PI pattern'),
                        Forms\Components\TextInput::make('pi_assessor_name')
                            ->label('Assessor Name')
                            ->maxLength(255)
                            ->placeholder('Who conducted the assessment?'),
                        Forms\Components\DateTimePicker::make('pi_assessed_at')
                            ->label('Assessment Date'),
                        Forms\Components\Textarea::make('pi_notes')
                            ->label('Assessment Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2)
                    ->collapsible(),
                
                Forms\Components\Section::make('PI Individual Scores (0-100)')
                    ->schema([
                        Forms\Components\TextInput::make('pi_raw_scores.dominance')
                            ->label('Dominance (A)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\TextInput::make('pi_raw_scores.extraversion')
                            ->label('Extraversion (B)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\TextInput::make('pi_raw_scores.patience')
                            ->label('Patience (C)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\TextInput::make('pi_raw_scores.formality')
                            ->label('Formality (D)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                    ])->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'success',
                        'member' => 'info',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('piBehavioralPattern.name')
                    ->label('PI Pattern')
                    ->badge()
                    ->color('primary')
                    ->placeholder('Not assessed')
                    ->sortable(),
                Tables\Columns\TextColumn::make('piBehavioralPattern.code')
                    ->label('PI Code')
                    ->badge()
                    ->color('gray')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->boolean()
                    ->label('Verified')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pi_assessed_at')
                    ->label('PI Assessed')
                    ->date()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'))
                    ->label('Verified users'),
                Tables\Filters\Filter::make('unverified')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at'))
                    ->label('Unverified users'),
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin' => 'Administrator',
                        'member' => 'Member',
                    ]),
                Tables\Filters\SelectFilter::make('pi_behavioral_pattern_id')
                    ->label('PI Pattern')
                    ->relationship('piBehavioralPattern', 'name')
                    ->preload(),
                Tables\Filters\Filter::make('has_pi_assessment')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('pi_behavioral_pattern_id'))
                    ->label('Has PI Assessment'),
                Tables\Filters\Filter::make('no_pi_assessment')
                    ->query(fn (Builder $query): Builder => $query->whereNull('pi_behavioral_pattern_id'))
                    ->label('No PI Assessment'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
