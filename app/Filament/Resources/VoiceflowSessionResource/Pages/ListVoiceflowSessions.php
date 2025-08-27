<?php

namespace App\Filament\Resources\VoiceflowSessionResource\Pages;

use App\Filament\Resources\VoiceflowSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVoiceflowSessions extends ListRecords
{
    protected static string $resource = VoiceflowSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
