<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\EventResource\Pages;
use Alison\ProjectManagementAssistant\Filament\Resources\EventResource\RelationManagers;
use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Управління подіями';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Подія';

    protected static ?string $pluralModelLabel = 'Події';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основна інформація')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Назва')
                            ->required()
                            ->maxLength(128),

                        Forms\Components\Select::make('category_id')
                            ->label('Категорія')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Дата початку')
                            ->required(),

                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('Дата завершення')
                            ->after('start_date'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Додаткова інформація')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Опис')
                            ->maxLength(512)
                            ->columnSpanFull(),

                        Forms\Components\ColorPicker::make('bg_color')
                            ->label('Колір фону')
                            ->rgba(),

                        Forms\Components\ColorPicker::make('fg_color')
                            ->label('Колір тексту')
                            ->rgba(),

                        Forms\Components\FileUpload::make('image')
                            ->label('Зображення')
                            ->image()
                            ->directory('events')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
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

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Категорія')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Початок')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Завершення')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\ColorColumn::make('bg_color')
                    ->label('Колір фону'),

                Tables\Columns\ColorColumn::make('fg_color')
                    ->label('Колір тексту')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\ImageColumn::make('image')
                    ->label('Зображення')
                    ->circular(),

                Tables\Columns\TextColumn::make('projects_count')
                    ->label('Кількість проектів')
                    ->counts('projects')
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
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Категорія')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('active')
                    ->label('Активні')
                    ->query(fn (Builder $query): Builder => $query->where('end_date', '>=', now())),

                Tables\Filters\Filter::make('past')
                    ->label('Минулі')
                    ->query(fn (Builder $query): Builder => $query->where('end_date', '<', now())),

                Tables\Filters\Filter::make('upcoming')
                    ->label('Майбутні')
                    ->query(fn (Builder $query): Builder => $query->where('start_date', '>', now())),

                Tables\Filters\Filter::make('date_range')
                    ->label('Діапазон дат')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('З')
                            ->default(now()->subMonth()),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('По')
                            ->default(now()->addMonth()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['end_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date),
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
            RelationManagers\ProjectsRelationManager::class,
            RelationManagers\SupervisorsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
