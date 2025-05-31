<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\CategoryResource\Pages;
use Alison\ProjectManagementAssistant\Filament\Resources\CategoryResource\RelationManagers;
use Alison\ProjectManagementAssistant\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Управління навчанням';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Категорія';

    protected static ?string $pluralModelLabel = 'Категорії';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основна інформація')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Назва')
                            ->required()
                            ->maxLength(32),

                        Forms\Components\Select::make('course_number')
                            ->label('Курс')
                            ->options([
                                1 => '1 курс',
                                2 => '2 курс',
                                3 => '3 курс',
                                4 => '4 курс',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('freezing_period')
                            ->label('Період заморожування (днів)')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        Forms\Components\TextInput::make('period')
                            ->label('Період (днів)')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Додаткова інформація')
                    ->schema([
                        Forms\Components\Repeater::make('attachments')
                            ->label('Додатки')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Назва')
                                    ->required(),

                                Forms\Components\TextInput::make('url')
                                    ->label('URL')
                                    ->url()
                                    ->required(),
                            ])
                            ->columns(2),
                    ]),

                Forms\Components\Section::make('Предмети')
                    ->schema([
                        Forms\Components\CheckboxList::make('subjects')
                            ->label('Предмети')
                            ->relationship('subjects', 'name')
                            ->searchable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('course_number')
                    ->label('Курс')
                    ->formatStateUsing(fn(int $state): string => "{$state} курс")
                    ->sortable(),

                Tables\Columns\TextColumn::make('freezing_period')
                    ->label('Період заморожування')
                    ->formatStateUsing(fn(int $state): string => "{$state} днів")
                    ->sortable(),

                Tables\Columns\TextColumn::make('period')
                    ->label('Період')
                    ->formatStateUsing(fn(int $state): string => "{$state} днів")
                    ->sortable(),

                Tables\Columns\TextColumn::make('subjects.name')
                    ->label('Предмети')
                    ->badge()
                    ->searchable(),

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
                Tables\Filters\SelectFilter::make('course_number')
                    ->label('Курс')
                    ->options([
                        1 => '1 курс',
                        2 => '2 курс',
                        3 => '3 курс',
                        4 => '4 курс',
                    ]),

                Tables\Filters\Filter::make('freezing_period')
                    ->label('Період заморожування')
                    ->form([
                        Forms\Components\TextInput::make('min_freezing_period')
                            ->label('Мінімум днів')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_freezing_period')
                            ->label('Максимум днів')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_freezing_period'],
                                fn(Builder $query, $min): Builder => $query->where('freezing_period', '>=', $min),
                            )
                            ->when(
                                $data['max_freezing_period'],
                                fn(Builder $query, $max): Builder => $query->where('freezing_period', '<=', $max),
                            );
                    }),

                Tables\Filters\Filter::make('period')
                    ->label('Період')
                    ->form([
                        Forms\Components\TextInput::make('min_period')
                            ->label('Мінімум днів')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_period')
                            ->label('Максимум днів')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_period'],
                                fn(Builder $query, $min): Builder => $query->where('period', '>=', $min),
                            )
                            ->when(
                                $data['max_period'],
                                fn(Builder $query, $max): Builder => $query->where('period', '<=', $max),
                            );
                    }),
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
            RelationManagers\SubjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
