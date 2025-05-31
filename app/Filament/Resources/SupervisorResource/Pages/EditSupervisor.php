<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\SupervisorResource\Pages;

use Alison\ProjectManagementAssistant\Filament\Resources\SupervisorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSupervisor extends EditRecord
{
    protected static string $resource = SupervisorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
