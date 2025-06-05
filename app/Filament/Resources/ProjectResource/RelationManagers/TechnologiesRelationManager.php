<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\ProjectResource\RelationManagers;

use Alison\ProjectManagementAssistant\Models\Technology;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TechnologiesRelationManager extends RelationManager
{
    protected static string $relationship = 'technologies';

    protected static ?string $title = 'Технології';

    public function form(Form $form): Form
    {
        return $form
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

                Forms\Components\MarkdownEditor::make('description')
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

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('link')
                    ->label('Посилання')
                    ->url()
                    ->openUrlInNewTab(),

                Tables\Columns\ImageColumn::make('image')
                    ->label('Зображення')
                    ->circular(),

                Tables\Columns\TextColumn::make('projects_count')
                    ->label('Кількість проектів')
                    ->counts('projects')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('with_link')
                    ->label('З посиланням')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('link')->where('link', '!=', '')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}