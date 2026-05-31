<?php

namespace App\Filament\Resources;

use App\Enums\Suggestion\Status;
use App\Filament\Actions\Table\Status\Suggestion\ApproveCompanyAction;
use App\Filament\Actions\Table\Status\Shared\RejectAction;
use App\Filament\Actions\Table\Status\Shared\ResetAction;
use App\Filament\Actions\Table\Status\Suggestion\ReturnCompanyAction;
use App\Filament\Resources\QuestionCompanySuggestionResource\Pages;
use App\Filament\Resources\QuestionCompanySuggestionResource\RelationManagers;
use App\Filament\Resources\QuestionResource\RelationManagers\CompanySuggestionRelationManager;
use App\Models\QuestionCompanySuggestion;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuestionCompanySuggestionResource extends Resource
{
    protected static ?string $model = QuestionCompanySuggestion::class;

    public static ?string $label = 'Companies';

    protected static ?string $navigationGroup = 'Moderation';

    protected static ?string $navigationIcon = 'heroicon-o-link';

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
                TextColumn::make('question.title')
                    ->searchable()
                    ->url(fn (QuestionCompanySuggestion $suggestion): string => "questions/$suggestion->question_id/edit")
                    ->limit(50),
                TextColumn::make('company.name')
                    ->searchable()
                    ->url(fn (QuestionCompanySuggestion $suggestion): string => "companies/$suggestion->company_id/edit")
                    ->limit(10),
                TextColumn::make('evidence_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => Status::colorByValue($state)),
                TextColumn::make('evidence_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('last_seen_at')
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->attribute('status')
                    ->options(Status::options())
                    ->default(Status::ReadyForReview->value)
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    ApproveCompanyAction::make(),
                    ReturnCompanyAction::make(),
                    ResetAction::make(),
                    RejectAction::make(),
                ]),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('last_seen_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            CompanySuggestionRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestionCompanySuggestions::route('/'),
            'edit' => Pages\EditQuestionCompanySuggestion::route('/{record}/edit'),
        ];
    }
}
