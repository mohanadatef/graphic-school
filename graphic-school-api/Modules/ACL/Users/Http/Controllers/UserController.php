<?php

namespace Modules\ACL\Users\Http\Controllers;

use App\Support\Controllers\BaseController;
use Modules\ACL\Users\Http\Requests\ListUserRequest;
use Modules\ACL\Users\Http\Requests\StoreUserRequest;
use Modules\ACL\Users\Http\Requests\UpdateUserRequest;
use Modules\ACL\Users\Http\Resources\UserResource;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Users\Application\UseCases\CreateUserUseCase;
use Modules\ACL\Users\Application\UseCases\UpdateUserUseCase;
use Modules\ACL\Users\Application\UseCases\DeleteUserUseCase;
use Modules\ACL\Users\Application\UseCases\ListUsersUseCase;
use Modules\ACL\Users\Application\UseCases\ShowUserUseCase;
use Modules\ACL\Users\Application\DTOs\CreateUserDTO;
use Modules\ACL\Users\Application\DTOs\UpdateUserDTO;
use Modules\ACL\Users\Application\DTOs\ListUsersDTO;

class UserController extends BaseController
{
    public function index(ListUserRequest $request, ListUsersUseCase $useCase)
    {
        $dto = ListUsersDTO::fromArray([
            'page' => $request->integer('page', 1),
            'per_page' => $request->integer('per_page', 15),
            'sort_by' => $request->string('sort_by'),
            'sort_order' => $request->string('sort_order'),
            'search' => $request->string('search'),
            'filters' => $request->input('filters', []),
            'date_from' => $request->date('date_from'),
            'date_to' => $request->date('date_to'),
        ]);

        $paginator = $useCase->execute($dto);

        return $this->paginated(
            UserResource::collection($paginator),
            'Users retrieved successfully'
        );
    }

    public function store(StoreUserRequest $request, CreateUserUseCase $useCase)
    {
        $dto = CreateUserDTO::fromArray($request->validated());
        $user = $useCase->execute($dto);

        return $this->created(
            UserResource::make($user),
            'User created successfully'
        );
    }

    public function show(User $user, ShowUserUseCase $useCase)
    {
        $user = $useCase->execute($user);

        return $this->success(
            UserResource::make($user),
            'User retrieved successfully'
        );
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserUseCase $useCase)
    {
        $dto = UpdateUserDTO::fromArray($request->validated());
        $user = $useCase->execute([$user, $dto]);

        return $this->success(
            UserResource::make($user),
            'User updated successfully'
        );
    }

    public function destroy(User $user, DeleteUserUseCase $useCase)
    {
        $useCase->execute($user);

        return $this->success(
            null,
            'User deleted successfully'
        );
    }
}
