<?php

namespace App\Services\Admin;

use App\Models\OrderType;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderTypeService
{
    public function __construct(
        protected OrderType $model,
    ) {}


    public function all(): JsonResponse
    {
        try {
            $orderTypes = $this->model->orderByDesc('id')
                ->get()
                ->toArray();

            return response()->success($orderTypes);
        }
        catch (\Exception $e)  {
            return response()->fail($e->getMessage());
        }
    }

    public function getOne(int $id): JsonResponse
    {
        try {
            return response()->success($this->model->findOrFail($id));
        }
        catch (\Exception $e) {
            return response()->fail($e->getMessage());
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            $this->model->create([
                'name_en' => $data['name_en'],
                'name_ru' => $data['name_ru'],
            ]);
            return response()->success('ok');
        }
        catch (\Exception $e) {
            return response()->fail($e->getMessage());
        }
    }



    public function update(int $id, array $data): JsonResponse
    {
        try {
            $orderType = $this->model->findOrFail($id);

            // Faqat kerakli maydonlarni olish
            $allowedFields = ['name_en', 'name_ru', 'status'];
            $filteredData = array_intersect_key($data, array_flip($allowedFields));

            if (!empty($filteredData)) {
                $orderType->update($filteredData);
            }
           
            return response()->success($orderType);
        } 
        catch (ModelNotFoundException $e) {
            return response()->fail('Record not found.', 404);
        }
        catch (\Exception $e) {
            return response()->fail($e->getMessage(), 500);
        }
    }




    public function destroy(int $id): JsonResponse
    {
        try {
            $this->model->destroy($id);
            return response()->success($id);
        }
        catch (\Exception $e) {
            return response()->fail($e->getMessage());
        }
    }
}
