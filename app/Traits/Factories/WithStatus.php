<?php

namespace App\Traits\Factories;

use App\Enums\Status;

trait WithStatus
{
    public function pending(): static
    {
        return $this->state(fn ($attributes) => [
            'status' => Status::Pending->name,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn ($attributes) => [
            'status' => Status::Approved->name,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn ($attributes) => [
            'status' => Status::Rejected->name,
        ]);
    }
}
