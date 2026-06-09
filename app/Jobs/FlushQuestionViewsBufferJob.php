<?php

namespace App\Jobs;

use App\Services\api\v1\Question\FlushQuestionViewsBufferService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FlushQuestionViewsBufferJob implements ShouldQueue
{
    use Queueable;

    public function handle(FlushQuestionViewsBufferService $service): void
    {
        $service->flush();
    }
}
