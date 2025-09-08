<?php

namespace App\Services\api\v1;

use App\Exceptions\v1\BusinessLogicException;
use App\Exceptions\v1\RepositoryException;
use App\Models\User;
use App\Repositories\v1\UserRepository;
use Illuminate\Support\Facades\Log;

class UserDeleteService
{
    public function __construct(
        protected UserRepository $repository,
    )
    {}

    /**
     * @throws BusinessLogicException
     */
    public function deleteMany(iterable $users): void
    {
        $usersCollection = collect($users);

        $adminExists = $usersCollection->contains(fn(User $user) => $user->hasRole('admin'));

        if ($adminExists) {
            throw new BusinessLogicException('You cannot delete the admin!');
        }

        $ids = $usersCollection->pluck('id')->all();

        if (empty($ids)) {
            return;
        }

        try {
            $this->repository->bulkUpdate($ids, [
                'deleted_at' => now(),
            ]);
        } catch (RepositoryException $e) {
            Log::error('Failed to bulk delete users', ['exception' => $e]);

            throw new BusinessLogicException($e->getMessage());
        }
    }
}
