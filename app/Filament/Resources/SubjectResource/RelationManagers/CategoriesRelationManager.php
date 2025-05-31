<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\SubjectResource\RelationManagers;

use Alison\ProjectManagementAssistant\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'categories';

    protected static ?string $title = 'Категорії';

    public function form(Form $form): Form
    {
        return $form
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

                Tables\Columns\TextColumn::make('course_number')
                    ->label('Курс')
                    ->formatStateUsing(fn (int $state): string => "{$state} курс")
                    ->sortable(),

                Tables\Columns\TextColumn::make('freezing_period')
                    ->label('Період заморожування')
                    ->formatStateUsing(fn (int $state): string => "{$state} днів")
                    ->sortable(),

                Tables\Columns\TextColumn::make('period')
                    ->label('Період')
                    ->formatStateUsing(fn (int $state): string => "{$state} днів")
                    ->sortable(),
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
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
