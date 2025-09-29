<?php

namespace App\Filament\Resources;

use App\Filament\Actions\Delete\DeleteBulkAction;
use App\Filament\Actions\Delete\ForceDeleteBulkAction;
use App\Filament\Actions\Status\{ApproveAction, RejectAction, ResetAction};
use App\Filament\Columns\Status\StatusColumn;
use App\Filament\Filters\Status\StatusFilter;
use App\Filament\Filters\Trash\TrashedFilter;
use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\{DeleteAction, EditAction, RestoreAction};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationGroup = 'Main';

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

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
                TextColumn::make('name')
                    ->searchable(),
                StatusColumn::make(),
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
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->model(Company::class),
                    ForceDeleteBulkAction::make()
                        ->model(Company::class),
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
