<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
   protected $table = 'activities';
     protected $fillable = [
        'user_id','car_id','is_done','before_photo_url','after_photo_url','before_kilos','after_kilos','expenses','fuel','previous_fuel_amount','after_fuel_amount','destination','returning_day'
    ];
  
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function car()
    {
        return $this->belongsTo('App\Car');
    }
}
