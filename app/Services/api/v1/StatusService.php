<?php

namespace App\Services\api\v1;

use App\Enums\Status;
use App\Exceptions\v1\BusinessLogicException;
use App\Exceptions\v1\RepositoryException;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class StatusService
{
    /**
     * @throws BusinessLogicException
     */
    public function approve(Model $record): void
    {
        $this->changeStatus($record, Status::Approved);
    }

    /**
     * @throws BusinessLogicException
     */
    public function reject(Model $record): void
    {
        $this->changeStatus($record, Status::Rejected);
    }

    /**
     * @throws BusinessLogicException
     */
    public function reset(Model $record): void
    {
        $this->changeStatus($record, Status::Pending);
    }

    /**
     * @throws BusinessLogicException
     */
    protected function changeStatus(Model $record, Status $status): void
    {
        $repository = app(Repository::class, [
            'modelClass' => $record::class,
        ]);

        $record->status = $status->value;

        try {
            $repository->save($record);
        } catch (RepositoryException $e) {
            Log::error('Failed to update status', ['exception' => $e]);

            throw new BusinessLogicException($e->getMessage());
        }
    }
}
