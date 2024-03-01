<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Jobs\UserCreated;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    protected Model $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }
    

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->only('email', 'first_name', 'last_name');

        try {
            $user = $this->model->create($data);

            UserCreated::dispatch($user->toArray());

        } catch (\Exception $ex) {
            $message = sprintf("File: %s Line: %s Message: %s", $ex->getFile(), $ex->getLine(), $ex->getMessage());
            Log::error($message);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again later.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ]);
    }

    public function index(): JsonResponse
    {
        $users = $this->model->all();

        return response()->json([
            'message' => 'List of users.',
            'data' => $users,
        ]);
    }
}
