<?php

namespace App\Filament\Resources\QuestionResource\RelationManagers;

use App\Models\QuestionCompanySuggestion;
use App\Models\Statistic;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CompanySuggestionRelationManager extends RelationManager
{
    protected static string $relationship = 'company';

    protected static ?string $title = 'Statistics';

    protected $listeners = [
        'statisticsUpdated',
    ];

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('viewAny', Statistic::class);
    }

    public function table(Table $table): Table
    {
        /** @var QuestionCompanySuggestion $record */
        $record = $this->getOwnerRecord();

        return $table
            ->query(
                Statistic::query()
                    ->where('question_id', $record->question_id)
                    ->where('company_id', $record->company_id)
            )
            ->columns([
                TextColumn::make('user.username')
                    ->label('User')
                    ->url(fn (Statistic $stat) => "/admin/users/{$stat->user_id}/edit")
                    ->placeholder('—'),
                TextColumn::make('when_asked')
                    ->label('Asked')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Created at')
                    ->dateTime('d.m.y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
