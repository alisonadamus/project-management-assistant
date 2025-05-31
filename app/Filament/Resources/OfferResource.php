<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\OfferResource\Pages;
//use Alison\ProjectManagementAssistant\Filament\Resources\OfferResource\RelationManagers;
use Alison\ProjectManagementAssistant\Models\Offer;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    protected static ?string $navigationGroup = 'Управління проектами';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Пропозиція';

    protected static ?string $pluralModelLabel = 'Пропозиції';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Деталі пропозиції')
                    ->schema([
                        Forms\Components\Select::make('project_id')
                            ->label('Проект')
                            ->relationship('project', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('student_id')
                            ->label('Студент')
                            ->relationship('student', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Проект')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.name')
                    ->label('Студент')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('project.event.name')
                    ->label('Подія')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('project.supervisor.user.name')
                    ->label('Керівник')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project')
                    ->label('Проект')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('student')
                    ->label('Студент')
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('event')
                    ->label('Подія')
                    ->relationship('project.event', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('created_at')
                    ->label('Дата створення')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Від'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('До'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOffers::route('/'),
            'create' => Pages\CreateOffer::route('/create'),
            'edit' => Pages\EditOffer::route('/{record}/edit'),
        ];
    }
}
