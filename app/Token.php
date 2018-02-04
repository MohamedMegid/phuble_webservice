<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
     protected $fillable = [
        'user_id','user_token','device_token'
    ];
}
