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
        $state="";
        if($cars->isEmpty()){
            $state="disabled";
        }
        return view('admin.activitieslist')->with(['activities' => $activities,'cars'=>$cars,'state'=>$state]);
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
        $retStr= '  <table  class="table table-striped table-bordered" id="customDataTable" style="width:100%">
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
                <td data-th="Photo initiale"><img src="storage/activities/'.$act->before_photo_url.'") " style="height:50px;width:50px" ></td>
                <td data-th="Photo actuelle"><img src="storage/activities/'.$photo.'" style="height:50px;width:50px" ></td>
                <td data-th="Carburant initial">'.$act->previous_fuel_amount.'</td>
                <td data-th="Carburant laissé">'.$fuel.'</td>
            </tr>
        </tbody>

    </table>';
        return $retStr;
    }
    
    public function showModalToDelete($id)
    {
        $act =  Activity::where('id',$id)->get();
        $retStr= ' voulez vous vraiment supprimer l\'activité d\'#ID '. $act[0]->id.' ?
        <form  method="post" action="/destroy-activity/'.$act[0]->id.'" class="floating-labels">
    <fieldset style="border:0;">
    <input type="hidden" name="id" value="' . $act[0]->id . '" />
    <input type="hidden" name="_token" value="' . csrf_token() . '" /> 
        <div class="form-group m-b-40">
            <button type="submit" class="btn btn-success waves-effect waves-light m-r-10">Supprimer</button>
            <button type="reset" class="btn btn-inverse waves-effect waves-light">Annuler</button>
        </div>
    </fieldset>
</form>';
        return $retStr;
    }
    public function delete($id)
    {
    $act= Activity::where('id',$id)->get(); 
    $path_before=$act[0]->before_photo_url;
    $path_after=$act[0]->after_photo_url;
    $type="activities";
    $default="noimage.jpg";
        $this->imageDeleting($path_before,$type,$default);
        $this->imageDeleting($path_after,$type,$default);
      $act=Activity::where('id',$id)->delete();  
      return redirect('/activities')->with('message', Config::get('constants.sucessful_delete')); 
        
     
    }

    public function deleteMass(Request $request)
    {
        $acts=$request->activities;
        for($i=0;$i<count($acts);$i++){
            $this->delete($acts[$i]);
        }
        
         return redirect('/activities')->with('message', Config::get('constants.sucessful_delete')); 
    }
   

}
