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
        $record->status = Status::Approved->value;

        try {
            $this->repository->save($record);
        } catch (RepositoryException $e) {
            Log::error('Failed to approve record', ['exception' => $e]);

            throw new BusinessLogicException($e->getMessage());
        }
    }
}
