<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\PermissionResource\Pages;

use Alison\ProjectManagementAssistant\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
