<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderFile extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'order_id',
        'file',
        'created_at',
        'updated_at',
    ];

}
