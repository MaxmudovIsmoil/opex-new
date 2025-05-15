<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    // 'user_id',
    // 'order_type_id',
    // 'comment',
    // 'copy_to_email',
    // 'status',
    // 'registration_number',
    // 'registration_date',
    // 'registration_created_at',
    // 'deleted_at',
    // 'stage_count',
    // 'current_stage',
    // 'current_instance_id',
    // 'created_at',
    // 'updated_at',

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function instance()
    {
        return $this->hasOne(Instance::class, 'id', 'instance_id');
    }

    public function currentInstance()
    {
        return $this->hasOne(Instance::class, 'id', 'current_instance_id');
    }

    public function orderAction()
    {
        return $this->hasMany(OrderAction::class, 'id', 'order_id');
    }


    public function RoadMap()
    {
        return $this->hasMany(RoadMap::class, 'order_type_id');
    }


    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'created_at' => 'datetime:d.m.Y H:i:s',
            'updated_at' => 'datetime:d.m.Y H:i:s',
        ];
    }

    public function isOwner($user)
    {
        return $this->user_id === $user->id & $this->current_stage === 1; 
    }


    // public static function isDuplicate($data)
    // {
    //     return self::where('date', $data['date'])
    //         ->where('client', $data['client'])
    //         ->where('address', $data['address'])
    //         ->exists();
    // }
    
    
}
