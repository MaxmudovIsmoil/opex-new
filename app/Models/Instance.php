<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instance extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

   public function users()
   {
       return $this->belongsToMany(User::class, 'instance_users', 'instance_id', 'user_id');
   }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:d.m.Y H:i:s',
            'updated_at' => 'datetime:d.m.Y H:i:s'
        ];
    }


}
