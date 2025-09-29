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
    public function bulkUpdate(array $ids, array $attributes): void
    {
        try {
            $this->modelClass::query()->whereIn('id', $ids)->update($attributes);
        } catch (Throwable) {
            throw new RepositoryException('Failed to bulk update models');
        }
    }
}
