<?php

namespace App\Services\Admin;

use App\Models\Department;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class DepartmentService
{
    public function __construct(
        protected Department $model,
    ) {}


    public function all(): JsonResponse
    {
        try {
            $instnaces = $this->model->orderByDesc('id')
                ->get()
                ->toArray();

            return response()->success($instnaces);
        }
        catch (\Exception $e)  {
            return response()->fail($e->getMessage());
        }
    }

    public function getOne(int $instanceId): JsonResponse
    {
        try {
            return response()->success($this->model->findOrFail($instanceId));
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
            $instance = $this->model->findOrFail($id);

            // Faqat kerakli maydonlarni olish
            $allowedFields = ['name_en', 'name_ru'];
            $filteredData = array_intersect_key($data, array_flip($allowedFields));

            if (!empty($filteredData)) {
                $instance->update($filteredData);
            }
           
            return response()->success($instance);
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
