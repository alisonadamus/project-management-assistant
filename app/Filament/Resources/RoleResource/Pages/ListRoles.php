<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\RoleResource\Pages;

use Alison\ProjectManagementAssistant\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
