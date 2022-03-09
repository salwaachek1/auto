<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Activity;
use App\Car;
 use App\Http\Requests\ReparationStoreRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use App\Http\Traits\ImageTrait;
use Illuminate\Support\Facades\Auth;
use App\Reparation;
class ReparationController extends Controller
{
    use ImageTrait;
        /**
     * this function returns all activities for admin and for driver it returns only their own activities  
     *
     * @param 
     */
  public function index()
    {   
      
        $reparations = Reparation::latest('id')->where("deleted_at",NULL)->paginate(10);
        $occupied_cars=Activity::where("is_done",0)->where("deleted_at",NULL)->select("car_id")->get()->toArray();
        $broke_down_cars=Reparation::where("is_done",0)->where("deleted_at",NULL)->select("car_id")->get()->toArray();
        $cars=Car::whereNotIn("id",$occupied_cars)->whereNotIn("id",$broke_down_cars)->where("is_working",1)->where("deleted_at",NULL)->get();
        $state="";
        if($cars->isEmpty()){
            $state="disabled"; 
        }
        return view('admin.reparationslist')->with(['reparations' => $reparations,'cars'=>$cars,'state'=>$state]);
    }

    /**
         * this function return activities that belongs to a car or a user
         *
         * @param string $type the type of asked activity: "selection" refer to a specific car, we want to return all activities related to that car, "longest-distance" refer to an activity, we want to return the rest of info
         */
    public function getSelectedActivity($type,$id)
    {   
        if($type=="selection"){ // if true it will returns activities that belongs to a specific car
        $activities =Activity::where('car_id',$id)->where("deleted_at",NULL)->latest('id')->paginate(15);
        }
        else if($type=="longest-distance"){ // if true it will returns an activity through activity's ID
        $activities =Activity::where('id',$id)->where("deleted_at",NULL)->latest('id')->paginate(15);
        }
        else{ // return activities that belongs to a specific user
            $activities =Activity::where('user_id',$id)->where("deleted_at",NULL)->latest('id')->paginate(15);
        }        
        $occupied_cars=Activity::where("is_done",0)->select("car_id")->get()->toArray();
        $cars=Car::whereNotIn("id",$occupied_cars)->where("deleted_at",NULL)->where("is_working",1)->get();
        $state="";
        if($cars->isEmpty()){
            $state="disabled";
        }
        return view('admin.activitieslist')->with(['cars'=>$cars,'state'=>$state,'activities'=>$activities]);
    }


  public function create(ReparationStoreRequest $request)
    {
        
        $rep= Reparation::firstOrNew(array('id' => $request->id));    
                 $validated = $request->validate([
                'garage' => 'required',
                'phone' => 'required',
                ]);
        $rep->car_id= $request->car_id;
        $rep->garage = $request->garage;
        $rep->phone=$request->phone;
        $rep->is_done = 0;
        $rep->save();

        $car_broke=Car::find($request->car_id);
        $car_broke->is_working=0;
        $car_broke->save();

        return back()->with('message', Config::get('constants.sucessful_create')); 
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
                <td data-th="Photo initiale"><img src="/storage/activities/'.$act->before_photo_url.'" style="height:50px;width:50px" ></td>
                <td data-th="Photo actuelle"><img src="/storage/activities/'.$photo.'" style="height:50px;width:50px" ></td>
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
    $rep= Reparation::where('id',$id)->get();
    if($rep[0]->is_done==0) {
        return false;
    }
     $rep->delete();  
     return true;       
     
    }

    public function deleteMass(Request $request)
    {
        $acts=$request->activities;
        for($i=0;$i<count($acts);$i++){
            $state=$this->delete($acts[$i]);
        }
        if($state) {
            return redirect('/activities')->with('message', Config::get('constants.sucessful_delete')); 
        }
        else{            
            $msg=" vous avez selectionné des activités qui ne sont terminées !";
            return redirect('/activities')->with('msgs', $msg); 
        }
        
         
    }
   
    public function showModalToEnd($id)
    {
        $rep =  Reparation::where('id',$id)->get();
        $retStr = '<form  method="post" action="/end-reparation" enctype="multipart/form-data" class="floating-labels">
    <fieldset style="border:0;">
    <input type="hidden" name="id" value="' . $rep[0]->id . '" />
    <input type="hidden" name="_token" value="' . csrf_token() . '" />
                        <div class="form-group m-b-40">
                                <label for="fees">Dépense</label>
                                <input type="number" class="form-control" name="fees" value="'.$rep[0]->fees.'"><span class="highlight"></span> <span class="bar"></span>
                            </div>
                        <div class="form-group m-b-40">
                                <label for="replaced_parts">Pieces à remplacer </label>
                                <input type="text" class="form-control" name="replaced_parts" value="'.$rep[0]->replaced_parts.'">
                            </div>
                           <div class="form-group m-b-40">
                                 <label for="diagnosis">Diagnostique</label>
                                <input type="text" class="form-control" name="diagnosis" value="'.$rep[0]->diagnosis.'">
                            </div>  
                        <br><br><br>

                        <div class="images-preview-div"> </div>
                        <div class="form-group m-b-40">
                            <button type="submit" class="btn btn-success waves-effect waves-light m-r-10">Sauvgarder</button>
                            <button type="reset" data-dismiss="modal" class="btn btn-inverse waves-effect waves-light">Annuler</button>
                        </div>
    </fieldset> 
</form>';
        return $retStr;
}

public function updateDone(ReparationStoreRequest $request)
    {
        $rep= Reparation::firstOrNew(array('id' => $request->id));
        $validated = $request->validate([
                'diagnosis' => 'required',
                'replaced_parts' => 'required',
                'fees' => 'required',
                ]);
         
        $rep->diagnosis = $request->diagnosis;
        $rep->replaced_parts = $request->replaced_parts;
        $rep->fees=$request->fees;
        $rep->date_out= now();
        $rep->is_done = 1;
        $rep->save();
        $car_booked=Car::find($rep->car_id);
        $car_booked->is_working=1;
        $car_booked->save();
        return back()->with('message', Config::get('constants.sucessful_create')); 
    } 

    public function showModalToUpdate($id)
    {
       $rep = Reparation::where("id",$id)->get();
       $occupied_cars=Activity::where("is_done",0)->where("deleted_at",NULL)->select("car_id")->get()->toArray();
       $broke_down_cars=Reparation::where("is_done",0)->select("car_id")->get()->toArray();
       $cars=Car::whereNotIn("id",$occupied_cars)->where("deleted_at",NULL)->whereNotIn("id",$broke_down_cars)->where("is_working",1)->get();
       $str_car="<option value='".$rep[0]->car->id."'>".$rep[0]->car->model."</option>";
       foreach($cars as $car){
            if($rep[0]->car->id!==$car->id){
                $str_car=$str_car."<option value='".$car->id."'>".$car->model."</option>"; 
            }                
       }
        $retStr = '<form  method="post" action="/edit-reparation" enctype="multipart/form-data" class="floating-labels">
    <fieldset style="border:0;">
    <input type="hidden" name="id" value="' . $rep[0]->id . '" />
    <input type="hidden" name="_token" value="' . csrf_token() . '" />

                       <div class="form-group m-b-40">
                            <label for="car_id">Voiture</label>
                            <select class="form-control p-0"  name="car_id" required="">'.$str_car.'                                     
                            </select>
                        </div>
                       <div class="form-group m-b-40">
                            <label for="garage">Nom du garage</label>
                            <input type="text" class="form-control"name="garage" value="'.$rep[0]->garage.'">
                        </div>
                         <div class="form-group m-b-40">
                            <label for="phone">Télèphone </label>
                            <input type="text" class="form-control" name="phone" value="'.$rep[0]->phone.'">
                        </div>
                        <br><br><br>

                        <div class="images-preview-div"> </div>
                        <div class="form-group m-b-40">
                            <button type="submit" class="btn btn-success waves-effect waves-light m-r-10">Sauvgarder</button>
                            <button type="reset" data-dismiss="modal" class="btn btn-inverse waves-effect waves-light">Annuler</button>
                        </div>
                        </fieldset> 
                            </form>';
        return $retStr;
    }
    
      public function update(ReparationStoreRequest $request)
    {
        // $this->create($request);
        // return back()->with('message', Config::get('constants.sucessful_edit')); ;
    }   
}
