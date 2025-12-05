<?php

namespace App\Traits\Repository;

use App\Enums\Status;
use App\Exceptions\v1\RepositoryException;
use Illuminate\Database\Eloquent\Model;
use Throwable;

trait HasStatus
{
    /**
     * @throws RepositoryException
     */
    public function approveById(int $id): void
    {
        try {
            $model = $this->modelClass::findOrFail($id);

            $this->approve($model);
        } catch (Throwable $throwable) {
            throw new RepositoryException('Failed to approve model');
        }
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
