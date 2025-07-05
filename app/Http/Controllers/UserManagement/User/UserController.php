<?php

namespace App\Http\Controllers\UserManagement\User;

use App\Models\User;
use App\Models\Problem;
use Illuminate\Http\Request;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Notifications\ProblemRequested;
use App\Services\UserManagement\User\UserService;
use App\Http\Requests\UserManagement\User\CreateUserRequest;
use App\Http\Requests\UserManagement\User\UpdateUserRequest;
use App\DataTransferObjects\UserManagement\User\CreateUserDto;
use App\DataTransferObjects\UserManagement\User\UpdateUserDto;

class UserController extends Controller
{
    use ApiResponseHelpers;

    public function __construct(private readonly UserService $userService) {}

    // Get all users
    public function index(): JsonResponse
    {
        $users = $this->userService->allUsers();

        return $this->respondWithSuccess([
            'success' => true,
            'message' => 'Users fetched successfully',
            'users' => $users,
        ]);
    }

    // Create new user
    public function store(CreateUserRequest $request)
    {
        $user = $this->userService->addUser(CreateUserDto::fromApiRequest($request));

        return $this->respondCreated([
            'success' => true,
            'message' => 'User created successfully',
            'user' => $user->load('roles.permissions'),
        ]);
    }

    // Update user
    public function update(UpdateUserRequest $request, $id): JsonResponse
    {
        $user = $this->userService->updateUser(UpdateUserDto::fromApiRequest($request), $id);

        return $this->respondWithSuccess([
            'success' => true,
            'user' => $user->load('roles.permissions'),
        ]);
    }

    // Show user
    public function show($id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        return $this->respondWithSuccess([
            'success' => true,
            'message' => 'User fetched successfully!',
            'user' => $user,
        ]);
    }

    // Delete user
    public function destroy($id)
    {
        $deletedUserId = $this->userService->deleteUser($id);

        if ($deletedUserId == -1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete admin user',
            ], 403);
        }

        return $this->respondWithSuccess([
            'success' => true,
            'message' => 'User deleted successfully',
            '_id' => $deletedUserId,
        ]);
    }

    // Client submits a problem request
    public function clientRequestProblem(Request $request)
{
    $user = auth()->user();

    // ✅ Check if the user is authenticated
    if (!$user || !$user->hasRole('Client')) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. Please login as a client.',
        ], 401);
    }

    // ✅ Validate input
    $validated = $request->validate([
        'car_id' => 'required|exists:cars,id',
        'title' => 'required|string',
        'description' => 'nullable|string',
    ]);

    // ✅ Create the problem with the authenticated client's ID
    $problem = Problem::create([
        'client_id' => $user->id,
        'car_id' => $validated['car_id'],
        'title' => $validated['title'],
        'description' => $validated['description'],
    ]);

    // ✅ Notify Admins
    $admins = User::role('Admin')->get();
    foreach ($admins as $admin) {
        $admin->notify(new ProblemRequested($problem));
    }

    return response()->json([
        'success' => true,
        'message' => 'Problem request sent to admin',
        'problem' => $problem,
    ]);
}

}
