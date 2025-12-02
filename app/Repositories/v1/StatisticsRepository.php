<?php

namespace App\Repositories\v1;

use App\Exceptions\v1\RepositoryException;
use App\Models\Statistic;
use App\Models\User;
use App\Repositories\Repository;

class StatisticsRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(Statistic::class);
    }

    public function findByUserAndQuestion(int $userId, int $questionId): ?Statistic
    {
        return Statistic::query()
            ->byUserAndQuestion($userId, $questionId)
            ->first();
    }

    public function findNotMetByUserAndQuestion(int $userId, int $questionId): ?Statistic
    {
        return Statistic::query()
            ->byUserAndQuestion($userId, $questionId)
            ->where('met_in_real_interview', false)
            ->first();
    }

    public function findByCombination(int $userId, int $questionId, int $companyId, int $positionId): ?Statistic
    {
        return Statistic::query()
            ->byUserAndQuestion($userId, $questionId)
            ->byCompanyAndPosition($companyId, $positionId)
            ->first();
    }

    public function countByUserQuestion(int $userId, int $questionId): int
    {
        return Statistic::query()
            ->byUserAndQuestion($userId, $questionId)
            ->count();
    }

    /**
     * Creates a record in the statistics table: the submitted user has not encountered the submitted question
     * in real interviews.
     *
     * @throws RepositoryException
     */
    public function createNotMet(int $userId, int $questionId): void
    {
        $statistic = new Statistic();

        $statistic->user_id = $userId;
        $statistic->question_id = $questionId;
        $statistic->met_in_real_interview = false;

        $this->save($statistic);
    }
}
