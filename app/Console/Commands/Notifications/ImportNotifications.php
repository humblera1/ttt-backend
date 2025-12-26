<?php

namespace App\Console\Commands\Notifications;

use App\Services\api\v1\Notification\NotificationImportService;
use Illuminate\Console\Command;

class ImportNotifications extends Command
{
    public function __construct
    (
        protected NotificationImportService $service,
    )
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-notifications
                            {--only-new : Import only new records, do not update existing ones}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import notification categories & types from config file';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $updateExisting = !$this->option('only-new');

        $categories = config('notification.categories');

        if (!is_array($categories)) {
            $this->error('Config [notification.categories] must return an array.');

            return self::FAILURE;
        }

        foreach ($categories as $index => $category) {
            if (!is_array($category)) {
                $this->error("Category at index {$index} must be an array.");

                return self::FAILURE;
            }

            if (empty($category['key']) || empty($category['name'])) {
                $this->error("Category at index {$index} must contain 'key' and 'name'.");

                return self::FAILURE;
            }

            foreach ($category['types'] as $tIndex => $type) {
                if (!is_array($type)) {
                    $this->error("Type at index {$tIndex} in category '{$category['key']}' must be an array.");

                    return self::FAILURE;
                }

                if (empty($type['key']) || empty($type['name'])) {
                    $this->error("Type at index {$tIndex} in category '{$category['key']}' must contain 'key' and 'name'.");

                    return self::FAILURE;
                }
            }
        }

        $isSuccess = $this->service->importFromConfig(
            categories: $categories,
            updateExisting: $updateExisting,
        );

        if ($isSuccess) {
            $this->info('Import completed.');

            return self::SUCCESS;
        }

        $this->error('Import failed!');

        return self::FAILURE;
    }
}
