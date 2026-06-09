<?php

namespace App\Services\api\v1\Question;

use App\Repositories\v1\QuestionRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use RedisException;

class FlushQuestionViewsBufferService
{
    public function __construct(
        private readonly QuestionRepository $questionRepository,
        private readonly QuestionViewService $viewService,
    ) {}

    /**
     * Atomically drains the Redis view buffer into views_count on questions.
     */
    public function flush(): void
    {
        $lockSeconds = (int) setting('question_views.flush_lock_seconds', 30);

        Cache::lock('flush-question-views', $lockSeconds)->get(function (): void {
            $increments = $this->drainBuffer();

            if ($increments === []) {
                return;
            }

            $this->questionRepository->incrementViewsCounts($increments);
        });
    }

    /**
     * @return array<int, int>
     */
    private function drainBuffer(): array
    {
        $bufferKey = $this->viewService->bufferKey();
        $tmpKey = $this->viewService->bufferTmpKey();

        try {
            $renamed = Redis::rename($bufferKey, $tmpKey);
        } catch (RedisException $e) {
            Log::error('Failed to rename question views buffer in Redis', [
                'buffer_key' => $bufferKey,
                'tmp_key' => $tmpKey,
                'exception' => $e,
            ]);

            return [];
        }

        if ($renamed === false) {
            Log::error('Redis rename returned false for question views buffer', [
                'buffer_key' => $bufferKey,
                'tmp_key' => $tmpKey,
            ]);

            return [];
        }

        /** @var array<string, string> $raw */
        $raw = Redis::hGetAll($tmpKey);

        Redis::del($tmpKey);

        $increments = [];

        foreach ($raw as $questionId => $delta) {
            $increments[(int) $questionId] = (int) $delta;
        }

        return $increments;
    }
}
