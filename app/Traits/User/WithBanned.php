<?php

namespace App\Traits\User;

use App\Exceptions\v1\BusinessLogicException;
use App\Services\api\v1\UserBanService;

trait WithBanned
{
    public function banned(): bool
    {
        return !is_null($this->banned_at);
    }

    /**
     * @throws BusinessLogicException
     */
    public function ban(): void
    {
        $service = app(UserBanService::class);

        $service->ban($this);
    }

    /**
     * @throws BusinessLogicException
     */
    public function unban(): void
    {
        $service = app(UserBanService::class);

        $service->unban($this);
    }
}
