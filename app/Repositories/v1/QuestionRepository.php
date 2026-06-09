<?php

namespace App\Repositories\v1;

use App\Exceptions\v1\RepositoryException;
use App\Models\Question;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Throwable;

class QuestionRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(Question::class);
    }

    /**
     * Bulk-increment views_count and mark questions for rating recalculation.
     *
     * @param  array<int, int>  $increments  question_id => delta
     *
     * @throws RepositoryException
     */
    public function incrementViewsCounts(array $increments): void
    {
        if ($increments === []) {
            return;
        }

        $chunkSize = (int) config('question_views.flush_chunk_size', 300);

        foreach (array_chunk($increments, $chunkSize, true) as $chunk) {
            $this->incrementViewsCountsChunk($chunk);
        }
    }

    /**
     * @param  array<int, int>  $increments
     *
     * @throws RepositoryException
     */
    private function incrementViewsCountsChunk(array $increments): void
    {
        $filtered = [];

        foreach ($increments as $id => $delta) {
            $delta = (int) $delta;

            if ($delta > 0) {
                $filtered[$id] = $delta;
            }
        }

        if ($filtered === []) {
            return;
        }

        $viewsCases = [];
        $bindings = [];
        $ids = [];

        foreach ($filtered as $id => $delta) {
            $viewsCases[] = 'WHEN ? THEN views_count + ?';
            $bindings[] = $id;
            $bindings[] = $delta;
            $ids[] = $id;
        }

        $placeholders = implode(', ', array_fill(0, count($ids), '?'));
        $viewsCaseSql = implode(' ', $viewsCases);

        $sql = "UPDATE questions SET views_count = CASE id {$viewsCaseSql} END, rating_needs_recalculation = 1 WHERE id IN ({$placeholders})";

        try {
            DB::update($sql, [...$bindings, ...$ids]);
        } catch (Throwable) {
            throw new RepositoryException('Failed to increment views counts');
        }
    }
}
