<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class OrderUpdated extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_detail_id',
        'updated_date',
        'user_id',
        'project_price_old',
        'project_price_new',
        'contract_number_old',
        'contract_number_new',
        'contract_date_old',
        'contract_date_new',
        'comment',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function order_detail()
    {
        return $this->hasOne(OrderDetail::class, 'id', 'order_detail_id');
    }

}
