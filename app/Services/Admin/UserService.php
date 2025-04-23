<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use function Laravel\Prompts\form;

class UserService
{
    public function __construct(
        protected User $model,
    ) {}

    public function all(): JsonResponse
    {
        try {
            $users = $this->model->select(
                'users.id', 
                'users.full_name', 
                'users.username', 
                'users.email', 
                'users.status', 
                'users.rule', 
                'users.can_create_order', 
                'users.can_order_detail_edit', 
                'users.language', 
                'instances.name_en as instanceName',
                'users.instance_id', 
                'users.order_type_id',
                'order_types.name_en as roadMap',
            )
            ->from('users')
            ->leftJoin('order_types', 'order_types.id', '=','users.order_type_id')
            ->leftJoin('instances', 'instances.id', '=','users.instance_id')
            ->where('rule', 'USER')
            ->orderBy('id', 'DESC')
            ->paginate(20);
            
            return response()->success($users);
        }
        catch (\Exception $e) {
            return response()->fail($e->getMessage());
        }
    }

    public function getUsers(): JsonResponse
    {
        try {
            $users = $this->model->select('users.id', 'users.full_name')
            ->where('rule', 'USER')
            ->where('status', 1)
            ->orderBy('id', 'DESC')
            ->get();
            
            return response()->success($users);
        }
        catch (\Exception $e) {
            return response()->fail($e->getMessage());
        }
    }

    public function getOne(int $userId): JsonResponse
    {
        try {
            $user = $this->model->findOrFail($userId);
            
            return response()->success($user);
        }
        catch (\Exception $e) {
            return response()->fail($e->getMessage());
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            
            $user = $this->model->create([
                'order_type_id' => $data['order_tpye_id'] ?? null,
                'instance_id' => $data['instance_id'],
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'username' => strtolower($data['username']),
                'language' => $data['language'] ?? "ru",
                'can_create_order' => $data['can_create_order'],
                'can_order_detail_edit' => $data['can_order_detail_edit'],
                'status' => $data['status'] ?? 1,
            ]);

            return response()->success($user);
        }
        catch (\Exception $e) {
            return response()->fail($e->getMessage());
        }
    }


    public function update(int $id, array $data): JsonResponse
    {
        try {
            $user = $this->model->findOrFail($id);

            // Faqat ruxsat etilgan maydonlarni olish
            $allowedFields = [
                'order_type_id', 'instance_id', 'full_name', 'email', 'username', 'language', 
                'status', 'can_create_order', 'can_order_detail_edit'
            ];
            $filteredData = array_intersect_key($data, array_flip($allowedFields));

            // Ma'lumotlarni yangilash
            if (!empty($filteredData)) {
                $user->fill($filteredData)->save();
            }

            return response()->success($user);
        }
        catch (\Exception $e) {
            return response()->fail($e->getMessage());
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
