<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\EventResource\Pages;

use Alison\ProjectManagementAssistant\Filament\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
