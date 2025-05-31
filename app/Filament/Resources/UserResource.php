<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\UserResource\Pages;
use Alison\ProjectManagementAssistant\Filament\Resources\UserResource\RelationManagers;
use Alison\ProjectManagementAssistant\Models\User;
use Spatie\Permission\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Адміністрування';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Користувач';

    protected static ?string $pluralModelLabel = 'Користувачі';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основна інформація')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Логін')
                            ->required()
                            ->maxLength(32)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('password')
                            ->label('Пароль')
                            ->password()
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Персональні дані')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label('Імя')
                            ->required()
                            ->maxLength(50),

                        Forms\Components\TextInput::make('last_name')
                            ->label('Прізвище')
                            ->required()
                            ->maxLength(50),

                        Forms\Components\TextInput::make('middle_name')
                            ->label('По батькові')
                            ->maxLength(50),

                        Forms\Components\Select::make('course_number')
                            ->label('Курс')
                            ->options([
                                1 => '1 курс',
                                2 => '2 курс',
                                3 => '3 курс',
                                4 => '4 курс',
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Додаткова інформація')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Опис')
                            ->maxLength(512)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('avatar')
                            ->label('Аватар')
                            ->image()
                            ->directory('avatars')
                            ->maxSize(1024)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Ролі')
                    ->schema([
                        Forms\Components\CheckboxList::make('roles')
                            ->label('Ролі')
                            ->relationship('roles', 'name')
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
                    ->label('Логін')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('first_name')
                    ->label('Імя')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_name')
                    ->label('Прізвище')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('course_number')
                    ->label('Курс')
                    ->formatStateUsing(fn (?int $state): ?string => $state ? "{$state} курс" : null),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Ролі')
                    ->badge()
                    ->searchable(),

                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Аватар')
                    ->circular(),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Верифіковано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Роль')
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('course_number')
                    ->label('Курс')
                    ->options([
                        1 => '1 курс',
                        2 => '2 курс',
                        3 => '3 курс',
                        4 => '4 курс',
                    ]),

                Tables\Filters\Filter::make('verified')
                    ->label('Верифіковані')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),

                Tables\Filters\Filter::make('unverified')
                    ->label('Неверифіковані')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
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
            RelationManagers\RolesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
