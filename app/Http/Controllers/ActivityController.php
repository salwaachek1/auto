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
        $activities = Activity::get();    
        }
        else{
            $activities = Activity::where('user_id',Auth::user()->id)->get();
        }
        $occupied_cars=Activity::where("is_done",0)->select("car_id")->get()->toArray();
        $cars=Car::whereNotIn("id",$occupied_cars)->get();
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
   
    public function showModalToEnd($id)
    {
        $act =  Activity::where('id',$id)->get();
       $retStr = '<form  method="post" action="/edit-activity" enctype="multipart/form-data" class="floating-labels">
    <fieldset style="border:0;">
    <input type="hidden" name="id" value="' . $act[0]->id . '" />
    <input type="hidden" name="_token" value="' . csrf_token() . '" />
                        <div class="form-group m-b-40">
                            <label for="model">Nom de modele</label>
                            <input type="text" class="form-control" name="model"  value="'.$car[0]->model.'" >
                        </div>
                        <div class="form-group m-b-40">
                            <label for="serial_number">Matricule</label>
                            <input type="text" class="form-control" name="serial_number" value="'.$car[0]->serial_number.'" >
                        </div>
                        <div>
                            <div class="form-group " style="float:left; width:50%;">
                                <label for="place">Lieu</label>
                                <input type="text" class="form-control" name="place"   value="'.$car[0]->place.'" >
                            </div>
                            <div class="form-group" style="float:left; width:50%;">
                                <label for="kilo">Kilométrage</label>
                                <input type="number" class="form-control" name="kilo"  value="'.$car[0]->kilo.'"   ><span class="highlight"></span> <span class="bar"></span>
                            </div>
                        </div >
                        <div>
                            <div class="form-group " style="float:left; width:50%;">
                                <label for="is_dispo">Disponibilité</label>
                                <select class="form-control p-0"  name="is_dispo">'.$str_dispo.'</select>
                            </div>
                            <div class="form-group" style="float:left; width:50%;">
                                <label for="is_working">Etat</label>
                                <select class="form-control p-0"  name="is_working" >'.$str_state.'</select>
                            </div>
                        </div >                           
                        
                         <div class="form-group m-b-40">
                            <label for="carburant_id">Type Carburant</label>
                            <select class="form-control p-0"  name="carburant_id">'.$str_carb.'</select>
                        </div>

                       <div class="form-group m-b-40">    
            <label >Image principale</label>  
            <table><tr><td></td><td></td>    </table>   
            <div class="images-preview-div-3"><img id="previous" src="storage/images/'.$car[0]->photo_url.'" style="height:100px;width:100px" ></div>
            
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

}
