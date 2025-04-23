<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class InstanceUser extends Model
{
    use HasFactory;

    protected $table = 'instance_users';

    public $timestamps = false;

    protected $fillable = [
        'instance_id',
        'user_id',
    ];



    // public function user()
    // {
    //     return $this->hasOne(User::class, 'id', 'user_id');
    // }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }



}
