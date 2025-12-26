<?php

namespace App\Filament\Resources\NotificationCategoryResource\RelationManagers;

use App\Filament\Filters\Trash\TrashedFilter;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TypesRelationManager extends RelationManager
{
    protected static string $relationship = 'types';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('key')
                    ->searchable()
                    ->badge(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->wrap(),
                TextColumn::make('created_at')
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
