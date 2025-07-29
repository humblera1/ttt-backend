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
        return [
            'id' => $this->id,
            'username' => $this->username,
            'personality' => $this->when($this->hasRole('user'), fn () => $this->getPersonality())
        ];
    }

    protected function getPersonality(): array
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
        ];
    }
}
