<?php

namespace App\Console\Commands\Settings;

use App\Enums\Type;
use App\Services\api\v1\SettingsService;
use Illuminate\Console\Command;
use App\Models\Setting;

class ImportSettings extends Command
{
    protected const string DEFAULT_TYPE = Type::Integer->value;

    public function __construct
    (
        protected SettingsService $service,
    )
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the application settings from config file';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $settings = config('settings');

        foreach ($settings as $section => $items) {
            foreach ($items as $data) {
                $key = $section . '.' . $data['key'];

                if (Setting::where('key', $key)->exists()) {
                    $this->line("Setting [$key] already exists, skipping.");

                    continue;
                }

                Setting::create([
                    'key' => $key,
                    'value' => $data['value'],
                    'label' => $data['label'],
                    'section' => $section,
                    'type' => $data['type'] ?? self::DEFAULT_TYPE,
                ]);

                $this->info("Imported setting: $key");
            }
        }

        $this->service->clearCache();
    }
}
