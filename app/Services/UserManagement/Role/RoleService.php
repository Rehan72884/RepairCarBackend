<?php

namespace App\Services\UserManagement\Role;

use App\DataTransferObjects\UserManagement\Role\RoleDto;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function allRoles(): Collection
    {
        return Role::with('permissions')->orderBy('updated_at', 'desc')->get();
    }

    public function addRole(RoleDto $dto): Role
    {
        // creating a new role with given name and permissions
        $role = Role::create([
            'name' => $dto->name,
            'guard_name' => 'web',
            'description' => $dto->description,
        ]);

        $role->syncPermissions($dto->permissions);

        return $role->load('permissions');
    }

    public function findRoleById(int $id): Role
    {
        // fetching the role by its id
        $role = Role::with('permissions')->findORFail($id);

        return $role;
    }

    public function updateRole(RoleDto $dto, int $id): Role
    {
        // find the role by its id
        $role = Role::findOrFail($id);

        // updating role
        $role->update([
            'name' => $dto->name,
            'guard_name' => 'web',
            'description' => $dto->description,
        ]);

        // syncing permissions with the new given permissions list
        $role->syncPermissions($dto->permissions);

        return $role->load('permissions');
    }

    public function getAllPermissions(): Collection
    {
        // fetching all permissions
        return Permission::all();
    }

    public function deleteRole(int $id): int
    {

        // checking if there are any users assigned to the role
        if (Role::find($id)->users->count() > 0 || $id == 1) {
            return -1;
        }

        // deleting the role with given id
        Role::findOrFail($id)->delete();

        return $id;
    }
}
