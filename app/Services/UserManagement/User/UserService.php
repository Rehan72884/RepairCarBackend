<?php

namespace App\Services\UserManagement\User;

use App\DataTransferObjects\UserManagement\User\CreateUserDto;
use App\DataTransferObjects\UserManagement\User\UpdateUserDto;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    public function allUsers(): Collection
    {
        // Fetch users from database
        return User::with('roles.permissions')->orderBy('updated_at', 'desc')->get();
    }

    // method for creating user
    public function addUser(CreateUserDto $createUserDto): User
    {
        // creating a user
        $user = User::create([
            'name' => $createUserDto->name,
            'email' => $createUserDto->email,
            'password' => Hash::make($createUserDto->password),
        ]);

        // finding the requested role
        $role = Role::find($createUserDto->role);

        // markking the user email as verified
        $user->markEmailAsVerified();
        // assigning requested role to user
        $user->assignRole($role->name);

        // returning newly created user
        return $user;
    }

    // method for updating the user
    public function updateUser(UpdateUserDto $updateUserDto, int $id): User
    {
        // finding the user by id
        $user = User::findOrFail($id);

        // finding the updated user role
        $role = Role::find($updateUserDto->role);

        // updating the user
        $user->update([
            'name' => $updateUserDto->name,
            'email' => $updateUserDto->email,
        ]);

        // checking if request data contains password
        if ($updateUserDto->password) {
            // updating the user password
            $user->update(['password' => Hash::make($updateUserDto->password)]);
        }

        // updating the user role
        $user->syncRoles($role->name);

        // returning the updated user
        return $user;
    }

    // method for viewing the user
    public function getUserById(int $id): User
    {
        // finding user by id
        return User::with('roles.permissions')->findOrFail($id);
    }

    // method for deleting the user
    public function deleteUser(int $id): int
    {
        // finding the user with if
        $user = User::findOrFail($id);

        // checking if the user is admin, if so, we can't delete it.
        if ($user->email == 'admin@fortierra.com') {
            return -1;
        }

        $user->delete();

        return $id;
    }

    // method for finding user by email
    public function findByEmail(string $email): User
    {
        // finding user by given email
        $user = User::with('roles')->where('email', $email)->first();

        // returning founded user
        return $user;
    }
}
