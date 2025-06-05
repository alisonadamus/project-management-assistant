<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\SubeventResource\Pages;
use Alison\ProjectManagementAssistant\Filament\Resources\SubeventResource\RelationManagers;
use Alison\ProjectManagementAssistant\Models\Subevent;
use Alison\ProjectManagementAssistant\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubeventResource extends Resource
{
    protected static ?string $model = Subevent::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Управління подіями';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Підподія';

    protected static ?string $pluralModelLabel = 'Підподії';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основна інформація')
                    ->schema([
                        Forms\Components\Select::make('event_id')
                            ->label('Подія')
                            ->relationship('event', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('name')
                            ->label('Назва')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Опис')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Дати та час')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Дата початку')
                            ->required()
                            ->native(false),

                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('Дата завершення')
                            ->required()
                            ->native(false)
                            ->after('start_date'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Залежності та стилізація')
                    ->schema([
                        Forms\Components\Select::make('depends_on')
                            ->label('Залежить від підподії')
                            ->relationship('dependsOn', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\ColorPicker::make('bg_color')
                            ->label('Колір фону')
                            ->default('#3b82f6'),

                        Forms\Components\ColorPicker::make('fg_color')
                            ->label('Колір тексту')
                            ->default('#ffffff'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['event', 'dependsOn']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('event.name')
                    ->label('Подія')
                    ->sortable(),

                Tables\Columns\TextColumn::make('dependsOn.name')
                    ->label('Залежить від')
                    ->sortable()
                    ->placeholder('Немає залежності'),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Дата початку')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Дата завершення')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\ColorColumn::make('bg_color')
                    ->label('Колір фону'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Опис')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
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
                Tables\Filters\SelectFilter::make('event')
                    ->label('Подія')
                    ->relationship('event', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('depends_on')
                    ->label('Залежить від')
                    ->relationship('dependsOn', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('start_date')
                    ->label('Дата початку')
                    ->form([
                        Forms\Components\DatePicker::make('start_from')
                            ->label('Від'),
                        Forms\Components\DatePicker::make('start_until')
                            ->label('До'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['start_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_date', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubevents::route('/'),
            'create' => Pages\CreateSubevent::route('/create'),
            'view' => Pages\ViewSubevent::route('/{record}'),
            'edit' => Pages\EditSubevent::route('/{record}/edit'),
        ];
    }
}
