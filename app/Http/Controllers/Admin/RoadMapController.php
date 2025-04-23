<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoadMapRequest;
use Illuminate\Http\JsonResponse;
use App\Services\Admin\RoadMapService;

class RoadMapController extends Controller
{
    public function __construct(
        private RoadMapService $service
    ) {}

    public function all(): JsonResponse
    {
        return $this->service->all();
    }

    public function getOne(int $roadMapId): JsonResponse
    {
        return $this->service->getOne($roadMapId);

    }

    public function store(RoadMapRequest $request): JsonResponse
    {
        return $this->service->store($request->validated());
    }

    public function update(int $id, RoadMapRequest $request): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }


    public function destroy(int $id)
    {
        return $this->service->destroy($id);
    }
}
