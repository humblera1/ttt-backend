<?php

namespace App\Filament\Resources\NotificationCategoryResource\Pages;

use App\Filament\Actions\Page\Delete\Shared\ForceDeleteAction;
use App\Filament\Resources\NotificationCategoryResource;
use App\Filament\Resources\Widgets\Notification\KeyBadge;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditNotificationCategory extends EditRecord
{
    protected static string $resource = NotificationCategoryResource::class;

    public function getTitle(): string
    {
        return 'Edit Notification Category';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            KeyBadge::class,
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('description'),
                ])
                ->compact()
                ->columnSpan(6),
        ])->columns(12);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
