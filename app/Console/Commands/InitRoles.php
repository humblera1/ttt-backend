<?php

namespace App\Console\Commands;

use App\Enums\Action;
use App\Services\api\v1\CaseService;
use App\Services\api\v1\RoleService;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class InitRoles extends Command
{
    protected CaseService $caseService;
    protected RoleService $roleService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize roles and permissions.';

    public function __construct(CaseService $caseService, RoleService $roleService)
    {
        parent::__construct();
        $this->caseService = $caseService;
        $this->roleService = $roleService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Role::query()->delete();
        Permission::query()->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = config('roles');

        foreach ($roles as $roleName => $entities) {
            $role = Role::create([
                'name' => $this->caseService->camelToKebabCase($roleName)
            ]);

            $permissions = [];

            foreach ($entities as $entityName => $actions) {

                /** @var Action $action */
                foreach ($actions as $action) {
                    $permissionName = $this->roleService->generatePermission(
                        $this->caseService->camelToKebabCase($action->name),
                        $this->caseService->camelToKebabCase($entityName),
                    );

                    $permissions[] = Permission::firstOrCreate([
                        'name' => $permissionName,
                    ]);
                }
            }

            $role->syncPermissions($permissions);
        }

        $this->info("Roles and permissions initialized successfully. Yay!");
    }
}
