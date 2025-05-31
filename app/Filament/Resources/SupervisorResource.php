<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\SupervisorResource\Pages;
use Alison\ProjectManagementAssistant\Filament\Resources\SupervisorResource\RelationManagers;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupervisorResource extends Resource
{
    protected static ?string $model = Supervisor::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Управління проектами';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Керівник';

    protected static ?string $pluralModelLabel = 'Керівники';

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
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Додаткова інформація')
                    ->schema([
                        Forms\Components\Textarea::make('note')
                            ->label('Примітка')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event.name')
                    ->label('Подія')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Користувач')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
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

                Tables\Filters\SelectFilter::make('user')
                    ->label('Користувач')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('active_event')
                    ->label('Активні події')
                    ->query(fn (Builder $query): Builder => $query->whereHas('event', function ($q) {
                        $q->where('end_date', '>=', now());
                    })),

                Tables\Filters\Filter::make('slot_count')
                    ->label('Кількість місць')
                    ->form([
                        Forms\Components\TextInput::make('min_slot_count')
                            ->label('Мінімум місць')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_slot_count')
                            ->label('Максимум місць')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_slot_count'],
                                fn (Builder $query, $min): Builder => $query->where('slot_count', '>=', $min),
                            )
                            ->when(
                                $data['max_slot_count'],
                                fn (Builder $query, $max): Builder => $query->where('slot_count', '<=', $max),
                            );
                    }),
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
            'index' => Pages\ListSupervisors::route('/'),
            'create' => Pages\CreateSupervisor::route('/create'),
            'edit' => Pages\EditSupervisor::route('/{record}/edit'),
        ];
    }
}