<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\TechnologyResource\Pages;

use Alison\ProjectManagementAssistant\Filament\Resources\TechnologyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTechnologies extends ListRecords
{
    protected static string $resource = TechnologyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
