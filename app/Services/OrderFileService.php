<?php

namespace App\Services;


use App\Models\OrderFile;
use App\Models\OrderRoadMapRun;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\FileTrait;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use App\Models\Order;
use App\Models\OrderAction;

use App\Services\TelegramService;

class OrderFileService
{
    use FileTrait;
    
    public function __construct(
        private OrderFile $model
    ) {}

    

    public function store(array $data): JsonResponse
    {   
        try {
            DB::beginTransaction();
                $userId = Auth::id(); 
                $fileName = $this->uploadFile($data['file']);

                $orderId = $this->model::insertGetId([
                    'orderId' => $data['orderId'],
                    'userId' => $userId,
                    'file'   => $fileName,
                    'created_at' => Carbon::now(),
                ]);

                $orderData = $this->model::leftJoin('users', 'users.id', '=', 'order_files.userId')
                    ->select(
                        'order_files.id', 
                        'users.name', 'users.position', 
                        'order_files.file', 'order_files.created_at'
                    )
                    ->where('order_files.id', $orderId)
                    ->firstOrFail()
                    ->toArray();

            DB::commit();

            return response()->success($orderData);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order File Store Error: '.$e->getMessage());
            return response()->fail($e->getMessage());
        }
    }



    public function destroy(int $id): JsonResponse
    {
        try {
            $file = $this->model::findOrfail($id);
            $this->fileDelete($file->file);
            $file->delete();
            return response()->success($id);
        }
        catch (\Exception $e) {
            return response()->fail($e->getMessage());
        }
    }

    
}
