<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'order_types';

    protected $fillable = [
        'name_ru',
        'name_en',
    ];

    public $timestamps = true;


    public function roadMap()
    {
        return $this->hasMany(RoadMap::class, 'order_type_id', 'id')
            ->orderBy('stage', 'ASC');
    }


}
