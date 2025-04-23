<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Http\Controllers\SendMailController;
use App\Models\Order;
use App\Models\OrderAction;
use App\Models\RoadMap;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\InstanceUser;
use App\Models\OrderRoadMapRun;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\FileTrait;


class OrderService
{
    use FileTrait;

    public function __construct(
        protected Order $model
    ) {}

    public function userInstanceIds(): array
    {
        return InstanceUser::where('userId', Auth::id())->pluck('instanceId')->toArray();
    }


    // public function getAll(?int $limit = null): JsonResponse
    // {
    //     try {
    //         $userId = Auth::id();

    //         $orders = Order::whereExists(function ($query) {
    //                 $query->select(DB::raw(1))
    //                     ->from('order_type_instances')
    //                     ->whereColumn('orders.order_type_id', 'order_type_instances.order_type_id')
    //                     ->whereColumn('order_type_instances.stage', '<=', 'orders.current_stage');
    //             })
    //             ->whereNull('deleted_at')
    //             ->orderBy('id', 'asc')
    //             ->paginate($limit ?? 50);

    //         // Har bir orderga `is_checkable` flagini qoshish
    //         $orders->getCollection()->transform(function ($order) {
    //             $order->is_checkable = self::checkOrderActionComment($order);
    //             return $order;
    //         });

    //         return response()->success($orders);
    //     } catch (\Exception $e) {
    //         return response()->fail($e->getMessage());
    //     }
    // }

    

    // public static function checkOrderActionComment(object $order): bool
    // {
    //     if ($order->status->isStopped() || $order->status->isCompleted()) {
    //         return false;
    //     }

    //     $userId = Auth::id();

    //     // Agarda buyurtma 1-bosqichda bo'lsa va foydalanuvchiga tegishli bo'lsa, true qaytaramiz
    //     if ($order->current_stage == 1) {
    //         return $order->userId == $userId;
    //     }

    //     $orderRoadMap = RoadMap::where('order_type_id', $order->order_type_id)
    //         ->where('stage', $order->current_stage)
    //         ->first();

    //     if (!$orderRoadMap) {
    //         return false;
    //     }

    //     $orderRoadMapInstanceId = $orderRoadMap->instanceId;
    //     $instanceUsers = InstanceUser::where('instance_id', $orderRoadMapInstanceId)->pluck('user_id');

    //     if (!is_array($instanceUsers)) {
    //         return false; // Agarda bu array bo'lmasa, xatolik chiqmasligi uchun tekshiramiz
    //     }

    //     // Agarda `users` maydoni array bo'lmasa, false qaytaramiz
    //     if (!in_array($userId, $instanceUsers, true)) {
    //         return false;
    //     }

    //     // Eng so‘nggi `OrderAction` ni olish
    //     $orderAction = OrderAction::where('orderId', $order->id)->latest('id')->first();

    //     // Agarda `OrderAction` topilmasa yoki u noto'g'ri bosqichga tegishli bo'lsa
    //     if (!$orderAction || (($orderAction->instanceId !== $orderRoadMapInstanceId) && ($orderAction->stage !== $order->current_stage))) {
    //         return true;
    //     }

    //     // OrderAction statusini tekshiramiz
    //     return in_array($orderAction->status, [
    //         OrderStatus::RESEND_PREVIOUS->value, 
    //         OrderStatus::RESEND_CREATOR->value
    //     ], true);
    // }

    public function getAll(?int $limit = 50):  JsonResponse
    {
        try {
            $userId = Auth::id();

            $orders = Order::select(
                'orders.id', 
                    'orders.opex', 
                    'orders.status', 
                    'orders.copy_to_email', 
                    'orders.project_price', 
                    'orders.current_stage', 
                    'orders.stage_count',
                    'orders.created_at',
                    'order_actions.time_signed',
                    'orders.comment',
                    'users.full_name', 
                    'order_types.name_en as department_name', 
                    'instances.name_en as current_instance_name'
                )
                ->whereExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('order_type_instances')
                        ->whereColumn('orders.order_type_id', 'order_type_instances.order_type_id')
                        ->whereColumn('order_type_instances.stage', '<=', 'orders.current_stage');
                })
                ->join('users', 'users.id', '=', 'orders.user_id')
                ->join('order_types', 'order_types.id', '=', 'orders.order_type_id')
                ->join('instances', 'instances.id', '=', 'orders.current_instance_id')
                ->join('order_actions', 'order_actions.order_id', '=', 'orders.id')
                ->whereNull('orders.deleted_at')
                ->orderBy('id', 'asc')
                ->paginate($limit);

            $orderIds = $orders->pluck('id')->toArray();
            $roadMaps = RoadMap::whereIn('order_type_id', $orders->pluck('order_type_id'))
                ->whereIn('stage', $orders->pluck('current_stage'))
                ->get()
                ->keyBy(fn($item) => $item->order_type_id . ':' . $item->stage);

            $instanceUserMap = InstanceUser::whereIn('instance_id', $roadMaps->pluck('instance_id'))
                ->get()
                ->groupBy('instance_id');

            $latestActions = OrderAction::whereIn('order_id', $orderIds)
                ->orderBy('id', 'desc')
                ->get()
                ->keyBy('order_id');

            $orders->getCollection()->transform(function ($order) use ($userId, $roadMaps, $instanceUserMap, $latestActions) {
                $order->is_checkable = $this->isCheckable($order, $userId, $roadMaps, $instanceUserMap, $latestActions);
                return $order;
            });

            return response()->success($orders);
        }

        catch (\Exception $e) {
            return response()->fail($e->getMessage());
        }
    }

    private function isCheckable($order, int $userId, Collection $roadMaps, Collection $instanceUserMap, Collection $latestActions): bool
    {
        if ($order->status->isStopped() || $order->status->isCompleted()) {
            return false;
        }

        if ($order->current_stage == 1) {
            return $order->user_id == $userId;
        }

        $key = $order->order_type_id . ':' . $order->current_stage;
        $roadMap = $roadMaps[$key] ?? null;

        if (!$roadMap) {
            return false;
        }

        $users = $instanceUserMap[$roadMap->instance_id] ?? collect();
        if (!$users->pluck('user_id')->contains($userId)) {
            return false;
        }

        $lastAction = $latestActions[$order->id] ?? null;
        if (!$lastAction || ($lastAction->instance_id != $roadMap->instance_id && $lastAction->stage != $order->current_stage)) {
            return true;
        }

        return in_array($lastAction->status, [
            OrderStatus::RESEND_PREVIOUS->value,
            OrderStatus::RESEND_CREATOR->value,
        ], true);
    }



    public function one(int $id): JsonResponse
    {
        try {
            $order = $this->model::findOrfail($id);
            return response()->success($order);
        }
        catch (\Exception $e) {
           return response()->fail($e->getMessage());
        }
    }

    public function store( $data): JsonResponse
    {
        // if (Order::isDuplicate($data)) {
        //     return response()->fail(['order_unique' => trans('text.order_unique')], 400);
        // }

        try {
            DB::beginTransaction();

                $roadId = Auth::user()->roadId;
                $roadMapCount = RoadMap::where('roadId', $roadId)->count();
                $currentInstanceId = RoadMap::where(['roadId' => $roadId, 'stage' => 2])->first()->instanceId;

                $orderId = $this->model::insertGetId([
                    'roadId' => $roadId,
                    'userId' => Auth::id(),
                    'instanceId' => Auth::user()->instanceId,
                    'date' => date('d.m.Y', strtotime($data['date'])),
                    'client' => $data['client'],
                    'address' => $data['address'],
                    'constructionWork' => $data['constructionWork'] ?? "",
                    'equipmentMaterial' => $data['equipmentMaterial'],
                    'consumable' => $data['consumable'],
                    'work' => $data['work'] ?? '',
                    'contract' => $data['contract'],
                    'comment' => $data['comment'] ?? "",
                    'currentStage' => 2,
                    'allStage' => $roadMapCount,
                    'currentInstanceId' => $currentInstanceId
                ]);

                $this->orderRoadMapRunSave($orderId, $roadId);

                if(!empty($data['files'])) {
                    $this->saveOrderFiles($orderId, $data['files']);
                }

                OrderAction::create([
                    'orderId' => $orderId,
                    'roadId' => Auth::user()->roadId,
                    'stage' => 1,
                    'userId' => Auth::id(),
                    'instanceId' => Auth::user()->instanceId,
                    'status'    => 2,
                    'comment' => $data['comment'] ?? "",
                ]);
                // send next stage users
                SendMailController::created($orderId, $data['comment']);
                
            DB::commit();
            return response()->success('ok');

        } catch (\Exception $e) {
            DB::rollBack();
           // $this->telegramService->sendError($e);
            return response()->fail($e->getMessage());
        }
    }

    protected function orderRoadMapRunSave(int $orderId, int $roadId): void
    {
        $roadMaps = RoadMap::with(['instanceUsers' => function ($query) {
                $query->select('instanceId', 'userId');
            }])
            ->where('roadId', $roadId)
            ->get()
            ->map(function ($roadMaps) {
                $roadMaps->instanceUsers = $roadMaps->instanceUsers->pluck('userId')->toArray();
                return $roadMaps;
            })
            ->toArray();

        foreach($roadMaps as $roadMap) {
            OrderRoadMapRun::create([
                'orderId' => $orderId,
                'roadId' => $roadId,
                'stage' => $roadMap['stage'],
                'instanceId' => $roadMap['instanceId'],
                'users' => $roadMap['instanceUsers'],
            ]);
        }
    }



    public function destroy(int $id): JsonResponse
    {
        try {
            return response()->success($this->model::destroy($id));
        }
        catch (\Exception $e) {
            $this->telegramService->sendError($e);
            return response()->fail($e->getMessage());
        }
    }


    public function search(array $data): JsonResponse
    {
        try {
            $result = $this->model::select(
                'orders.*',
                'users.name as userName',
                'roads.name as roadName',
                'i.name as instanceName',
                'ins.name as currentInstanceName'
            )
            ->join('users', 'users.id', '=', 'orders.userId')
            ->join('instances as i', 'i.id', '=', 'orders.instanceId')
            ->join('instances as ins', 'ins.id', '=', 'orders.currentInstanceId')
            ->join('roads', 'roads.id', '=', 'orders.roadId')
            ->where('orders.'.$data['type'], 'LIKE', '%'.$data['name'].'%')
            ->get();

            return response()->success($result);
        }
        catch (\Exception $e) {
            $this->telegramService->sendError($e);
            return response()->fail($e->getMessage());
        }
    }


}
