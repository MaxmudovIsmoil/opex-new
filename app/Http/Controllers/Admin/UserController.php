<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use Illuminate\Http\JsonResponse;
use App\Services\Admin\UserService;

class UserController extends Controller
{
    public function __construct(
        private UserService $service
    ) {}

    public function all(): JsonResponse
    {
        return $this->service->all();
    }

    public function getUsers(): JsonResponse
    {
        return $this->service->getUsers();
    }

    public function getOne(int $userId): JsonResponse
    {
        return $this->service->getOne($userId);

    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        return $this->service->store($request->validated());
    }

    public function update(int $id, UserUpdateRequest $request): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }


    public function destroy(int $id)
    {
        return $this->service->destroy($id);
    }
}
