<?php

namespace App\Filament\Resources;

use App\Filament\Actions\Delete\DeleteBulkAction;
use App\Filament\Actions\Delete\ForceDeleteBulkAction;
use App\Filament\Filters\Trash\TrashedFilter;
use App\Filament\Resources\UserNotificationResource\Pages;
use App\Filament\Resources\UserNotificationResource\RelationManagers;
use App\Models\UserNotification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserNotificationResource extends Resource
{
    protected static ?string $model = UserNotification::class;

    protected static ?string $navigationGroup = 'Notifications';

    public static ?string $label = 'Notifications';

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'type'])
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
                TextColumn::make('user.username')
                    ->searchable()
                    ->icon('heroicon-m-user')
                    ->iconColor('primary')
                    ->wrap(),
                TextColumn::make('type.name')
                    ->searchable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('title')
                    ->html()
                    ->wrap(),
                TextColumn::make('is_read')
                    ->label('Is Read?')
                    ->badge()
                    ->state(fn (UserNotification $record) => $record->read_at ? 'yes' : 'no')
                    ->color(fn (UserNotification $record) => $record->read_at ? 'success' : 'danger')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable()
                    ->since(),
                TextColumn::make('read_at')
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship(
                        'user',
                        'username',
                    )
                    ->searchable(),
                Tables\Filters\SelectFilter::make('type')
                    ->relationship(
                        'type',
                        'name',
                    ),
                TrashedFilter::make(),
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label(__('Is Read'))
                    ->placeholder(__('All'))
                    ->trueLabel(__('Yes'))
                    ->falseLabel(__('No'))
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereNotNull('read_at'),
                        false: fn (Builder $query): Builder => $query,
                        blank: fn (Builder $query): Builder => $query->whereNull('read_at'),
                    ),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_at'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when(
                            $data['created_at'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '=', $date),
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->recordUrl(
                fn (UserNotification $record): string => static::getUrl('view', ['record' => $record])
            )
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->model(UserNotification::class),
                    ForceDeleteBulkAction::make()
                        ->model(UserNotification::class),
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
            'index' => Pages\ListUserNotifications::route('/'),
            'create' => Pages\CreateUserNotification::route('/create'),
            'edit' => Pages\EditUserNotification::route('/{record}/edit'),
            'view' => Pages\ViewUserNotification::route('/{record}'),
        ];
    }
}
