<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reparation extends Model
{
    protected $table = 'reparations';
     protected $fillable = [
        'garage', 'diagnosis','car_id','replaced_parts','fees','date_out','phone','is_done'
    ];
  
    public function car()
    {
        return $this->belongsTo('App\Car');
    }
    
}
