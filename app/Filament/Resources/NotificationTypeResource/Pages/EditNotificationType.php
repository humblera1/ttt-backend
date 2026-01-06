<?php

namespace App\Filament\Resources\NotificationTypeResource\Pages;

use App\Filament\Actions\Delete\ForceDeleteAction;
use App\Filament\Forms\Notification\ListPlaceholders;
use App\Filament\Resources\NotificationTypeResource;
use App\Filament\Resources\Widgets\Notification\KeyBadge;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditNotificationType extends EditRecord
{
    protected static string $resource = NotificationTypeResource::class;

    public function getTitle(): string
    {
        return 'Edit Notification Type';
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
                    RichEditor::make('template_title')
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'link',
                            'redo',
                            'strike',
                            'underline',
                            'undo',
                            'subscript',
                        ])
                        ->maxWidth(200),
                    RichEditor::make('template_body')
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'link',
                            'redo',
                            'strike',
                            'underline',
                            'undo',
                            'subscript',
                        ])
                        ->maxWidth(200),
                ])
                ->compact()
                ->columnSpan(6),
            Section::make('Available placeholders')
                ->description(
                    'These keys will be guaranteed to be interpolated during the notification generation process by the system.'
                )
                ->schema([
                    ListPlaceholders::make(),
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
