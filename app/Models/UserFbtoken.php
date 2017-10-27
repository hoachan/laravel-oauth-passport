<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFbtoken extends Model
{
    protected $fillable = [
        'facebook_id', 'user_id', 'email',
        'token', 'refresh_token',
        
    ];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
