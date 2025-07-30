<?php

namespace App\Filament\Resources\CoachingSessionResource\Pages;

use App\Filament\Resources\CoachingSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCoachingSession extends EditRecord
{
    protected static string $resource = CoachingSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
