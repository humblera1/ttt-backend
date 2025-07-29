<?php

namespace App\Repositories;

use App\Exceptions\v1\RepositoryException;
use Illuminate\Database\Eloquent\Model;

abstract class Repository
{
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
}
