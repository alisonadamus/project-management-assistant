<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\TechnologyResource\RelationManagers;

use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $title = 'Проекти';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Назва')
                    ->required()
                    ->maxLength(248)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $state, Forms\Set $set) => $set('slug', Str::slug($state))),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(128)
                    ->unique(Project::class, 'slug', ignoreRecord: true),

                Forms\Components\Select::make('event_id')
                    ->label('Подія')
                    ->relationship('event', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('supervisor_id')
                    ->label('Керівник')
                    ->relationship('supervisor', 'id')
                    ->getOptionLabelFromRecordUsing(fn (Supervisor $record) => $record->user->name)
                    ->searchable()
                    ->preload(),

                Forms\Components\Textarea::make('body')
                    ->label('Опис')
                    ->maxLength(65535),
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

                Tables\Columns\TextColumn::make('event.name')
                    ->label('Подія')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('supervisor.user.name')
                    ->label('Керівник')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->label('Подія')
                    ->relationship('event', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('supervisor')
                    ->label('Керівник')
                    ->relationship('supervisor.user', 'name')
                    ->searchable()
                    ->preload(),
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
