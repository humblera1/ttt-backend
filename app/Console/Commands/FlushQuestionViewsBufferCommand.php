<?php

namespace App\Console\Commands;

use App\Services\api\v1\Question\FlushQuestionViewsBufferService;
use Illuminate\Console\Command;

class FlushQuestionViewsBufferCommand extends Command
{
    protected $signature = 'views:flush';

    protected $description = 'Flush buffered question views from Redis into the database';

    public function __construct(
        private readonly FlushQuestionViewsBufferService $service,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->service->flush();

        $this->info('Question views buffer flushed.');

        return self::SUCCESS;
    }
}
