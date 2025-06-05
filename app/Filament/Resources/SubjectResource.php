<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\SubjectResource\Pages;
use Alison\ProjectManagementAssistant\Filament\Resources\SubjectResource\RelationManagers;
use Alison\ProjectManagementAssistant\Models\Subject;
use Alison\ProjectManagementAssistant\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Управління навчанням';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Предмет';

    protected static ?string $pluralModelLabel = 'Предмети';

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
                            ->maxLength(72)
                            ->unique(Subject::class, 'slug', ignoreRecord: true),

                        Forms\Components\Select::make('course_number')
                            ->label('Курс')
                            ->options([
                                1 => '1 курс',
                                2 => '2 курс',
                                3 => '3 курс',
                                4 => '4 курс',
                            ])
                            ->required(),
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
                            ->directory('subjects')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Категорії')
                    ->schema([
                        Forms\Components\CheckboxList::make('categories')
                            ->label('Категорії')
                            ->relationship('categories', 'name')
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

                Tables\Columns\TextColumn::make('course_number')
                    ->label('Курс')
                    ->formatStateUsing(fn (int $state): string => "{$state} курс")
                    ->sortable(),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Категорії')
                    ->badge()
                    ->searchable(),

                Tables\Columns\ImageColumn::make('image')
                    ->label('Зображення')
                    ->circular(),

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
                Tables\Filters\SelectFilter::make('course_number')
                    ->label('Курс')
                    ->options([
                        1 => '1 курс',
                        2 => '2 курс',
                        3 => '3 курс',
                        4 => '4 курс',
                    ]),

                Tables\Filters\SelectFilter::make('categories')
                    ->label('Категорія')
                    ->relationship('categories', 'name')
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
            RelationManagers\CategoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}
