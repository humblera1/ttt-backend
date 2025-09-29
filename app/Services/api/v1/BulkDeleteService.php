<?php

namespace App\Services\api\v1;

use App\Exceptions\v1\BusinessLogicException;
use App\Exceptions\v1\RepositoryException;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Log;

class BulkDeleteService
{
    protected Repository $repository;

    public function __construct(string $modelClass)
    {
        $this->repository = app(Repository::class, [
            'modelClass' => $modelClass,
        ]);
    }

    /**
     * @throws BusinessLogicException
     */
    public function forceDeleteMany(iterable $records): void
    {
        $recordsCollection = collect($records);

        $ids = $recordsCollection->pluck('id')->all();

        if (empty($ids)) {
            return;
        }

        try {
            $this->repository->bulkForceDelete($ids);
        } catch (RepositoryException $e) {
            Log::error('Failed to bulk delete users', ['exception' => $e]);

            throw new BusinessLogicException($e->getMessage());
        }
    }
}
