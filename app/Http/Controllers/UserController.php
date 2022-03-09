<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserStoreRequest;
use Illuminate\Support\Facades\Config;
use App\Http\Traits\ImageTrait;
use Illuminate\Support\Facades\File;
use App\Car;
class UserController extends Controller
{
    use ImageTrait;
   public function index()
    {     
        $users = User::where("deleted_at",NULL)->paginate(10);   
        $cars=Car::where("deleted_at",NULL)->get(); 
        return view('admin.accountslist')->with(['users'=>$users,'cars'=>$cars]);
    }

  public function create(UserStoreRequest $request,$type_request)
    {
        $user= User::firstOrNew(array('id' => $request->id));
        $fileNameToStore = "";
        if($type_request=="update"){
            if ($request->hasFile('images')) {
                $type="user";
            $fileNameToStore =$this->imageStoring($request,$type);
            $user->photo_url = $fileNameToStore;
                }
                if($request->car_id==0){
            $user->car_id=null;
        }
        }
        else{
                if ($request->hasFile('images')) {
                    $type="user";
                    $fileNameToStore =$this->imageStoring($request,$type);
                        }
                else {
                    $fileNameToStore = 'user.png';
                }
            $user->photo_url = $fileNameToStore;
        }
        
        if($request->car_id!=0){
            $user->car_id=$request->car_id;
        }
        $user->email = $request->email;
        $user->role_id =2;
        $user->name= $request->name;
        $user->password = Hash::make($request->password);
        $user->save();
        return back()->with('message', Config::get('constants.sucessful_create')); ;
    }
    public function update(UserStoreRequest $request)
    {
        $type_request="update";
        $this->create($request,$type_request);
         return back()->with('message', Config::get('constants.sucessful_edit')); ;
       
    } 
    public function showModalToUpdate($id)
    {
       $user= User::where('id',$id)->get();            
    //    $cars=Car::where("is_working",1)->get();
        $cars=Car::where("deleted_at",NULL)->get();
        if($user[0]->car!=null){
            $str_car="<option value='".$user[0]->car->id."'>".$user[0]->car->model."</option>";
            $str_car=$str_car."<option value='0'>tout</option>";
            foreach($cars as $car){
            if($user[0]->car->id!=$car->id){
                $str_car=$str_car."<option value='".$car->id."'>".$car->model."</option>"; 
            }
            }
        }
        else{
            $str_car="<option value='0'>tout</option>";
            foreach($cars as $car){
              $str_car=$str_car."<option value='".$car->id."'>".$car->model."</option>";   
            }
        }  
       $retStr = '<form  method="post" action="/edit-user" enctype="multipart/form-data" class="floating-labels">
    <fieldset style="border:0;">
    <input type="hidden" name="id" value="' . $user[0]->id . '" />
    <input type="hidden" name="_token" value="' . csrf_token() . '" />
                        <div class="form-group m-b-40">
                            <label for="name">Nom</label>
                            <input type="text" class="form-control" name="name" value="'.$user[0]->name.'"  >
                        </div>
                         <div class="form-group m-b-40">
                            <label for="car_id">Voiture</label>
                            <select class="form-control p-0"  name="car_id" required="">'.$str_car.'                                     
                            </select>
                        </div>
                         <div class="form-group m-b-40">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" value="'.$user[0]->email.'"  >
                        </div>
                            <div class="form-group ">
                                <label for="place">Mot de passe</label>
                                <input type="password" class="form-control" name="password" >
                            </div>   

                       <div class="form-group m-b-40">    
            <label >Image principale</label>  
            <table><tr><td></td><td></td>    </table>   
            <div class="images-preview-div-3"><img id="previous" src="storage/users/'.$user[0]->photo_url.'" style="height:100px;width:100px" ></div>
            
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
    public function showModalToDelete($id)
        {
            $user =  User::where('id',$id)->get();
            $retStr= ' voulez vous vraiment supprimer l\'utilisateur '. $user[0]->name.' ?
            <form  method="post" action="/destroy-user/'.$user[0]->id.'" class="floating-labels">
        <fieldset style="border:0;">
        <input type="hidden" name="id" value="' . $user[0]->id . '" />
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
      $user=User::where('id',$id)->get(); 
      $path= $user[0]->photo_url;
      $type="users";
      $default="user.png";
      $this->imageDeleting($path,$type,$default);
      $user= User::find($id); 
      $user->activities()->delete();
      $user->delete();  
      return redirect('/users')->with('message', Config::get('constants.sucessful_delete')); 
         
     
    }
}
