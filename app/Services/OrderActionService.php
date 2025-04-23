<?php

namespace App\Services;

use App\Events\OrderUpdated;
use App\Events\OrderUpdatedEvent;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\SSEController;
use App\Http\Resources\CableResource;
use App\Models\OrderAction;
use App\Models\OrderRoadMapRun;
use App\Services\TelegramService;
use App\Models\Order;
use App\Models\CableChange;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Enums\OrderStatus;

class OrderActionService
{
    public function __construct(
        protected OrderAction $model,
        protected TelegramService $telegram
    ) {}

    public function list(int $orderId): JsonResponse
    {
        try {
            $orderAction = $this->model::select(
                    'roads.name as roadName',
                    'instances.name as instanceName',
                    'users.name as userName',
                    'order_actions.*'
                )
                ->leftJoin('roads', 'roads.id', '=', 'order_actions.roadId')
                ->leftJoin('instances', 'instances.id', '=', 'order_actions.instanceId')
                ->leftJoin('users', 'users.id', '=', 'order_actions.userId')
                ->orderBy('stage')
                ->get();

            return response()->success($orderAction);

        } catch (\Exception $e) {
            return response()->fail($e->getMessage());
        }
    }


    public function action(int $orderId, array $data): JsonResponse
    {
        try { 
            DB::beginTransaction();

                $order = Order::findOrfail($orderId);
                $actionInstanceId = $order->currentInstanceId;
                $actionStage = $order->currentStage;

                $resendStatus = $order->resendStatus;
                // resendStatus = 1 ga teng bo'lsa
                // order action dan oxirgi decline qilgan userni topib shu userga qaytadan yuborish kerak
                // va order current stageni currentInstnaceId larni ham to'glirlash kerak  


                $status = 1;
                $currentInstanceId = $order->currentInstanceId;
                $currentStage = $order->currentStage;

                if($data['status'] == OrderStatus::AGREED->value) {
                    
                    // new get currentInstanceId, stage++, status = 1, send email next users
                    if ($order->currentStage == $order->allStage) {                      
                        $status = 3; // Complated
                        SendMailController::agreed($orderId, $order->instanceId,$currentInstanceId, $order->userId, $currentStage, $data['comment']);

                        // $this->telegram->sendOrderMessage($orderId);
                    }
                    else {
                        // $status = 1 processing
                        $currentStage++;
                        $currentInstanceId = $this->getNewCurrentInstanceId($orderId, $currentStage);

                        // send next stage users
                        SendMailController::agreed($orderId, $order->instanceId,$currentInstanceId, $order->userId, $currentStage, $data['comment']);
                    }
                }


                if($data['status'] == OrderStatus::RESEND_PREVIOUS->value) {
                    $currentStage = $order->currentStage-1;
                    $currentInstanceId = $this->getNewCurrentInstanceId($orderId, $currentStage);
                    SendMailController::resendToPrevious($orderId, $order->instanceId,$currentInstanceId, $order->userId, $data['comment']);
                }

                if($data['status'] == OrderStatus::RESEND_CREATOR->value) {
                    $currentStage = 1;
                    $currentInstanceId = $this->getNewCurrentInstanceId($orderId, $currentStage);
                    SendMailController::resendToCreator($orderId, $order->instanceId,$currentInstanceId, $order->userId, 5, $data['comment']);
                }

                if($data['status'] == OrderStatus::STOPPED->value) {
                    $status = 6; // Stopped
                    SendMailController::stopped($orderId, $order->instanceId,$currentInstanceId, $order->userId, $data['comment']);
                }

                $this->model::create([
                    'userId' => Auth::id(),
                    'orderId' => $orderId,
                    'roadId' => $order->roadId,
                    'instanceId' => $actionInstanceId,
                    'stage' => $actionStage,
                    'status' => $data['status'],
                    'comment' => $data['comment']
                ]);

                
                // $this->telegram->sendOrderMessage($orderId);
                // event(new OrderUpdated($order));

                $dataToFill = [
                    'status' => $status,
                    'currentStage' => $currentStage,
                    'currentInstanceId' => $currentInstanceId,
                ];

                $fieldsToMap = [
                    'dueDate' => 'dueDay',
                    'payback' => 'paybackPeriod',
                    'total' => 'total',
                    'lkc' => 'lkc',
                ];
                foreach ($fieldsToMap as $key => $mappedKey) {
                    if (isset($data[$key])) {
                        $dataToFill[$mappedKey] = $data[$key];
                    }
                }

                $order->fill($dataToFill);
                $order->save();

            DB::commit();
            return response()->success('ok');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('action: '. $e->getMessage());
            return response()->json($e->getMessage());
        }
    }


    protected function getNewCurrentInstanceId($orderId, $stage): int|null
    {
        return OrderRoadMapRun::select('instanceId')
            ->where(['orderId' => $orderId, 'stage' => $stage])?->first()
            ->toArray()['instanceId'];
    }

}
