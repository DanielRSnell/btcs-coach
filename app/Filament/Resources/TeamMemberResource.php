<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamMemberResource\Pages;
use App\Filament\Resources\TeamMemberResource\RelationManagers;
use App\Models\TeamMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'Team Members';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employee_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('employee_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('employee_email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('job')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('job_code')
                    ->maxLength(255),
                Forms\Components\TextInput::make('org_level_2')
                    ->maxLength(255),
                Forms\Components\TextInput::make('employment_status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee_email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('employee_number')
                    ->label('Emp #')
                    ->searchable(),
                Tables\Columns\TextColumn::make('job')
                    ->label('Job Title')
                    ->searchable()
                    ->wrap()
                    ->limit(40),
                Tables\Columns\TextColumn::make('org_level_2')
                    ->label('Org Level 2')
                    ->searchable()
                    ->wrap()
                    ->limit(30),
                Tables\Columns\TextColumn::make('employment_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Active' => 'success',
                        'Leave of absence' => 'warning',
                        'Inactive' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('user_id')
                    ->label('Has Account')
                    ->boolean()
                    ->getStateUsing(fn($record) => $record->user_id !== null),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employment_status')
                    ->label('Employment Status')
                    ->options([
                        'Active' => 'Active',
                        'Leave of absence' => 'Leave of absence',
                        'Inactive' => 'Inactive',
                    ]),
                Tables\Filters\SelectFilter::make('org_level_2')
                    ->label('Org Level 2')
                    ->options(fn() => TeamMember::query()
                        ->whereNotNull('org_level_2')
                        ->distinct()
                        ->pluck('org_level_2', 'org_level_2')
                        ->sort()),
                Tables\Filters\Filter::make('has_account')
                    ->label('Has Account')
                    ->query(fn(Builder $query) => $query->whereNotNull('user_id')),
                Tables\Filters\Filter::make('no_account')
                    ->label('No Account')
                    ->query(fn(Builder $query) => $query->whereNull('user_id')),
            ])
            ->actions([
                Tables\Actions\Action::make('create_user')
                    ->label('Create User')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->visible(fn($record) => !$record->hasAccount())
                    ->requiresConfirmation()
                    ->modalHeading('Create User Account')
                    ->modalDescription(fn($record) => "Create a user account for {$record->name} ({$record->employee_email})?")
                    ->action(function ($record) {
                        try {
                            $user = $record->createUser();

                            Notification::make()
                                ->success()
                                ->title('User created successfully')
                                ->body("{$user->name} has been created with default password: Welcome2024!")
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Error creating user')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('view_user')
                    ->label('View User')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->visible(fn($record) => $record->hasAccount())
                    ->url(fn($record) => $record->user_id
                        ? route('filament.admin.resources.users.edit', ['record' => $record->user_id])
                        : null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('create_users')
                        ->label('Create Users')
                        ->icon('heroicon-o-user-plus')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Create User Accounts')
                        ->modalDescription('Create user accounts for all selected team members who don\'t have accounts yet?')
                        ->action(function ($records) {
                            $created = 0;
                            $skipped = 0;

                            foreach ($records as $record) {
                                if ($record->hasAccount()) {
                                    $skipped++;
                                    continue;
                                }

                                try {
                                    $record->createUser();
                                    $created++;
                                } catch (\Exception $e) {
                                    \Log::error('Error creating user: ' . $e->getMessage());
                                    $skipped++;
                                }
                            }

                            if ($created > 0) {
                                Notification::make()
                                    ->success()
                                    ->title("Created {$created} user(s)")
                                    ->body($skipped > 0 ? "Skipped {$skipped} users" : null)
                                    ->send();
                            } else {
                                Notification::make()
                                    ->warning()
                                    ->title('No users created')
                                    ->body("All {$skipped} selected users already have accounts or encountered errors")
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->defaultSort('first_name', 'asc');
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
            'index' => Pages\ListTeamMembers::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
