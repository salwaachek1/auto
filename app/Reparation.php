<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Reparation extends Model
{
    use SoftDeletes;
    protected $table = 'reparations';
    protected $fillable = [
        'garage', 'diagnosis','car_id','replaced_parts','fees','date_out','phone','is_done'
    ];
  
    public function car()
    {
        return $this->belongsTo('App\Car');
    }
    
}
