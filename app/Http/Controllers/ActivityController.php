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
        if(Auth::user()->role_id==1){
        $activities = Activity::latest('id')->paginate(10);    
        }
        else{
            $activities = Activity::where('user_id',Auth::user()->id)->latest('id')->paginate(10);
        }
        $occupied_cars=Activity::where("is_done",0)->select("car_id")->get()->toArray();
        $cars=Car::whereNotIn("id",$occupied_cars)->where("is_working",1)->get();
        $state="";
        if($cars->isEmpty()){
            $state="disabled";
        }
        return view('admin.activitieslist')->with(['activities' => $activities,'cars'=>$cars,'state'=>$state]);
    }


    public function getSelectedActivity($type,$id)
    {   
        if($type=="selection"){
        $activities =Activity::where('car_id',$id)->latest('id')->paginate(15);
        }
         if($type=="longest-distance"){
        $activities =Activity::where('id',$id)->latest('id')->paginate(15);
        }
        else{
            $activities =Activity::where('user_id',$id)->latest('id')->paginate(15);
        }        
        $occupied_cars=Activity::where("is_done",0)->select("car_id")->get()->toArray();
        $cars=Car::whereNotIn("id",$occupied_cars)->where("is_working",1)->get();
        $state="";
        if($cars->isEmpty()){
            $state="disabled";
        }
        return view('admin.activitieslist')->with(['cars'=>$cars,'state'=>$state,'activities'=>$activities]);
    }


  public function create(ActivityStoreRequest $request,$type_request)
    {
        $act_check= Activity::where("car_id",$request->car_id)->where("is_done",1)->first();
        if(isset($act_checknull)){
            if(($act_check[0]->after_kilos!=$request->before_kilos)||($act_check[0]->after_fuel_amount!=$request->previous_fuel_amount)){
            return back()->with('message'," fausses informations ! veuillez contacter l'administrateur !");
        }
        }
        
        $act= Activity::firstOrNew(array('id' => $request->id));
        $fileNameToStore = "";
         $type="activity";
         if($type_request=="update"){

              $validated = $request->validate([
                'after_kilos' => 'required',
                'expenses' => 'required',
                'fuel' => 'required',
                'after_fuel_amount' => 'required',
                'after_photo_url' => 'image|mimes:jpg,jpeg,png,gif,svg|max:2048'
                ]);

            if ($request->hasFile('images')) {
               
                $fileNameToStore =$this->imageStoring($request,$type);
                $act->before_photo_url = $fileNameToStore;
                }
                
        }
        else{
             $validated = $request->validate([
                'before_kilos' => 'required',
                'previous_fuel_amount' => 'required',
                'destination' => 'required',
                'before_photo_url' => 'image|mimes:jpg,jpeg,png,gif,svg|max:2048'
                ]);
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
        $car_booked=Car::find($request->car_id);
        $car_booked->is_dispo=0;
        $car_booked->save();
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
                <td data-th="Photo initiale"><img src="storage/activities/'.$act->before_photo_url.'" style="height:50px;width:50px" ></td>
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
    if($act[0]->is_done==0) {
        return false;
    }
    $path_before=$act[0]->before_photo_url;
    $path_after=$act[0]->after_photo_url;
    $type="activities";
    $default="noimage.jpg";
        $this->imageDeleting($path_before,$type,$default);
        $this->imageDeleting($path_after,$type,$default);
      $act=Activity::where('id',$id)->delete();  
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
        $act =  Activity::where('id',$id)->get();
        $retStr = '<form  method="post" action="/end-activity" enctype="multipart/form-data" class="floating-labels">
    <fieldset style="border:0;">
    <input type="hidden" name="id" value="' . $act[0]->id . '" />
    <input type="hidden" name="_token" value="' . csrf_token() . '" />
                        <div>
                            <div class="form-group " style="float:left; width:50%;">
                                <label for="expenses">Dépenses</label>
                                <input type="text" class="form-control" name="expenses"   value="'.$act[0]->expenses.'" >
                            </div>
                            <div class="form-group" style="float:left; width:50%;">
                                <label for="after_kilos">Kilométrage aprés activité</label>
                                <input type="number" class="form-control" name="after_kilos"  value="'.$act[0]->after_kilos.'"   ><span class="highlight"></span> <span class="bar"></span>
                            </div>
                        </div > 
                        <div>
                            <div class="form-group " style="float:left; width:50%;">
                                <label for="fuel">carburant acheté</label>
                                <input type="number" class="form-control" name="fuel"  value="'.$act[0]->fuel.'"   ><span class="highlight"></span> <span class="bar"></span>
                            </div>
                            <div class="form-group" style="float:left; width:50%;">
                                <label for="after_fuel_amount">carburtant laissé</label>
                                <input type="number" class="form-control" name="after_fuel_amount"  value="'.$act[0]->after_fuel_amount.'"   ><span class="highlight"></span> <span class="bar"></span>
                            </div>
                        </div >        

                       <div class="form-group m-b-40">    
            <label >Image principale</label>  
            <table><tr><td></td><td></td>    </table>   
            <div class="images-preview-div-3"><img id="previous" src="storage/activities/noimage.jpg" style="height:100px;width:100px" ></div>
            
            </div>
                       <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" readonly>
                                    <div class="input-group-btn">
                            
                                        <span class="fileUpload btn btn-info">
                                            <span class="upl" id="upload">Importer des images </span>
                                            <input type="file" class="upload up" id="images_b" name="images"  onchange="readURL(this);" />
                                        </span><!-- btn-orange -->
                                    </div><!-- btn -->
                             </div><!-- group -->
                        </div><!-- form-group -->
                        <br><br><br>

                        <div class="images-preview-div"> </div>
                        <div class="form-group m-b-40">
                            <button type="submit" class="btn btn-success waves-effect waves-light m-r-10">Sauvgarder</button>
                            <button type="reset" data-dismiss="modal" class="btn btn-inverse waves-effect waves-light">Annuler</button>
                        </div>
    </fieldset> 
</form><script >
$(function() {
// Multiple images preview with JavaScript
var previewImages = function(input, imgPreviewPlaceholder) {
if (input.files) {
var filesAmount = input.files.length;
for (i = 0; i < filesAmount; i++) {
var reader = new FileReader();
reader.onload = function(event) {
    document.getElementById("previous").style.display = "none";
$($.parseHTML("<img>")).attr("src", event.target.result).appendTo(imgPreviewPlaceholder);
}
reader.readAsDataURL(input.files[i]);
}
}
};
$("#images").on("change", function() {
previewImages(this, "div.images-preview-div");
});
$("#images_b").on("change", function() {
previewImages(this, "div.images-preview-div-3");
});
});
</script>';
        return $retStr;
}

public function updateDone(ActivityStoreRequest $request)
    {
        $act= Activity::firstOrNew(array('id' => $request->id));
        $fileNameToStore = "";
        $type="activity";
         
                if ($request->hasFile('images')) {
                    
                    $fileNameToStore =$this->imageStoring($request,$type);
                        }
                else {
                    $fileNameToStore = 'noimage.jpg';
                }
        $act->after_photo_url = $fileNameToStore;
        $act->after_kilos = $request->after_kilos;
        $act->after_fuel_amount=$request->after_fuel_amount;
        $act->expenses= $request->expenses;
        $act->fuel= $request->fuel;
        $act->is_done = 1;
        $act->returning_date=now();
        $act->save();
        $car_booked=Car::find($act->car_id);
        $car_booked->is_dispo=1;
        $car_booked->save();
        return back()->with('message', Config::get('constants.sucessful_create')); 
    } 

    public function showModalToUpdate($id)
    {
       $act = Activity::where('id',$id)->get();
       $occupied_cars=Activity::where("is_done",0)->select("car_id")->get()->toArray();
       $cars=Car::whereNotIn("id",$occupied_cars)->where("is_working",1)->get();
       $str_car="<option value='".$act[0]->car->id."'>".$act[0]->car->model."</option>";
       foreach($cars as $car){
            if($act[0]->car->id!==$car->id){
                $str_car=$str_car."<option value='".$car->id."'>".$car->model."</option>"; 
            }                
       }
        $retStr = '<form  method="post" action="/edit-activity" enctype="multipart/form-data" class="floating-labels">
    <fieldset style="border:0;">
    <input type="hidden" name="id" value="' . $act[0]->id . '" />
    <input type="hidden" name="_token" value="' . csrf_token() . '" />

                       <div class="form-group m-b-40">
                            <label for="car_id">Voiture</label>
                            <select class="form-control p-0"  name="car_id" required="">'.$str_car.'                                     
                            </select>
                        </div>
                        <div class="form-group m-b-40">
                            <label for="before_kilos">Kilométrage initial</label>
                            <input type="number" class="form-control" name="before_kilos" value="'.$act[0]->before_kilos.'"><span class="highlight"></span> <span class="bar"></span>
                        </div>
                         <div class="form-group m-b-40">
                            <label for="destination">Destination</label>
                            <input type="text" class="form-control" name="destination" value="'.$act[0]->destination.'">
                        </div>
                        <div class="form-group m-b-40">
                            <label for="previous_fuel_amount">Carburant initiale</label> 
                             <input type="number" class="form-control" name="previous_fuel_amount" value="'.$act[0]->previous_fuel_amount.'"><span class="highlight"></span> <span class="bar"></span>
                        </div>


                       <div class="form-group m-b-40">    
            <label >Image principale</label>  
            <table><tr><td></td><td></td>    </table>   
            <div class="images-preview-div-3"><img id="previous" src="storage/activities/'.$act[0]->before_photo_url.'" style="height:100px;width:100px" ></div>
            
            </div>
                       <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" readonly>
                                    <div class="input-group-btn">
                            
                                        <span class="fileUpload btn btn-info">
                                            <span class="upl" id="upload">Importer des images </span>
                                            <input type="file" class="upload up" id="images_b" name="images"  onchange="readURL(this);" />
                                        </span><!-- btn-orange -->
                                    </div><!-- btn -->
                             </div><!-- group -->
                        </div><!-- form-group -->
                        <br><br><br>

                        <div class="images-preview-div"> </div>
                        <div class="form-group m-b-40">
                            <button type="submit" class="btn btn-success waves-effect waves-light m-r-10">Sauvgarder</button>
                            <button type="reset" data-dismiss="modal" class="btn btn-inverse waves-effect waves-light">Annuler</button>
                        </div>
    </fieldset> 
</form><script >
$(function() {
// Multiple images preview with JavaScript
var previewImages = function(input, imgPreviewPlaceholder) {
if (input.files) {
var filesAmount = input.files.length;
for (i = 0; i < filesAmount; i++) {
var reader = new FileReader();
reader.onload = function(event) {
    document.getElementById("previous").style.display = "none";
$($.parseHTML("<img>")).attr("src", event.target.result).appendTo(imgPreviewPlaceholder);
}
reader.readAsDataURL(input.files[i]);
}
}
};
$("#images").on("change", function() {
previewImages(this, "div.images-preview-div");
});
$("#images_b").on("change", function() {
previewImages(this, "div.images-preview-div-3");
});
});
</script>';
        return $retStr;
    }
    
      public function update(ActivityStoreRequest $request)
    {
        $type_request="update";
        $this->create($request,$type_request);
        return back()->with('message', Config::get('constants.sucessful_edit')); ;
    }   
}
