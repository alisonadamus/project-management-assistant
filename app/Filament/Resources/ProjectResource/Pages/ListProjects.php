<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\ProjectResource\Pages;

use Alison\ProjectManagementAssistant\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
