<?php

namespace App\Filament\Resources;

use App\Filament\Actions\Delete\DeleteBulkAction;
use App\Filament\Actions\Delete\ForceDeleteBulkAction;
use App\Filament\Filters\Trash\TrashedFilter;
use App\Filament\Resources\NotificationCategoryResource\Pages;
use App\Filament\Resources\NotificationCategoryResource\RelationManagers\TypesRelationManager;
use App\Models\NotificationCategory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotificationCategoryResource extends Resource
{
    protected static ?string $model = NotificationCategory::class;

    protected static ?string $navigationGroup = 'Notifications';

    public static ?string $label = 'Categories';

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withTrashed();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->limit(50),
                TextColumn::make('created_at')
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->model(NotificationCategory::class),
                    ForceDeleteBulkAction::make()
                        ->model(NotificationCategory::class),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TypesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificationCategories::route('/'),
            'edit' => Pages\EditNotificationCategory::route('/{record}/edit'),
        ];
    }
}
