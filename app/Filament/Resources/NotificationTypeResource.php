<?php

namespace App\Filament\Resources;

use App\Filament\Actions\Delete\DeleteBulkAction;
use App\Filament\Actions\Delete\ForceDeleteBulkAction;
use App\Filament\Filters\Trash\TrashedFilter;
use App\Filament\Resources\NotificationTypeResource\Pages;
use App\Models\NotificationType;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotificationTypeResource extends Resource
{
    protected static ?string $model = NotificationType::class;

    protected static ?string $navigationGroup = 'Notifications';

    public static ?string $label = 'Types';

    protected static ?string $navigationIcon = 'heroicon-o-stop';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('category')
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
                    ->searchable()
                    ->badge(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->wrap(),
                TextColumn::make('category.key')
                    ->badge()
                    ->color('info'),
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
                        ->model(NotificationType::class),
                    ForceDeleteBulkAction::make()
                        ->model(NotificationType::class),
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
            'index' => Pages\ListNotificationTypes::route('/'),
            'edit' => Pages\EditNotificationType::route('/{record}/edit'),
        ];
    }
}
