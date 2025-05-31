<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\EventResource\RelationManagers;

use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SupervisorsRelationManager extends RelationManager
{
    protected static string $relationship = 'supervisors';

    protected static ?string $title = 'Керівники';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Користувач')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('slot_count')
                    ->label('Кількість місць')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                Forms\Components\Textarea::make('note')
                    ->label('Примітка')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Користувач')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slot_count')
                    ->label('Кількість місць')
                    ->sortable(),

                Tables\Columns\TextColumn::make('note')
                    ->label('Примітка')
                    ->limit(50),

                Tables\Columns\TextColumn::make('projects_count')
                    ->label('Кількість проектів')
                    ->counts('projects')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->label('Користувач')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('with_projects')
                    ->label('З проектами')
                    ->query(fn (Builder $query): Builder => $query->has('projects')),
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
