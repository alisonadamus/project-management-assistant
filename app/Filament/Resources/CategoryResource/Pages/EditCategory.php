<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\CategoryResource\Pages;

use Alison\ProjectManagementAssistant\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
