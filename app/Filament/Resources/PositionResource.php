<?php

namespace App\Filament\Resources;

use App\Enums\Status;
use App\Filament\Actions\Table\Status\Shared\{ApproveAction, RejectAction, ResetAction};
use App\Filament\Filters\Status\StatusFilter;
use App\Filament\Filters\Trash\TrashedFilter;
use App\Filament\Resources\PositionResource\Pages;
use App\Models\Position;
use Exception;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\{DeleteAction, DeleteBulkAction, EditAction, RestoreAction};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PositionResource extends Resource
{
    protected static ?string $model = Position::class;

    protected static ?string $navigationGroup = 'Main';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

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

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => Status::colorByValue($state)),
                TextColumn::make('created_at')
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                StatusFilter::make(),
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    ApproveAction::make(),
                    RejectAction::make(),
                    ResetAction::make(),
                ]),
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (Position $record) => !$record->trashed() && auth()->user()->can('delete', $record)),
                RestoreAction::make()
                    ->visible(fn (Position $record) => $record->trashed() && auth()->user()->can('restore', $record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (Position $record) => auth()->user()->can('bulkDelete', $record)),
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
            'index' => Pages\ListPositions::route('/'),
            'create' => Pages\CreatePosition::route('/create'),
            'edit' => Pages\EditPosition::route('/{record}/edit'),
        ];
    }
}
