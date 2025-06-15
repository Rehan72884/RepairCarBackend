<?php

namespace App\DataTransferObjects\UserManagement\Role;

use App\Http\Requests\UserManagement\Role\CreateRoleRequest;
use App\Http\Requests\UserManagement\Role\UpdateRoleRequest;

readonly class RoleDto
{
    public function __construct(
        public string $name,
        public array $permissions,
        public ?string $description,
    ) {}

    public static function fromApiRequestCreate(CreateRoleRequest $request): RoleDto
    {
        // returning new dto object after assigning properties
        return new self(
            $request->validated('name'),
            $request->validated('permissions'),
            $request->validated('description'),
        );
    }

    public static function fromApiRequestUpdate(UpdateRoleRequest $request): RoleDto
    {
        // returning new dto object after assigning properties
        return new self(
            $request->validated('name'),
            $request->validated('permissions'),
            $request->validated('description'),
        );
    }
}
