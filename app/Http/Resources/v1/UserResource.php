<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $hasPersonality = !$this->hasRole('guest');

        return [
            'id' => $this->id,
            'username' => $this->username,
            'personality' => $this->when($hasPersonality, fn () => $this->getPersonality()),
        ];
    }

    protected function getPersonality(): array
    {
        return [
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
        ];
    }
}
