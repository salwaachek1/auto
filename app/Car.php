<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
     protected $table = 'cars';
     protected $fillable = [
        'model', 'place', 'carburant_id','kilo','disponibility','state','photo_url'
    ];
  
    public function carburant()
    {
        return $this->belongsTo('App\Carburant');
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'activities','car_id','user_id');
    }
}
