<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\EventResource\RelationManagers;

use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\Technology;
use Alison\ProjectManagementAssistant\Models\User;
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

                Forms\Components\Select::make('supervisor_id')
                    ->label('Керівник')
                    ->relationship('supervisor', 'id')
                    ->getOptionLabelFromRecordUsing(fn (Supervisor $record) => $record->user->full_name)
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('assigned_to')
                    ->label('Призначено')
                    ->relationship('assignedTo', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                    ->searchable()
                    ->preload(),

                Forms\Components\Textarea::make('appendix')
                    ->label('Додаток')
                    ->maxLength(512),

                Forms\Components\MarkdownEditor::make('body')
                    ->label('Опис')
                    ->maxLength(65535),

                Forms\Components\CheckboxList::make('technologies')
                    ->label('Технології')
                    ->relationship('technologies', 'name')
                    ->searchable()
                    ->columnSpanFull(),
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

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('supervisor.user.full_name')
                    ->label('Керівник')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->supervisor?->user?->full_name),

                Tables\Columns\TextColumn::make('assignedTo.full_name')
                    ->label('Призначено')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->assignedTo?->full_name),

                Tables\Columns\TextColumn::make('technologies.name')
                    ->label('Технології')
                    ->badge()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('supervisor')
                    ->label('Керівник')
                    ->relationship('supervisor.user', 'id')
                    ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name)
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('Призначено')
                    ->relationship('assignedTo', 'id')
                    ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name)
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('technologies')
                    ->label('Технологія')
                    ->relationship('technologies', 'name')
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
