<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FbResponse extends Model
{
    protected $fillable = [
        'facebook_id', 'response', 'scope',
        
    ];
    
    public function userFbToken()
    {
        return $this->belongsTo(App\UserFbtoken::class);
    }
}
