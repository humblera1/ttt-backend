<?php

namespace App\Interfaces\v1\Requests;

use App\DTOs\BaseDTO;

interface RequestDTOInterface
{
    public function getDTO(): BaseDTO;
}
