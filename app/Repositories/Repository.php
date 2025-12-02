<?php

namespace App\Repositories;

use App\Exceptions\v1\RepositoryException;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class Repository
{
    public function __construct
    (
        protected string $modelClass
    ) {}

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
    public function delete(Model $model): void
    {
        $deleted = $model->delete();

        if ($deleted === false) {
            throw new RepositoryException('Failed to delete model');
        }
    }

    /**
     * @throws RepositoryException
     */
    public function bulkUpdate(array $ids, array $attributes): void
    {
        try {
            $this->modelClass::query()->whereIn('id', $ids)->update($attributes);
        } catch (Throwable) {
            throw new RepositoryException('Failed to bulk update models');
        }
    }

    public function bulkForceDelete(array $ids): void
    {
        try {
            $this->modelClass::query()->whereIn('id', $ids)->forceDelete();
        } catch (Throwable) {
            throw new RepositoryException('Failed to bulk delete models');
        }
    }

    public function bulkDelete(array $ids): void
    {
        try {
            $this->modelClass::query()->whereIn('id', $ids)->delete();
        } catch (Throwable) {
            throw new RepositoryException('Failed to bulk delete models');
        }
    }
}
