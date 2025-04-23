<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    public $timestamps = true;


    protected $fillable = [
        'order_id',
        'name',
        'count',
        'pcs', // штук
        'opex',
        'purpose',
        'address',
        'comment',
        'project_price',
        'installation_time',
        'contract_number',
        'contract_date',
    ];

    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

}
