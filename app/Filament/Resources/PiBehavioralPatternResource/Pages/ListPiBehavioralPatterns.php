<?php

namespace App\Filament\Resources\PiBehavioralPatternResource\Pages;

use App\Filament\Resources\PiBehavioralPatternResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPiBehavioralPatterns extends ListRecords
{
    protected static string $resource = PiBehavioralPatternResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
