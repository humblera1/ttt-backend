<?php

namespace App\Services\api\v1;

use App\Exceptions\v1\BusinessLogicException;
use App\Exceptions\v1\RepositoryException;
use App\Models\User;
use App\Repositories\v1\UserRepository;
use Illuminate\Support\Facades\Log;

class UserBanService
{
    public function __construct(
        protected UserRepository $repository,
    )
    {}

    /**
     * @throws BusinessLogicException
     */
    public function ban(User $user): void
    {
        if ($user->hasRole('admin')) {
            throw new BusinessLogicException('You cannot ban the admin!');
        }

        $user->banned_at = now();

        try {
            $this->repository->save($user);
        } catch (RepositoryException $e) {
            Log::error('Failed to ban user', ['exception' => $e]);

            throw new BusinessLogicException($e->getMessage());
        }
    }

    /**
     * @throws BusinessLogicException
     */
    public function unban(User $user): void
    {
        $user->banned_at = null;

        try {
            $this->repository->save($user);
        } catch (RepositoryException $e) {
            Log::error('Failed to ban user', ['exception' => $e]);

            throw new BusinessLogicException($e->getMessage());
        }
    }

    /**
     * @throws BusinessLogicException
     */
    public function banMany(iterable $users): void
    {
        $usersCollection = collect($users);

        $adminExists = $usersCollection->contains(fn(User $user) => $user->hasRole('admin'));

        if ($adminExists) {
            throw new BusinessLogicException('You cannot ban the admin!');
        }

        $ids = $usersCollection->pluck('id')->all();

        if (empty($ids)) {
            return;
        }

        try {
            $this->repository->bulkUpdate($ids, [
                'banned_at' => now(),
            ]);
        } catch (RepositoryException $e) {
            Log::error('Failed to bulk ban users', ['exception' => $e]);

            throw new BusinessLogicException($e->getMessage());
        }
    }
}
