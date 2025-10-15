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
    public function approve(Model $record): bool
    {
        return $this->changeStatus($record, Status::Approved);
    }

    public function reject(Model $record): bool
    {
        return $this->changeStatus($record, Status::Rejected);
    }

    public function reset(Model $record): bool
    {
        return $this->changeStatus($record, Status::Pending);
    }

    protected function changeStatus(Model $record, Status $status): bool
    {
        $repository = app(Repository::class, [
            'modelClass' => $record::class,
        ]);

        $record->status = $status->value;

        try {
            $repository->save($record);
        } catch (RepositoryException $e) {
            Log::error('Failed to update status', ['exception' => $e]);

            return false;
        }

        return true;
    }
}
