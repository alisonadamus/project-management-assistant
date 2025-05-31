<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\RoleResource\Pages;
use Alison\ProjectManagementAssistant\Filament\Resources\RoleResource\RelationManagers;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Адміністрування';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Роль';

    protected static ?string $pluralModelLabel = 'Ролі';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Деталі ролі')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Назва')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('guard_name')
                            ->label('Guard Name')
                            ->default('web')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Дозволи')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->label('Дозволи')
                            ->relationship('permissions', 'name')
                            ->searchable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Guard Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('permissions.name')
                    ->label('Дозволи')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Кількість користувачів')
                    ->counts('users')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function getRelations(): array
    {
        return [
            RelationManagers\PermissionsRelationManager::class,
            RelationManagers\UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
