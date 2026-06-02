<?php

namespace App\Http\Resources\v1\Vote;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoteDeltaResource extends JsonResource
{
    public function __construct(
        int $delta,
    ) {
        parent::__construct(['delta' => $delta]);
    }

    /**
     * @return array<string, int>
     */
    public function toArray(Request $request): array
    {
        return [
            'delta' => $this->resource['delta'],
        ];
    }
}
