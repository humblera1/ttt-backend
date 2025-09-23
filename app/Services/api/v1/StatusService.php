<?php

namespace App\Services\api\v1;

use App\Enums\Status;
use App\Exceptions\v1\BusinessLogicException;
use App\Exceptions\v1\RepositoryException;
use App\Repositories\v1\UserRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class StatusService
{
    public function __construct(
        protected UserRepository $repository,
    )
    {}

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
    protected function changeStatus(Model $record, Status $status): void
    {
        $record->status = $status->value;

        try {
            $this->repository->save($record);
        } catch (RepositoryException $e) {
            Log::error('Failed to approve record', ['exception' => $e]);

            throw new BusinessLogicException($e->getMessage());
        }
    }
}
