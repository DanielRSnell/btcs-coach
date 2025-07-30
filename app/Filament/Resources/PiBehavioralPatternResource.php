<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PiBehavioralPatternResource\Pages;
use App\Filament\Resources\PiBehavioralPatternResource\RelationManagers;
use App\Models\PiBehavioralPattern;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PiBehavioralPatternResource extends Resource
{
    protected static ?string $model = PiBehavioralPattern::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationGroup = 'PI Assessment';
    
    protected static ?string $modelLabel = 'PI Pattern';
    
    protected static ?string $pluralModelLabel = 'PI Patterns';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10)
                            ->dehydrateStateUsing(fn (string $state): string => strtoupper($state)),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true),
                    ])->columns(2),
                
                Forms\Components\Section::make('Behavioral Drives (0-100 scale)')
                    ->schema([
                        Forms\Components\TextInput::make('behavioral_drives.dominance')
                            ->label('Dominance (A)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\TextInput::make('behavioral_drives.extraversion')
                            ->label('Extraversion (B)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\TextInput::make('behavioral_drives.patience')
                            ->label('Patience (C)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\TextInput::make('behavioral_drives.formality')
                            ->label('Formality (D)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100),
                    ])->columns(2),
                
                Forms\Components\Section::make('Characteristics')
                    ->schema([
                        Forms\Components\Textarea::make('strengths')
                            ->required()
                            ->rows(3),
                        Forms\Components\Textarea::make('challenges')
                            ->required()
                            ->rows(3),
                        Forms\Components\Textarea::make('work_style')
                            ->required()
                            ->rows(3),
                        Forms\Components\Textarea::make('communication_style')
                            ->required()
                            ->rows(3),
                        Forms\Components\Textarea::make('leadership_style')
                            ->rows(3),
                        Forms\Components\Textarea::make('ideal_work_environment')
                            ->required()
                            ->rows(3),
                    ])->columns(2),
                
                Forms\Components\Section::make('Motivation & Stress')
                    ->schema([
                        Forms\Components\Textarea::make('motivation_factors')
                            ->required()
                            ->rows(3),
                        Forms\Components\Textarea::make('stress_factors')
                            ->required()
                            ->rows(3),
                    ])->columns(2),
                
                Forms\Components\Section::make('Compatibility')
                    ->schema([
                        Forms\Components\TagsInput::make('compatible_patterns')
                            ->label('Compatible Pattern Codes')
                            ->placeholder('Enter pattern codes (e.g., ANALYZER, CAPTAIN)')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('behavioral_drives.dominance')
                    ->label('Dom (A)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('behavioral_drives.extraversion')
                    ->label('Ext (B)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('behavioral_drives.patience')
                    ->label('Pat (C)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('behavioral_drives.formality')
                    ->label('For (D)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
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
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
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
            'index' => Pages\ListPiBehavioralPatterns::route('/'),
            'create' => Pages\CreatePiBehavioralPattern::route('/create'),
            'edit' => Pages\EditPiBehavioralPattern::route('/{record}/edit'),
        ];
    }
}
