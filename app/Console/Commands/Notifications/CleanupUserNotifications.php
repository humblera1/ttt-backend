<?php

namespace App\Console\Commands\Notifications;

use App\Services\api\v1\Notification\UserNotificationCleanupService;
use Illuminate\Console\Command;

class CleanupUserNotifications extends Command
{
    public function __construct
    (
        protected UserNotificationCleanupService $service,
    )
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-user-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clearing old user notifications by TTL and per-user limit';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->service->cleanup(
            setting('notifications.read_ttl_days', 30),
            setting('notifications.unread_ttl_days', 90),
            setting('notifications.max_per_user', 500),
        );

        $this->info('Notifications cleanup finished.');

        return self::SUCCESS;
    }
}
