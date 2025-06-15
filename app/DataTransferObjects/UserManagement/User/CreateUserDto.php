<?php

namespace App\DataTransferObjects\UserManagement\User;

use App\Http\Requests\UserManagement\User\CreateUserRequest;

readonly class CreateUserDto
{
    public function __construct(
        public int $role,
        public string $name,
        public string $email,
        public string $password,
    ) {}

    public static function fromApiRequest(CreateUserRequest $request): CreateUserDto
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
