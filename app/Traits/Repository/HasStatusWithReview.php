<?php

namespace App\Traits\Repository;

use App\Enums\Suggestion\Status;
use App\Exceptions\v1\RepositoryException;
use Illuminate\Database\Eloquent\Model;
use Throwable;

trait HasStatusWithReview
{
    use HasStatus;

    /**
     * @throws RepositoryException
     */
    public function returnForReviewById(int $id): void
    {
        try {
            $model = $this->modelClass::findOrFail($id);

            $this->returnForReview($model);
        } catch (Throwable $throwable) {
            throw new RepositoryException('Failed to return model for review');
        }
    }

    /**
     * @throws RepositoryException
     */
    public function returnForReview(Model $model): void
    {
        $this->changeStatus($model, Status::ReadyForReview);
    }

    /**
     * @throws RepositoryException
     */
    public function approve(Model $model): void
    {
        $this->changeStatus($model, Status::Approved);
    }

    /**
     * @throws RepositoryException
     */
    public function changeStatus(Model $model, Status $status): void
    {
        $model->status = $status->value;

        $this->save($model);
    }
}
