<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DepartmentStoreRequest;
use App\Http\Requests\Admin\DepartmentUpdateRequest;
use Illuminate\Http\JsonResponse;
use App\Services\Admin\DepartmentService;

class DepartmentController extends Controller
{
    public function __construct(
        private DepartmentService $service
    ) {}

    public function all(): JsonResponse
    {
        return $this->service->all();
    }

    public function getOne(int $userId): JsonResponse
    {
        return $this->service->getOne($userId);

    }

    public function store(DepartmentStoreRequest $request): JsonResponse
    {
        return $this->service->store($request->validated());
    }

    public function update(int $id, DepartmentUpdateRequest $request): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }


    public function destroy(int $id)
    {
        return $this->service->destroy($id);
    }
}
