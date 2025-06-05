<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\ProjectResource\Pages;
use Alison\ProjectManagementAssistant\Filament\Resources\ProjectResource\RelationManagers;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\User;
use Alison\ProjectManagementAssistant\Models\Technology;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Управління проектами';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Проект';

    protected static ?string $pluralModelLabel = 'Проекти';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основна інформація')
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
                            ->getOptionLabelFromRecordUsing(fn (Supervisor $record) => $record->user->full_name)
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('assigned_to')
                            ->label('Призначено')
                            ->relationship('assignedTo', 'id')
                            ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name)
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Додаткова інформація')
                    ->schema([
                        Forms\Components\Textarea::make('appendix')
                            ->label('Додаток')
                            ->maxLength(512),

                        Forms\Components\MarkdownEditor::make('body')
                            ->label('Опис')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Технології')
                    ->schema([
                        Forms\Components\CheckboxList::make('technologies')
                            ->label('Технології')
                            ->relationship('technologies', 'name')
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

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('event.name')
                    ->label('Подія')
                    ->searchable()
                    ->sortable(),

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

                Tables\Columns\TextColumn::make('offers_count')
                    ->label('Кількість заявок')
                    ->counts('offers')
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
                Tables\Filters\SelectFilter::make('event')
                    ->label('Подія')
                    ->relationship('event', 'name')
                    ->searchable()
                    ->preload(),

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
            RelationManagers\TechnologiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}