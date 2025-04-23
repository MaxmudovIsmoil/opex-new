<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\BelongsToManyRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadMap extends Model
{
    use HasFactory;

    protected $table = 'order_type_instances';

    public $timestamps = true;

    protected $fillable = [
        'stage',
        'order_type_id',
        'instance_id',
//        'is_deleted'
    ];



    public function instance()
    {
        return $this->hasOne(Instance::class, 'id', 'instance_id');
    }

    public function instanceUsers()
    {
        return $this->hasMany(InstanceUser::class, 'instance_id', 'instance_id');
    }


    public function department()   
    {
        return $this->belongsTo(Department::class, 'order_type_id');
    }


    public function order()   
    {
        return $this->belongsTo(Order::class, 'order_type_id');
    }

}
