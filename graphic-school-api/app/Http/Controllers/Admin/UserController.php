<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\ListUserRequest;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function index(ListUserRequest $request)
    {
        return UserResource::collection(
            $this->userService->paginate(
                $request->validated(),
                $request->integer('per_page', 10)
            )
        );
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->create($request->validated());

        return UserResource::make($user)
            ->response()
            ->setStatusCode(201);
    }

    public function show(User $user)
    {
        return UserResource::make($user->load('role'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user = $this->userService->update($user, $request->validated());

        return UserResource::make($user);
    }

    public function destroy(User $user)
    {
        $this->userService->delete($user);

        return response()->json(['message' => 'Deleted']);
    }
}
