<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
 use App\Http\Requests\CarStoreRequest;
use App\Carburant;
use App\Car;
use App\Activity;
use App\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use App\Http\Traits\ImageTrait;
class CarController extends Controller
{
    use ImageTrait;
  public function index()
    {     
        $carburants = Carburant::get();            
        $cars = Car::where("deleted_at",NULL)->paginate(10);        
        return view('admin.carslist')->with(['carburants' => $carburants,'cars'=>$cars]);
    }
 public function showStatisticsModal($id)
    {
        $expenses=0;
        $carburants=0;
        $str='';
        $expenses=Activity::where("car_id",$id)->sum("expenses");
        $carburants=Activity::where("car_id",$id)->sum("fuel");
        $activity_count=Activity::where("car_id",$id)->count();
        $farest=Activity::where("car_id",$id)->where("is_done",1)->select("after_kilos","before_kilos","user_id","id")->get();
        if(!$farest->isEmpty()){
            foreach($farest as $far){
             $vals[]=$far->before_kilos-$far->after_kilos;
             $id_user[]=$far->user_id;
             $id_act[]=$far->id;
        }
        $max=max($vals);
        $id= array_keys($vals,$max);
        $id_driver=$id_user[$id[0]];
        $id_activity=$id_act[$id[0]];
        $driver=User::where("id",$id_driver)->select("name")->get();
        $act=Activity::where("id",$id_activity)->select("destination")->get();
        $str='<div class="small-box bg-warning">
              <div class="inner" style="margin-bottom:20px;">
                <h4>Distance la plus longue</h4> 
                <p></p>               
              </div>
              <div class="icon">
               <i class="fas fa-chart-line"></i>
              </div>
              <a href="#" style="font-size:20px;" class="small-box-footer"><i class="fas fa-road nav-icon"></i>'.$max.' Km  <i class="fas fa-map-pin nav-icon"></i> '.ucwords($act[0]->destination).' <i class="fas fa-user nav-icon"></i> '.$driver[0]->name.'</a>
              <a href="/car/longest-distance/'.$id_activity.' style="font-size:15px;font-weight:bolder;" class="small-box-footer">Voir details </a>';
        }
        
        // $kilos=Activity::where("car_id",$id)->sum("carburant");
        $retStr= ' 
        <div class="row d-flex justify-content-center">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner" style="margin-bottom:20px;">
                <h4>Carburant</h4> 
                <p></p>               
              </div>
              <div class="icon">
               <i class="fas fa-gas-pump"></i>
              </div>
              <a href="#" style="font-size:20px;" class="small-box-footer">'.$carburants.'</a>
            </div>
          </div>
           <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner" style="margin-bottom:20px;">
                <h4>D??penses</h4> 
                <p></p>               
              </div>
              <div class="icon">
               <i class="fas fa-dollar-sign"></i>
              </div>
              <a href="#" style="font-size:20px;" class="small-box-footer">'.$expenses.'</a>
            </div>
          </div>
           <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner" style="margin-bottom:20px;">
                <h4>Activit??s </h4> 
                <p></p>               
              </div>
              <div class="icon">
               <i class="fas fa-road"></i>
              </div>
              <a href="#" style="font-size:20px;" class="small-box-footer">'.$activity_count.'</a>
            </div>
            </div>
           <div class="col-lg-6 col-12">'.$str.'     
            </div>
          </div>
          </div>
          ';
        return $retStr;
    }
    
  public function create(CarStoreRequest $request,$type_request)
    {
        $car= Car::firstOrNew(array('id' => $request->id));
        $fileNameToStore = "";
         if($type_request=="update"){
            if ($request->hasFile('images')) {
                $type="car";
                $fileNameToStore =$this->imageStoring($request,$type);
                $car->photo_url = $fileNameToStore;
                }
                
        }
        else{
                if ($request->hasFile('images')) {
                    $type="car";
                    $fileNameToStore =$this->imageStoring($request,$type);
                        }
                else {
                    $fileNameToStore = 'noimage.jpg';
                }
            $car->photo_url = $fileNameToStore;
        }
        $car->model = $request->model;
        $car->serial_number= $request->serial_number;
        $car->place = $request->place;
        $car->carburant_id= $request->carburant_id;
        $car->kilo= $request->kilo;
        $car->is_dispo=1;
        $car->is_working = 1;
        $car->save();
        return back()->with('message', Config::get('constants.sucessful_create')); ;
    }
    public function update(CarStoreRequest $request)
    {
        $type_request="update";
        $this->create($request,$type_request);
        return back()->with('message', Config::get('constants.sucessful_edit')); ;
    }   
    
    public function showModalToDelete($id)
    {
        $car =  Car::where('id',$id)->get();
        $retStr= ' voulez vous vraiment supprimer la voiture '. $car[0]->model.' ?
        <form  method="post" action="/destroy-car/'.$car[0]->id.'" class="floating-labels">
    <fieldset style="border:0;">
    <input type="hidden" name="id" value="' . $car[0]->id . '" />
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
      $car= Car::where('id',$id)->get(); 
      $path= $car[0]->photo_url;
      $type="images";
      $default="noimage.jpg";
      $this->imageDeleting($path,$type,$default);  
      $car= Car::find($id); 
      $car->activity()->delete();
      $car->reparation()->delete();
      $car->delete();  
      return redirect('/cars')->with('message', Config::get('constants.sucessful_delete')); 
         
     
    }

    public function showModalToUpdate($id)
    {
       $car = Car::where('id',$id)->get();
       $carburants=Carburant::all();
       $default_carb=Car::find($id)->carburant->name;
       $str_carb="<option value='".$car[0]->carburant_id."'>".$default_carb."</option>";
       $op_occ="<option value='1'>disponible</option>";
       $op_dis="<option value='0'>occup??e</option>";
       if($car[0]->is_dispo==1){
        $str_dispo=$op_occ.$op_dis;
       }
       else{
           $str_dispo=$op_dis.$op_occ;
       }
       $op_mer="<option value='1'>En marche</option>";
       $op_pan="<option value='0'>En panne</option>";
       if($car[0]->is_working==1){
        $str_state=$op_mer.$op_pan;
       }
       else{
           $str_state=$op_pan.$op_mer;
       }
      
       foreach($carburants as $carb){
            if($carb->id!==$car[0]->carburant_id){
                $str_carb=$str_carb."<option value='".$carb->id."'>".$carb->name."</option>"; 
            }                
       }
        $retStr = '<form  method="post" action="/edit-car" enctype="multipart/form-data" class="floating-labels">
    <fieldset style="border:0;">
    <input type="hidden" name="id" value="' . $car[0]->id . '" />
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
                                <label for="kilo">Kilom??trage</label>
                                <input type="number" class="form-control" name="kilo"  value="'.$car[0]->kilo.'"   ><span class="highlight"></span> <span class="bar"></span>
                            </div>
                        </div >
                        <div>
                            <div class="form-group " style="float:left; width:50%;">
                                <label for="is_dispo">Disponibilit??</label>
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
