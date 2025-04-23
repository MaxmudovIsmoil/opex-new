<?php

namespace App\Services\Admin;


use App\Models\Department;
use App\Models\RoadMap;
use App\Models\InstanceUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RoadMapService
{
    public function __construct(
        protected RoadMap $roadMap,
        protected Department $department,
    ) {}


    public function all()
    {
        try {
           
            $all = $this->department->select('order_types.id', 'order_types.name_en')
                ->with(['roadMap' => function ($query) {
                    $query->select(
                        'order_type_instances.id', 
                        'order_type_instances.stage',
                        'order_type_instances.order_type_id',
                        'order_type_instances.instance_id',
                        'instances.name_en as instanceName', 
                        DB::raw("STRING_AGG(COALESCE(users.full_name, ''), ', ') as users")
                    )
                    ->leftJoin('instances', 'instances.id', 'order_type_instances.instance_id')
                    ->leftJoin('instance_users', 'instance_users.instance_id', 'instances.id')
                    ->leftJoin('users', 'users.id', 'instance_users.user_id')
                    ->groupBy([
                        'order_type_instances.id', 
                        'order_type_instances.order_type_id', 
                        'order_type_instances.stage', 
                        'order_type_instances.instance_id',
                        'instances.name_en'
                    ])
                    ->orderBy('stage');
                }])
                ->orderBy('order_types.id')
                ->get();

            return response()->success($all);
        }
        catch (\Exception $e) {
            return response()->fail($e->getMessage());
        }
    }


    public function getOne(int $roadMapId): JsonResponse
    {
        try {
            $roadMap = $this->roadMap->findOrFail($roadMapId);
            $roadMap->userIds = $roadMap->instanceUsers()->pluck('user_id')->all();

            return response()->success($roadMap);
        }
        catch (\Exception $e) {
            return response()->fail($e->getMessage());
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            DB::beginTransaction();
                $roadMap = $this->roadMap->create([
                    'order_type_id' => $data['roadId'],
                    'instance_id' => $data['instanceId'],
                    'stage' => $data['stage']
                ]);

                if(!empty($data['userIds'])) {
                    InstanceUser::where('instance_id', $data['instanceId'])->delete();
                    foreach ($data['userIds'] as $userId) {
                        InstanceUser::create([
                            'instance_id' => $data['instanceId'],
                            'user_id' => $userId
                        ]);
                    }
                }
            DB::commit();
            return response()->success($roadMap);
        }
        catch(\Exception $e) {
            DB::rollBack();
            return response()->fail($e->getMessage());
        }
    }

    public function update(int $id, array $data): JsonResponse
    {
        try {
            DB::beginTransaction();
                $this->roadMap->findOrfail($id)
                    ->update([
                        'instance_id' => $data['instanceId'],
                        'stage' => $data['stage']
                    ]);
                if(!empty($data['userIds'])) {
                    InstanceUser::where('instance_id', $data['instanceId'])->delete();
                    foreach ($data['userIds'] as $userId) {
                        InstanceUser::create([
                            'instance_id' => $data['instanceId'],
                            'user_id' => $userId
                        ]);
                    }
                }
            DB::commit();

            return response()->success($data);
        }
        catch(\Exception $e) {
            DB::rollBack();
            return response()->fail($e->getMessage());
        }
    }


    public function destroy(int $id)
    {
        try {
            $this->roadMap->destroy($id);
            return response()->success($id);
        }
        catch(\Exception $e) {
            return response()->fail($e->getMessage());
        }
    }

}
