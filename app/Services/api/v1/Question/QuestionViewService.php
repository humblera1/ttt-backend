<?php

namespace App\Services\api\v1\Question;

use App\Models\Question;
use Illuminate\Support\Facades\Redis;

class QuestionViewService
{
    /**
     * Records a view in the Redis buffer after IP deduplication. Does not write to the database.
     */
    public function recordView(Question $question, string $ip): void
    {
        if (!Question::query()->whereKey($question->id)->exists()) {
            return;
        }

        $dedupKey = $this->dedupKey($question->id, $ip);
        $ttl = (int) setting('question_views.dedup_ttl_seconds', 120);

        if (!Redis::setnxex($dedupKey, '1', $ttl)) {
            return;
        }

        Redis::hIncrBy($this->bufferKey(), (string) $question->id, 1);
    }

    public function bufferKey(): string
    {
        return (string) config('question_views.redis.buffer_key');
    }

    public function bufferTmpKey(): string
    {
        return (string) config('question_views.redis.buffer_tmp_key');
    }

    private function dedupKey(int $questionId, string $ip): string
    {
        $prefix = (string) config('question_views.redis.dedup_key_prefix');

        return $prefix.$questionId.':'.$ip;
    }
}
