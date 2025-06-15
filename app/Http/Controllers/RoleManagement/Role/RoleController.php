<?php

namespace App\Http\Controllers\RoleManagement\Role;

use App\DataTransferObjects\UserManagement\Role\RoleDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserManagement\Role\CreateRoleRequest;
use App\Http\Requests\UserManagement\Role\UpdateRoleRequest;
use App\Services\UserManagement\Role\RoleService;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use ApiResponseHelpers;

    // injecting the dependecies
    public function __construct(private readonly RoleService $roleService) {}

    // method for updating a role
    public function index(): JsonResponse
    {
        // fetching all roles
        $roles = $this->roleService->allRoles();

        // returning the roles
        return $this->respondWithSuccess([
            'success' => true,
            'message' => 'Roles fetched successfully!',
            'roles' => $roles,
        ]);
    }

    // method for creating a new role
    public function store(CreateRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->addRole(RoleDto::fromApiRequestCreate($request));

        // returning the created role
        return $this->respondCreated([
            'success' => true,
            'message' => 'Role created successfully!',
            'role' => $role,
        ]);
    }

    // method for fetching a role by its id
    public function show(int $id)
    {
        // fetching the role by its id
        $role = $this->roleService->findRoleById($id);

        // returning the role
        return $this->respondWithSuccess([
            'success' => true,
            'message' => 'Role fetched successfully!',
            'role' => $role,
        ]);
    }

    // method for fetching all permissions
    public function getPermissions(): JsonResponse
    {
        $permissions = $this->roleService->getAllPermissions();

        // returning the permissions
        return $this->respondWithSuccess([
            'success' => true,
            'message' => 'Permissions fetched successfully!',
            'permissions' => $permissions,
        ]);
    }

    // method for updating a role
    public function update(UpdateRoleRequest $request, int $id): JsonResponse
    {
        // updating the role with the given id and data from the request
        $role = $this->roleService->updateRole(RoleDto::fromApiRequestUpdate($request), $id);

        // returning the updated role
        return $this->respondWithSuccess([
            'success' => true,
            'message' => 'Role updated successfully!',
            'role' => $role,
        ]);
    }

    // method for deleting a role
    public function destroy(int $id)
    {
        // deleting the role with the given id
        $deletedUserId = $this->roleService->deleteRole($id);

        if ($deletedUserId < 0) {
            return $this->respondForbidden('Role is assigned to users and cannot be deleted.');
        }

        // returning a success response
        return $this->respondWithSuccess([
            'success' => true,
            'message' => 'Role deleted successfully!',
            '_id' => $deletedUserId,
        ]);
    }
}
