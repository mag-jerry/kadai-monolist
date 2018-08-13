<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['code','name','url','imag_url'];
    
    public function users() {
        return $this->belongsToMany(User::class)->withpivot('type')->withTimestamps();
    }
    
    public function want_users() {
        return $this->users()->where('type','want');
    }
}
