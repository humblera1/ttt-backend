<?php

namespace App\Repositories;

use App\Exceptions\v1\RepositoryException;
use Illuminate\Database\Eloquent\Model;
use Throwable;

abstract class Repository
{
    abstract protected function getModelInstance(): Model;

    /**
     * @throws RepositoryException
     */
    public function save(Model $model): void
    {
        $saved = $model->save();

        if (!$saved) {
            throw new RepositoryException('Failed to save model');
        }
    }

    /**
     * @throws RepositoryException
     */
    public function bulkUpdate(array $ids, array $attributes): void
    {
        try {
            $model = $this->getModelInstance();

            $model->newQuery()->whereIn('id', $ids)->update($attributes);
        } catch (Throwable) {
            throw new RepositoryException('Failed to bulk update models');
        }
    }
}
