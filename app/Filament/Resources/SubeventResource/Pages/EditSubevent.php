<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\SubeventResource\Pages;

use Alison\ProjectManagementAssistant\Filament\Resources\SubeventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubevent extends EditRecord
{
    protected static string $resource = SubeventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
