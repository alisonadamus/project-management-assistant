<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\TechnologyResource\Pages;

use Alison\ProjectManagementAssistant\Filament\Resources\TechnologyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTechnology extends EditRecord
{
    protected static string $resource = TechnologyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
