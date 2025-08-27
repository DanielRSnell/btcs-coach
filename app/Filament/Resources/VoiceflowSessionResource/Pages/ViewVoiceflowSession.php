<?php

namespace App\Filament\Resources\VoiceflowSessionResource\Pages;

use App\Filament\Resources\VoiceflowSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewVoiceflowSession extends ViewRecord
{
    protected static string $resource = VoiceflowSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Session Overview')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('User')
                            ->icon('heroicon-m-user'),
                        
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email')
                            ->icon('heroicon-m-at-symbol'),
                        
                        Infolists\Components\TextEntry::make('session_id')
                            ->label('Voiceflow User ID')
                            ->copyable()
                            ->icon('heroicon-m-identification'),
                        
                        Infolists\Components\TextEntry::make('project_id')
                            ->label('Project ID (localStorage key)')
                            ->copyable()
                            ->icon('heroicon-m-key'),
                        
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'ACTIVE' => 'success',
                                'CHATTING' => 'warning',
                                'UPDATED' => 'primary',
                                'INACTIVE' => 'secondary',
                                'COMPLETED' => 'success',
                                default => 'secondary',
                            }),
                        
                        Infolists\Components\TextEntry::make('source')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'localStorage_sync' => 'primary',
                                'migrated_from_json' => 'secondary',
                                'migrated_from_legacy_json' => 'warning',
                                'manual' => 'success',
                                default => 'secondary',
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Session Data')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('value_data')
                            ->label('Session Value Data')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\TextEntry::make('session_created_at')
                            ->label('Session Created')
                            ->dateTime('M j, Y g:i A')
                            ->icon('heroicon-m-calendar-days'),
                        
                        Infolists\Components\TextEntry::make('session_updated_at')
                            ->label('Last Activity')
                            ->dateTime('M j, Y g:i A')
                            ->icon('heroicon-m-clock'),
                        
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Recorded At')
                            ->dateTime('M j, Y g:i A')
                            ->icon('heroicon-m-server'),
                        
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Record Updated')
                            ->dateTime('M j, Y g:i A')
                            ->icon('heroicon-m-arrow-path'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Session Statistics')
                    ->schema([
                        Infolists\Components\TextEntry::make('data_size')
                            ->label('Data Size')
                            ->state(function ($record): string {
                                return number_format($record->getDataSize()) . ' bytes';
                            })
                            ->icon('heroicon-m-document-text'),
                        
                        Infolists\Components\TextEntry::make('is_active')
                            ->label('Is Active')
                            ->state(function ($record): string {
                                return $record->isActive() ? 'Yes' : 'No';
                            })
                            ->badge()
                            ->color(fn (string $state): string => $state === 'Yes' ? 'success' : 'secondary')
                            ->icon('heroicon-m-signal'),
                    ])
                    ->columns(2),
            ]);
    }
}