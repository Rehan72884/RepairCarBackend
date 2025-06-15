<?php

namespace App\DataTransferObjects\UserManagement\User;

use App\Http\Requests\UserManagement\User\UpdateUserRequest;

readonly class UpdateUserDto
{
    public function __construct(
        public int $role,
        public string $name,
        public string $email,
        public ?string $password,
    ) {}

    public static function fromApiRequest(UpdateUserRequest $request): UpdateUserDto
    {
        // returning new dto object after assigning properties
        return new self(
            $request->validated('role'),
            $request->validated('name'),
            $request->validated('email'),
            $request->validated('password'),
        );
    }
}
