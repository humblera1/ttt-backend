<?php

namespace App\Filament\Resources;

use App\Enums\Role;
use App\Filament\Actions\Ban\BanBulkAction;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Scopes\User\NotBannedScope;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Users';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([NotBannedScope::class])
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
            ->modifyQueryUsing(fn (Builder $query) => $query->with('roles'))
            ->columns([
                TextColumn::make('username')
                    ->searchable(),
                TextColumn::make('full_name')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function ($query) use ($search) {
                            $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                        });
                    })
                    ->label('Name'),
                TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-m-envelope')
                    ->iconColor('primary'),
                TextColumn::make('roles')
                    ->getStateUsing(fn (User $record) => $record->getRoleNames()->toArray())
                    ->badge()
                    ->color(fn (string $state): string => Role::colorByValue($state)),
                TextColumn::make('created_at')
                    ->sortable()
                    ->since()
                    ->dateTooltip(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->relationship('roles', 'name')
                    ->preload(),
                TernaryFilter::make('banned_at')
                    ->label('Ban')
                    ->placeholder('Unbanned')
                    ->trueLabel('Banned')
                    ->falseLabel('All')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereNotNull('banned_at'),
                        false: fn (Builder $query): Builder => $query,
                        blank: fn (Builder $query): Builder => $query->whereNull('banned_at')
                    ),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (User $record) => !$record->trashed() && auth()->user()->can('delete', $record)),
                RestoreAction::make()
                    ->visible(fn (User $record) => $record->trashed() && auth()->user()->can('restore', $record)),
            ])
            ->headerActions([
                // ...
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasPermissionTo('delete-bulk-user')),
                    BanBulkAction::make()
                        ->visible(fn () => auth()->user()->hasPermissionTo('ban-bulk-user')),
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
            'index' => Pages\ListUsers::route('/'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
