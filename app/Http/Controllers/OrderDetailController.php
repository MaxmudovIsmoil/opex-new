<?php

namespace App\Http\Controllers;


use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\SearchRequest;
use App\Services\OrderDetailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class OrderDetailController extends Controller
{
    public function __construct(
        protected OrderDetailService $service
    ) {}

    public function index(int $status = null)
    {
    
        // return $this->service->getOrders();;
    }

    

    public function getOne(int $id): JsonResponse
    {
        // return $this->service->one($id);
    }

    /**
     * @param OrderStoreRequest $request
     * @return JsonResponse
     */
    public function store(OrderStoreRequest $request): JsonResponse
    {
        // return $this->service->store($request->validated());
    }

    


    public function search(SearchRequest $request): JsonResponse
    {
        // return $this->service->search($request->validated());
    }

}
