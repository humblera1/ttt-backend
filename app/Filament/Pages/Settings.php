<?php

namespace App\Filament\Pages;

use App\Enums\Type;
use App\Models\Setting;
use App\Services\api\v1\SettingsService;
use App\Services\api\v1\TypeService;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

// todo: arrays type support
class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.settings';

    protected SettingsService $service;

    public array $settings = [];

    public function __construct()
    {
        $this->service = app(SettingsService::class);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('viewAny', Setting::class);
    }

    public function mount(): void
    {
        $this->settings = Setting::all()
            ->groupBy('section')
            ->map(function ($settings) {
                return $settings->mapWithKeys(function (Setting $setting) {
                    return [
                        $setting->key => [
                            'value' => $setting->value,
                            'label' => $setting->label,
                            'type' => $setting->type,
                        ],
                    ];
                });
            })
            ->toArray();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())
            ->statePath('settings');
    }

    public function save(): void
    {
        $flattened = [];

        foreach ($this->settings as $section => $settings) {
            foreach ($settings as $key => $setting) {
                $flattened[] = [
                    'section' => $section,
                    'key' => $key,
                    'value' => $setting['value'],
                ];
            }
        }

        try {
            Setting::upsert(
                $flattened,
                uniqueBy: ['section', 'key'],
                update: ['value'],
            );

            $this->service->clearCache();
        } catch (\Throwable) {
            Notification::make()
                ->warning()
                ->title('Failed to save settings')
                ->send();

            return;
        }

        Notification::make()
            ->success()
            ->title('Settings saved successfully!')
            ->send();
    }

    protected function getFormSchema(): array
    {
        $output = [];

        foreach ($this->settings as $section => $settings) {
            $fields = [];

            foreach ($settings as $name => $setting) {
                $type = Type::tryFrom($setting['type']);

                $key = "$section.$name.value";

                $label = $setting['label'] ?? $key;

                $field = $this->getFieldByType($type, $key)
                    ->label($label);

                $fields[] = $field;
            }

            $output[] = Section::make($this->getSectionTitle($section))
                ->schema($fields)
                ->compact()
                ->columnSpan(1);
        }

        return $output;
    }

    protected function getFieldByType(Type $type, string $name): Field
    {
        return match ($type) {
            Type::Boolean => Toggle::make($name),
            Type::Integer, Type::Float => TextInput::make($name)->numeric()->required(),

            default   => TextInput::make($name)->required(),
        };
    }

    protected function getSectionTitle(string $label): string
    {
        return ucfirst($label);
    }
}
