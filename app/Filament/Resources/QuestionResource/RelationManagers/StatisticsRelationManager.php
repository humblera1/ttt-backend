<?php

namespace App\Filament\Resources\QuestionResource\RelationManagers;

use App\Enums\Period;
use App\Models\Statistic;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StatisticsRelationManager extends RelationManager
{
    protected static string $relationship = 'statistics';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('viewAny', Statistic::class);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('met_in_real_interview')
                    ->sortable()
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'met' : 'not met'),
                TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('position.name')
                    ->label('Position')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('when_asked')
                    ->label('Asked')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('user.username')
                    ->label('User')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('met_in_real_interview')
                    ->label('Met in real interview')
                    ->placeholder('All')
                    ->trueLabel('Met')
                    ->falseLabel('Not met')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->where('met_in_real_interview', true),
                        false: fn (Builder $query): Builder => $query->where('met_in_real_interview', false),
                        blank: fn (Builder $query): Builder => $query,
                    ),
                SelectFilter::make('when_asked')
                    ->options(Period::options()),
                TernaryFilter::make('company_id')
                    ->label('Company specified')
                    ->placeholder('All')
                    ->trueLabel('With company')
                    ->falseLabel('Without company')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereNotNull('company_id'),
                        false: fn (Builder $query): Builder => $query->whereNull('company_id'),
                        blank: fn (Builder $query): Builder => $query,
                    ),
                TernaryFilter::make('position_id')
                    ->label('Position specified')
                    ->placeholder('All')
                    ->trueLabel('With position')
                    ->falseLabel('Without position')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereNotNull('position_id'),
                        false: fn (Builder $query): Builder => $query->whereNull('position_id'),
                        blank: fn (Builder $query): Builder => $query,
                    ),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
