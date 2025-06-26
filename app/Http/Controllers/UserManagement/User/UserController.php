<?php

namespace App\Http\Controllers\UserManagement\User;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ClientProblem;
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

    public function clientRequestProblem(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $problem = ClientProblem::create([
            'client_id' => auth()->id(),
            'car_id' => $validated['car_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
        ]);

        // Notify all Admins
        $admins = User::role('Admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new ProblemRequested($problem));
        }

        return response()->json(['message' => 'Problem request sent to admin']);
    }

    public function payForClientProblem(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Car Problem Solution Request',
                    ],
                    'unit_amount' => 500 * 100, // 500 Rs in paisa (or $5 if usd)
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('client.problem.success'),
            'cancel_url' => route('client.problem.cancel'),
        ]);

        return response()->json(['url' => $session->url]);
    }
}
