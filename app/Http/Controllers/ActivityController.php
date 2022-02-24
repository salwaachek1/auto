<?php

namespace App\Http\Controllers;
use App\Activity;
use App\Car;
use Illuminate\Http\Request;
 use App\Http\Requests\ActivityStoreRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use App\Http\Traits\ImageTrait;
use Illuminate\Support\Facades\Auth;
class ActivityController extends Controller
{
    use ImageTrait;
  public function index()
    {     
        
        $activities = Activity::get();    
        $cars=Car::get();
        return view('admin.activitieslist')->with(['activities' => $activities,'cars'=>$cars]);
    }
  public function create(ActivityStoreRequest $request,$type_request)
    {
        $act= Activity::firstOrNew(array('id' => $request->id));
        $fileNameToStore = "";
         $type="activity";
         if($type_request=="update"){
            if ($request->hasFile('images')) {
               
                $fileNameToStore =$this->imageStoring($request,$type);
                $act->before_photo_url = $fileNameToStore;
                }
                
        }
        else{
                if ($request->hasFile('images')) {
                    
                    $fileNameToStore =$this->imageStoring($request,$type);
                        }
                else {
                    $fileNameToStore = 'noimage.jpg';
                }
            $act->before_photo_url = $fileNameToStore;
        }
        $act->user_id = Auth::id();
        $act->car_id= $request->car_id;
        $act->before_kilos = $request->before_kilos;
        $act->previous_fuel_amount=$request->previous_fuel_amount;
        $act->destination= $request->destination;
        $act->is_done = 0;
        $act->save();
        return back()->with('message', Config::get('constants.sucessful_create')); ;
    }
     public function showModalDetails($id)
    {
        $act =  Activity::find($id);
        $photo="noimage.jpg";
        $fuel="--";
        if($act->after_photo_url!=null){
            $photo=$act->after_photo_url;
        }
        if($act->after_fuel_amount!=null){
            $fuel=$act->after_fuel_amount;
        }
        $retStr= '  <table  class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Photo initiale</th>
                        <th>Photo actuelle</th>
                        <th>Carburant initial</th>
                        <th>Carburant laissé</th>
                    </tr>
                </thead>
                <tbody>
            <tr>
                <td><img src="storage/activities/'.$act->before_photo_url.'") " style="height:50px;width:50px" ></td>
                <td><img src="storage/activities/'.$photo.'" style="height:50px;width:50px" ></td>
                <td>'.$act->previous_fuel_amount.'</td>
                <td>'.$fuel.'</td>
            </tr>
        </tbody>

    </table>';
        return $retStr;
    }
    

}
