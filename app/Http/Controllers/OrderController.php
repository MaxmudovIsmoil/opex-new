<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\SearchRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $service
    ) {}

    public function index(?int $limit = 50): JsonResponse
    {
        return $this->service->getAll(limit: $limit);
    }



    public function getOne(int $id): JsonResponse
    {
        return $this->service->one($id);
    }

    /**
     * @param OrderStoreRequest $request
     * @return JsonResponse
     */
    public function store(OrderStoreRequest $request): JsonResponse
    {
        return $this->service->store($request->validated());
    }

    


    public function search(SearchRequest $request): JsonResponse
    {
        return $this->service->search($request->validated());
    }

}
