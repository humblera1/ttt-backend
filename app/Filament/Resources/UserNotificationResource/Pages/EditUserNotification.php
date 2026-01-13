<?php

namespace App\Filament\Resources\UserNotificationResource\Pages;

use App\Filament\Actions\Delete\ForceDeleteAction;
use App\Filament\Forms\Notification\ListData;
use App\Filament\Resources\UserNotificationResource;
use App\Models\UserNotification;
use App\Traits\Filament\Forms\Utils\HasLinkUtils;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditUserNotification extends EditRecord
{
    use HasLinkUtils;

    protected static string $resource = UserNotificationResource::class;

    public function getTitle(): string
    {
        return 'Edit Notification';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Group::make()
                ->schema([
                    Section::make('User')
                        ->schema([
                            Placeholder::make('user.username')
                                ->content(function (UserNotification $record): HtmlString {
                                    return $this->createLink(
                                        "/admin/users/$record->user_id/edit",
                                        $record->user->username,
                                    );
                                }),
                            Placeholder::make('user.email')
                                ->content(fn (UserNotification $record) => $record->user->email)
                        ])
                        ->icon('heroicon-m-user'),

                    Section::make('Type')
                        ->schema([
                            Placeholder::make('type.name')
                                ->label('Type')
                                ->content(function (UserNotification $record): HtmlString {
                                    return $this->createLink(
                                        "/admin/notification-types/{$record->type->id}/edit",
                                        $record->type->name,
                                    );
                                }),
                            Placeholder::make('category.name')
                                ->label('Category')
                                ->content(function (UserNotification $record): HtmlString {
                                    return $this->createLink(
                                        "/admin/notification-categories/{$record->type->category->id}/edit",
                                        $record->type->category->name,
                                    );
                                }),
                        ])
                        ->icon('heroicon-m-squares-2x2'),

                    Section::make('Data')
                        ->schema([
                            ListData::make('data'),
                        ])
                        ->icon('heroicon-m-code-bracket-square'),
                ])
                ->columnSpan(1),

            Group::make()
                ->schema([
                    Section::make('Info')->schema([
                        RichEditor::make('title')
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
                        RichEditor::make('body')
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
                        ->icon('heroicon-m-information-circle'),
                ])
                ->columnSpan(1),
        ])->columns(2);
    }
}
