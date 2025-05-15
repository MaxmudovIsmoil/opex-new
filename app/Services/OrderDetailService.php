<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderChange;
use App\Models\OrderFile;
use App\Models\User;
use App\Models\OrderAction;
use App\Models\OrderRoadMapRun;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderDetailService
{
    public function __construct(
        protected Order $model
    ) {}

    public function order(int $orderId): object
    {
        $userId = Auth::id();

        $orderExists = Order::join('order_road_map_runs', 'orders.id', '=', 'order_road_map_runs.orderId')
            ->where('orders.id', $orderId)
            ->whereJsonContains('order_road_map_runs.users', $userId)
            // ->whereRaw('orders.currentStage - order_road_map_runs.stage <= ?', [2])
            ->exists();

        if ($orderExists) {
            return $this->model::query()
                ->select('roads.name as roadName', 'orders.*', 'instances.name as instanceName', 'users.name as userName')
                ->leftJoin('instances', 'instances.id', '=', 'orders.instanceId')
                ->leftJoin('users', 'users.id', '=', 'orders.userId')
                ->leftJoin('roads', 'roads.id', '=', 'orders.roadId')
                ->where('orders.id', $orderId)
                ->firstOrFail();
        }

        abort(404, 'Order not found or does not meet the criteria.');
    }


    public function orderAction(int $orderId): array
    {
        return OrderAction::select('order_actions.*', 'instances.name as instanceName', 'users.name as userName')
            ->where('orderId', $orderId)
            ->leftJoin('instances', 'instances.id', 'order_actions.instanceId')
            ->leftJoin('users', 'users.id', 'order_actions.userId')
            ->orderBy('id')->get()->toArray();
    }

    public function orderRoadMapRun(int $orderId): array
    {
        $results = OrderRoadMapRun::query()
            ->leftJoin('instances', 'instances.id', '=', 'order_road_map_runs.instanceId')
            ->leftJoinSub(
                DB::table('order_actions as oa1')
                    ->select(
                        'oa1.orderId',
                        'oa1.stage',
                        'oa1.status',
                        'oa1.created_at'
                    )
                    ->whereIn(
                        DB::raw('(oa1.orderId, oa1.stage, oa1.created_at)'),
                        DB::table('order_actions as oa2')
                            ->select(
                                'oa2.orderId',
                                'oa2.stage',
                                DB::raw('MAX(oa2.created_at) as latest_action_date')
                            )
                            ->groupBy('oa2.orderId', 'oa2.stage')
                    ),
                'latest_oa',
                function ($join) {
                    $join->on('latest_oa.orderId', '=', 'order_road_map_runs.orderId')
                        ->on('latest_oa.stage', '=', 'order_road_map_runs.stage');
                }
            )
            ->select(
                'order_road_map_runs.*',
                'instances.name as instanceName',
                'instances.timeLine',
                'latest_oa.status as actionStatus',
                'latest_oa.stage as actionStage'
            )
            ->where('order_road_map_runs.orderId', $orderId)
            ->orderBy('order_road_map_runs.stage', 'asc')
            ->get()
            ->map(function ($orderRoadMapRun) {
                // Extract user IDs and retrieve user names
                $userIds = $orderRoadMapRun->users;
                $userNames = User::whereIn('id', $userIds)->pluck('name')->toArray();
                $orderRoadMapRun->userNames = implode(', ', $userNames);
                return $orderRoadMapRun;
            })
            ->toArray();

                
        return $results;
    }

    public function orderRoadMapRunActions($order): array
    {
        $result = DB::table('order_road_map_runs as orm')
            ->select(
                'orm.*',
                'instances.name as instance_name',
                'oa.stage as order_action_stage',
                'oa.status',
                'oa.created_at as last_action_date'
            )
            ->leftJoin('instances', 'instances.id', '=', 'orm.instanceId')
            ->leftJoinSub(
                DB::table('order_actions as oa1')
                    ->select('oa1.instanceId', DB::raw('MAX(oa1.created_at) as max_date'))
                    ->where('oa1.status', '=', 2)
                    ->groupBy('oa1.instanceId'),
                'latest_oa',
                'latest_oa.instanceId',
                '=',
                'orm.instanceId'
            )
            ->leftJoin('order_actions as oa', function ($join) use ($order) {
                $join->on('oa.instanceId', '=', 'orm.instanceId')
                    ->on('oa.created_at', '=', 'latest_oa.max_date')
                    ->where('oa.stage', '>=', $order->currentStage); 
            })
            ->where('orm.stage', '>=', 2)
            ->where('orm.orderId', $order->id)
            ->get()
            ->toArray();
            
        return $result;
    }

    // public function orderRoadMapRunActions($order): array
    // {
    //     $results = DB::table('order_road_map_runs as orm')
    //         ->select(
    //             'orm.*',
    //             'i.name as instance_name',
    //             'u.username','u.position', 'u.name',
    //             'oa.stage as order_action_stage',
    //             'oa.status',
    //             'oa.created_at as last_action_date'
    //         )
    //         ->join('instances as i', 'orm.instanceId', '=', 'i.id')
    //         ->leftJoin('instance_users as iu', 'i.id', '=', 'iu.instanceId') 
    //         ->leftJoin('users as u', 'iu.userId', '=', 'u.id') 
    //         ->leftJoin('orders as o','orm.orderId','=','o.id')
    //         ->leftJoin(DB::raw('(SELECT orderId, instanceId, MAX(created_at) AS last_action_date FROM order_actions GROUP BY orderId, instanceId) as latest_actions'), function($join) {
    //             $join->on('orm.orderId', '=', 'latest_actions.orderId')
    //                 ->on('orm.instanceId', '=', 'latest_actions.instanceId');
    //         })
    //         ->leftJoin('order_actions as oa', function($join) {
    //             $join->on('latest_actions.orderId', '=', 'oa.orderId')
    //                 ->on('latest_actions.instanceId', '=', 'oa.instanceId')
    //                 ->on('latest_actions.last_action_date', '=', 'oa.created_at');
    //         })
    //         ->where('orm.orderId', $order->id)
    //         ->orderBy('orm.stage')
    //         ->get()
    //         ->map(function($item) use ($order) {
    //             if ($item->stage > $order->currentStage) {
    //                 $item->status = null;
    //             }
    //             return $item;
    //         })
    //         ->toArray();
            
    //     return $results;
    // }

    public function orderFiles(int $orderId): array
    {
        $data = OrderFile::select(
            'users.name',
            'users.position',
            'order_files.id',
            'order_files.file',
            'order_files.userId',
            'order_files.created_at'
        )
        ->leftJoin('users', 'users.id', '=', 'order_files.userId')
        ->where('order_files.orderId', $orderId)
        ->get()
        ->toArray();
        return $data;
    }


    public function update(int $id, array $data): JsonResponse
    {
        try {
            DB::beginTransaction();
                $order = $this->model::findOrFail($id);
                $resendStatus = $order->resendStatus;

                if (isset($data['date']) && ($order->date != $data['date'])) {
                    $changeData['oldDate'] = $order->date;
                    $changeData['newDate'] = $data['date'];
                    $changeData['orderId'] = $id;
                    $changeData['userId'] = Auth::id();

                    $order->fill(['date' => date('d.m.Y', strtotime($data['date']))]);
                    $resendStatus = 1;
                }

                if (isset($data['client']) && ($order->client != $data['client'])) {
                    $changeData['oldClient'] = $order->client;
                    $changeData['newClient'] = $data['client'];
                    $changeData['orderId'] = $id;
                    $changeData['userId'] = Auth::id();

                    $order->fill(['client' => $data['client']]);
                    $resendStatus = 1;
                }

                if (isset($data['address']) && ($data['address'] != $order->address)) {
                    $changeData['oldAddress'] = $order->address;
                    $changeData['newAddress'] = $data['address'];
                    $changeData['orderId'] = $id;
                    $changeData['userId'] = Auth::id();

                    $order->fill(['address' => $data['address']]);
                    $resendStatus = 1;
                }

                if (isset($data['equipmentMaterial']) && ($data['equipmentMaterial'] != $order->equipmentMaterial)) {
                    $changeData['oldEquipmentMaterial'] = $order->equipmentMaterial;
                    $changeData['newEquipmentMaterial'] = $data['equipmentMaterial'];
                    $changeData['orderId'] = $id;
                    $changeData['userId'] = Auth::id();

                    $order->fill(['equipmentMaterial' => $data['equipmentMaterial']]);
                    $resendStatus = 1;
                }


                if (isset($data['consumable']) && ($order->consumable != $data['consumable'])) {
                    $changeData['oldConsumable'] = $order->consumable;
                    $changeData['newConsumable'] = $data['consumable'];
                    $changeData['orderId'] = $id;
                    $changeData['userId'] = Auth::id();

                    $order->fill(['consumable' => $data['consumable']]);
                    $resendStatus = 1;
                }

                if (isset($data['contract'])) {
                    $changeData['oldContract'] = $order->contract;
                    $changeData['newContract'] = $data['contract'];
                    $order->fill(['subscriptionFee' => $data['contract']]);
                    $resendStatus = 1;
                }



                if (isset($data['constructionWork']) && ($order->constructionWork != $data['constructionWork'])) {

                    $changeData['oldConstructionWork'] = $order->constructionWork;
                    $changeData['newConstructionWork'] = $data['constructionWork'];
                    $changeData['orderId'] = $id;
                    $changeData['userId'] = Auth::id();

                    $order->fill(['constructionWork' => $data['constructionWork']]);
                    $resendStatus = 1;
                }

                if (isset($data['comment']) && ($order->comment != $data['comment'])) {
                    $changeData['oldComment'] = $order->comment;
                    $changeData['newComment'] = $data['comment'];
                    $changeData['orderId'] = $id;
                    $changeData['userId'] = Auth::id();
                
                    $order->fill(['comment' => $data['comment']]);
                    $resendStatus = 1;
                }

                $order->fill(['resendStatus' => $resendStatus]);
                $order->save();

                OrderChange::create($changeData);

            DB::commit();
            return response()->success('ok');
        } catch (\Exception $e) {
            DB::rollBack();
            // $this->telegramService->sendError($e);
            return response()->fail($e->getMessage());
        }
    }


    public function orderChanges(int $orderId): array
    {
        $updateds = OrderChange::select('order_changes.*', 'users.name as userName')
            ->join('users', 'users.id', '=', 'order_changes.userId')
            ->where('order_changes.orderId', $orderId)
            ->get()
            ->toArray();

        return $updateds;
    }

}
