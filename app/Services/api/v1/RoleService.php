<?php

namespace App\Services\api\v1;

class RoleService
{
    public function generatePermission(string $actionName, string $entityName): string
    {
        return $actionName . '-' . $entityName;
    }
}
