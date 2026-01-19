<?php

namespace App\Filament\Pages;

use App\Enums\Notification\RecipientMode;
use App\Filament\Resources\UserNotificationResource;
use App\Models\NotificationType;
use App\Models\User;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SendMassNotification extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = UserNotificationResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationGroup = 'Notifications';

    protected static ?string $navigationLabel = 'Send Mass Notification';

    protected static ?string $title = 'Send Mass Notification';

    protected static string $view = 'filament.pages.send-mass-notification';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return true;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('type')
                            ->options(NotificationType::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Select::make('recipient_mode')
                            ->label('Получатели')
                            ->options(RecipientMode::options())
                            ->default(RecipientMode::All->value)
                            ->reactive()
                            ->required(),
                        Select::make('users')
                            ->multiple()
                            ->options(User::all()->pluck('username', 'id'))
                            ->searchable()
                            ->visible(fn (callable $get) => $get('recipient_mode') === RecipientMode::ByUsername->value)
                            ->required(fn (callable $get) => $get('recipient_mode') === RecipientMode::ByUsername->value),
                    ])
                    ->columnSpan(1),
                Section::make()
                    ->schema([
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
                            ->maxWidth(200)
                            ->required(),
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
                            ->maxWidth(200)
                            ->required(),
                    ])
                    ->columnSpan(1)
            ])
            ->statePath('data')
            ->columns(2);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $mode = RecipientMode::tryFrom($data['recipient_mode']);

        $forAll = $mode === RecipientMode::All;

        Notification::make()
            ->success()
            ->title('Notifications are queued for sending.')
            ->send();
    }
}
