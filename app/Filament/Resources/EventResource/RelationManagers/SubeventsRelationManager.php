<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\EventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubeventsRelationManager extends RelationManager
{
    protected static string $relationship = 'subevents';

    protected static ?string $title = 'Підподії';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Назва')
                    ->required()
                    ->maxLength(255),

                Forms\Components\MarkdownEditor::make('description')
                    ->label('Опис')
                    ->maxLength(65535),

                Forms\Components\DateTimePicker::make('start_date')
                    ->label('Дата початку')
                    ->required()
                    ->native(false),

                Forms\Components\DateTimePicker::make('end_date')
                    ->label('Дата завершення')
                    ->required()
                    ->native(false)
                    ->after('start_date'),

                Forms\Components\Select::make('depends_on')
                    ->label('Залежить від підподії')
                    ->options(function () {
                        $eventId = $this->getOwnerRecord()->id;
                        return \Alison\ProjectManagementAssistant\Models\Subevent::where('event_id', $eventId)
                            ->pluck('name', 'id');
                    })
                    ->searchable(),

                Forms\Components\ColorPicker::make('bg_color')
                    ->label('Колір фону')
                    ->default('#3b82f6'),

                Forms\Components\ColorPicker::make('fg_color')
                    ->label('Колір тексту')
                    ->default('#ffffff'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Дата початку')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Дата завершення')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('dependsOn.name')
                    ->label('Залежить від')
                    ->placeholder('Немає залежності'),

                Tables\Columns\ColorColumn::make('bg_color')
                    ->label('Колір фону'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Опис')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('depends_on')
                    ->label('Залежить від')
                    ->options(function () {
                        $eventId = $this->getOwnerRecord()->id;
                        return \Alison\ProjectManagementAssistant\Models\Subevent::where('event_id', $eventId)
                            ->pluck('name', 'id');
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
