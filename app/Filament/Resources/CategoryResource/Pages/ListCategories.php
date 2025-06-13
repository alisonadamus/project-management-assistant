<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\CategoryResource\Pages;

use Alison\ProjectManagementAssistant\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
