<?php
namespace App\Http\Traits;
use Illuminate\Support\Facades\File;
trait ImageTrait {
    public function imageStoring($request,$type){
            $filenameWithExt = $request->file('images')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('images')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            if($type=="user"){
                $path = $request->file('images')->storeAs('/users/', $fileNameToStore, 'public');
            }
            else if($type=="car"){
                $path = $request->file('images')->storeAs('/images/', $fileNameToStore, 'public');
            }
            else if($type=="activity"){
                $path = $request->file('images')->storeAs('/activities/', $fileNameToStore, 'public');
            }
            return $fileNameToStore;
  }  
  public function imageDeleting($path,$type,$default){

        $image_path = "storage/".$type.'/'.$path;
        if(File::exists($image_path)&&($image_path!=$default)) {
            File::delete($image_path);
        }
        return true;
       
  }
}
?>