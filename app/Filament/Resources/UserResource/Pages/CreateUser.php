<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\UserResource\Pages;

use Alison\ProjectManagementAssistant\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
