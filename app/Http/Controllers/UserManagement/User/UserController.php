<?php

namespace App\Http\Controllers\UserManagement\User;

use App\DataTransferObjects\UserManagement\User\CreateUserDto;
use App\DataTransferObjects\UserManagement\User\UpdateUserDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserManagement\User\CreateUserRequest;
use App\Http\Requests\UserManagement\User\UpdateUserRequest;
use App\Services\UserManagement\User\UserService;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use ApiResponseHelpers;

    // injecting the dependecies
    public function __construct(private readonly UserService $userService) {}

    public function index(): JsonResponse
    {
        // getting all users
        $users = $this->userService->allUsers();

        // returning the users
        return $this->respondWithSuccess([
            'success' => true,
            'message' => 'Users fetched successfully',
            'users' => $users,
        ]);
    }

    // creating a new user
    public function store(CreateUserRequest $request)
    {
        // creating a new user with given data
        $user = $this->userService->addUser(CreateUserDto::fromApiRequest($request));

        // returning the newly created user
        return $this->respondCreated([
            'success' => true,
            'message' => 'User created successfully',
            'user' => $user->load('roles.permissions'),
        ]);
    }

    // updating a user
    public function update(UpdateUserRequest $request, $id): JsonResponse
    {
        // updating the user with given id
        $user = $this->userService->updateUser(UpdateUserDto::fromApiRequest($request), $id);

        // returning the updated user
        return $this->respondWithSuccess([
            'success' => true,
            'user' => $user->load('roles.permissions'),
        ]);
    }

    // viewing a user
    public function show($id): JsonResponse
    {
        // getting the user with given id
        $user = $this->userService->getUserById($id);

        // returning the user
        return $this->respondWithSuccess([
            'success' => true,
            'message' => 'User fetched successfully!',
            'user' => $user,
        ]);
    }

    // deleting a user
    public function destroy($id)
    {
        // deleting the user with given id
        $deletedUserId = $this->userService->deleteUser($id);

        // checking if user was deleted successfully
        if ($deletedUserId == -1) {
            return $this->respondForbidden([
                'success' => false,
                'message' => 'Cannot delete admin user',
            ]);
        }

        // returning a success message
        return $this->respondWithSuccess([
            'success' => true,
            'message' => 'User deleted successfully',
            '_id' => $deletedUserId,
        ]);
    }
}
