<?php

namespace App\Services\api\v1\Notification;

use App\Models\UserNotification;
use Illuminate\Database\Eloquent\Builder;

class UserNotificationCleanupService
{
    public function cleanup(int $readTtlDays, int $unreadTtlDays, int $maxPerUser): void
    {
        $this->cleanupByTtl($readTtlDays, $unreadTtlDays);
        $this->cleanupByPerUserLimit($maxPerUser);
    }

    private function cleanupByTtl(int $readTtlDays, int $unreadTtlDays): void
    {
        $now = now();

        $readBefore = $now->copy()->subDays($readTtlDays);
        $unreadBefore = $now->copy()->subDays($unreadTtlDays);

        $query = UserNotification::query()
            ->where(function (Builder $q) use ($readBefore, $unreadBefore) {
                $q->whereNotNull('read_at')
                    ->where('created_at', '<', $readBefore)
                    ->orWhere(function (Builder $q2) use ($unreadBefore) {
                        $q2->whereNull('read_at')
                            ->where('created_at', '<', $unreadBefore);
                    });
            });

        $query->chunkById(1000, function ($chunk) {
            $ids = $chunk->pluck('id');

            UserNotification::query()
                ->whereIn('id', $ids)
                ->forceDelete();
        });
    }

    private function cleanupByPerUserLimit(int $maxPerUser): void
    {
        if ($maxPerUser <= 0) {
            return;
        }

        $table = new UserNotification()->getTable();
        $connection = UserNotification::query()->getConnection();

        $sql = "
            DELETE FROM {$table}
            WHERE id IN (
                SELECT id FROM (
                    SELECT id,
                        ROW_NUMBER() OVER (
                            PARTITION BY user_id
                            ORDER BY created_at DESC
                        ) AS rn
                    FROM {$table}
                ) t
                WHERE t.rn > ?
            )
        ";

        $connection->statement($sql, [$maxPerUser]);
    }
}
