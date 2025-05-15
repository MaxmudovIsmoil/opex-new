<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderActionRequest;
use App\Services\OrderActionService;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class OrderActionController extends Controller
{
    public function __construct(
        public OrderActionService $service
    ) {}

    public function index(int $orderId)
    {
        return $this->service->list($orderId);
    }

    public function action(int $orderId, OrderActionRequest $request)
    {
        if(Auth::user()->lkc && !$request->lkc && $request->status == 2) {
            return response()->fail("LKC is required");
        }
        
        if(Auth::user()->isFinance && !$request->payback && $request->status == 2){
            return response()->fail("Payback required");
        }
        return $this->service->action($orderId, $request->validated());
    }


}
