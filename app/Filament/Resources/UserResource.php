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
                        Forms\Components\DateTimePicker::make('pi_assessed_at')
                            ->label('Assessment Date'),
                        Forms\Components\Textarea::make('pi_notes')
                            ->label('Assessment Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2)
                    ->collapsible(),
                
                Forms\Components\Section::make('Performance Index Profile')
                    ->schema([
                        Forms\Components\Fieldset::make('Basic Information')
                            ->schema([
                                Forms\Components\DatePicker::make('pi_profile.basic_info.assessment_date')
                                    ->label('Assessment Date'),
                                Forms\Components\DatePicker::make('pi_profile.basic_info.report_date')
                                    ->label('Report Date'),
                                Forms\Components\TextInput::make('pi_profile.basic_info.profile_type')
                                    ->label('Profile Type')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('pi_profile.basic_info.profile_description')
                                    ->label('Profile Description')
                                    ->rows(3),
                            ])->columns(2),

                        Forms\Components\Fieldset::make('Behavioral Traits')
                            ->schema([
                                Forms\Components\Repeater::make('pi_profile.behavioral_traits.strongest_behaviors')
                                    ->label('Strongest Behaviors')
                                    ->schema([
                                        Forms\Components\TextInput::make('behavior')
                                            ->label('Behavior Statement')
                                            ->required(),
                                    ])
                                    ->addActionLabel('Add Behavior')
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['behavior'] ?? null),
                                Forms\Components\Textarea::make('pi_profile.behavioral_traits.summary')
                                    ->label('Summary')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Fieldset::make('Management Strategies')
                            ->schema([
                                Forms\Components\Repeater::make('pi_profile.management_strategies.optimal_conditions')
                                    ->label('Optimal Conditions')
                                    ->schema([
                                        Forms\Components\TextInput::make('condition')
                                            ->label('Condition')
                                            ->required(),
                                    ])
                                    ->addActionLabel('Add Condition')
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['condition'] ?? null),
                                Forms\Components\Repeater::make('pi_profile.management_strategies.avoid_conditions')
                                    ->label('Conditions to Avoid')
                                    ->schema([
                                        Forms\Components\TextInput::make('condition')
                                            ->label('Condition to Avoid')
                                            ->required(),
                                    ])
                                    ->addActionLabel('Add Condition to Avoid')
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['condition'] ?? null),
                            ]),

                        Forms\Components\Fieldset::make('Work Preferences')
                            ->schema([
                                Forms\Components\TextInput::make('pi_profile.work_preferences.pace')
                                    ->label('Pace'),
                                Forms\Components\TextInput::make('pi_profile.work_preferences.decision_making')
                                    ->label('Decision Making'),
                                Forms\Components\TextInput::make('pi_profile.work_preferences.communication_style')
                                    ->label('Communication Style'),
                                Forms\Components\TextInput::make('pi_profile.work_preferences.focus')
                                    ->label('Focus'),
                                Forms\Components\TextInput::make('pi_profile.work_preferences.team_orientation')
                                    ->label('Team Orientation'),
                                Forms\Components\TextInput::make('pi_profile.work_preferences.risk_tolerance')
                                    ->label('Risk Tolerance'),
                            ])->columns(2),

                        Forms\Components\Fieldset::make('Social Style')
                            ->schema([
                                Forms\Components\TextInput::make('pi_profile.social_style.formality')
                                    ->label('Formality'),
                                Forms\Components\TextInput::make('pi_profile.social_style.trust_building')
                                    ->label('Trust Building'),
                                Forms\Components\TextInput::make('pi_profile.social_style.relationship_focus')
                                    ->label('Relationship Focus'),
                            ])->columns(2),

                        Forms\Components\Fieldset::make('Motivation Drivers')
                            ->schema([
                                Forms\Components\TextInput::make('pi_profile.motivation_drivers.recognition')
                                    ->label('Recognition'),
                                Forms\Components\TextInput::make('pi_profile.motivation_drivers.security')
                                    ->label('Security'),
                                Forms\Components\TextInput::make('pi_profile.motivation_drivers.autonomy')
                                    ->label('Autonomy'),
                                Forms\Components\TextInput::make('pi_profile.motivation_drivers.advancement')
                                    ->label('Advancement'),
                                Forms\Components\TextInput::make('pi_profile.motivation_drivers.collaboration')
                                    ->label('Collaboration'),
                            ])->columns(2),

                        Forms\Components\Fieldset::make('Technical Orientation')
                            ->schema([
                                Forms\Components\TextInput::make('pi_profile.technical_orientation.detail_orientation')
                                    ->label('Detail Orientation'),
                                Forms\Components\TextInput::make('pi_profile.technical_orientation.innovation_tolerance')
                                    ->label('Innovation Tolerance'),
                                Forms\Components\TextInput::make('pi_profile.technical_orientation.process_adherence')
                                    ->label('Process Adherence'),
                            ])->columns(2),
                    ])
                    ->collapsible()
                    ->collapsed(),
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
                Tables\Columns\IconColumn::make('pi_profile')
                    ->label('Has PI Profile')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !is_null($record->pi_profile))
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
                Tables\Filters\Filter::make('has_pi_profile')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('pi_profile'))
                    ->label('Has PI Profile'),
                Tables\Filters\Filter::make('no_pi_profile')
                    ->query(fn (Builder $query): Builder => $query->whereNull('pi_profile'))
                    ->label('No PI Profile'),
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
