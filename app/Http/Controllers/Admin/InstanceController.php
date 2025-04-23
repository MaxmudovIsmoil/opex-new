<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InstanceStoreRequest;
use App\Http\Requests\Admin\InstanceUpdateRequest;
use Illuminate\Http\JsonResponse;
use App\Services\Admin\InstanceService;

class InstanceController extends Controller
{
    public function __construct(
        private InstanceService $service
    ) {}

    public function all(): JsonResponse
    {
        return $this->service->all();
    }

    public function getInstances(): JsonResponse
    {
        return $this->service->getInstances();
    }

    public function getOne(int $userId): JsonResponse
    {
        return $this->service->getOne($userId);

    }

    public function store(InstanceStoreRequest $request): JsonResponse
    {
        return $this->service->store($request->validated());
    }

    public function update(int $id, InstanceUpdateRequest $request): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }


    public function destroy(int $id)
    {
        return $this->service->destroy($id);
    }
}
