<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Init extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes the application';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->call('app:init-roles');
        $this->call('app:init-admin-user');
    }
}
