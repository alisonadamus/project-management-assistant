<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\TechnologyResource\Pages;
use Alison\ProjectManagementAssistant\Filament\Resources\TechnologyResource\RelationManagers;
use Alison\ProjectManagementAssistant\Models\Technology;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TechnologyResource extends Resource
{
    protected static ?string $model = Technology::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationGroup = 'Управління проектами';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Технологія';

    protected static ?string $pluralModelLabel = 'Технології';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основна інформація')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Назва')
                            ->required()
                            ->maxLength(128)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $state, Forms\Set $set) => $set('slug', Str::slug($state))),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(128)
                            ->unique(Technology::class, 'slug', ignoreRecord: true),

                        Forms\Components\TextInput::make('link')
                            ->label('Посилання')
                            ->url()
                            ->maxLength(2048),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Додаткова інформація')
                    ->schema([
                        Forms\Components\MarkdownEditor::make('description')
                            ->label('Опис')
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('image')
                            ->label('Зображення')
                            ->image()
                            ->directory('technologies')
                            ->maxSize(2048)
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

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('link')
                    ->label('Посилання')
                    ->url(fn (Technology $record): string => $record->link)
                    ->openUrlInNewTab(),

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
                Tables\Filters\Filter::make('with_link')
                    ->label('З посиланням')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('link')->where('link', '!=', '')),

                Tables\Filters\Filter::make('with_projects')
                    ->label('З проектами')
                    ->query(fn (Builder $query): Builder => $query->has('projects')),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTechnologies::route('/'),
            'create' => Pages\CreateTechnology::route('/create'),
            'edit' => Pages\EditTechnology::route('/{record}/edit'),
        ];
    }
}
