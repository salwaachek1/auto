@extends('layouts.common')

@section('content')
<script type="text/javascript">
$(document).ready(function () {
 
window.setTimeout(function() {
    $(".alert").fadeTo(1000, 0).slideUp(1000, function(){
        $(this).remove(); 
    });
}, 2000);
 
});
</script>
<button type="button" class="popup-with-form btn btn-block btn-primary btn-rounded" data-toggle="modal" data-target="#newCar" >Ajouter Voiture</button>
<div id="newAct" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="newAct" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="newAct">Ajouter voiture</h4> </div>
                <br>
            <div class="modal-body">
                <form  method="post" enctype="multipart/form-data" class="floating-labels" action="/ajouter_voiture" >
                    @csrf
                    <fieldset style="border:0;">

                        <div class="form-group m-b-40">
                            <label for="titre">Nom de pack</label>
                            <input type="text" class="form-control" name="name"  required="" >
                        </div>
                        <div >
                            <div class="form-group " style="float:left; width:50%;">
                                <label for="titre">Tarif</label>
                                <input type="text" class="form-control" name="price"  required="" >
                            </div>
                            <div class="form-group " style="float:left;width:50%; ">
                                <label for="titre">Pays</label>
                                <input type="text" class="form-control" name="country"  required="" >
                            </div>
                        </div >
                       <div class="form-group m-b-40" style=" clear:both;">
                            <label for="Description">Description</label>
                            <textarea type="text" class="form-control" name="description"  required="" ></textarea>
                        </div>
                        <div >
                            <div class="form-group  m-b-40" style="float:left; margin-right:20px;">
                                <label><i class="icon-calendar-7"></i> Date debut</label>
                                <input name="start" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" style="background-color:#eee;border:0px solid black;" type="date" required>
                            </div>
                            <div class="form-group  m-b-40" style="float:left;">
                                <label><i class="icon-calendar-7"></i> Date fin</label>
                                <input name="end" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" style="background-color:#eee;border:0px solid black;" type="date" required>
                            </div>
                        </div>
                          
                        
                        <div class="form-group m-b-40">
                        <label for="hotel">Type hébergement</label>
                        <select class="form-control p-0"  name="hotel" required="">                           
                             <option value="modeste">modeste</option>
                             <option value="moyen">moyen</option>
                             <option value="confort">confort</option>
                       </select>

                    </div>
                        <div class="form-group m-b-40">
                        <label for="Type">Niveau</label>
                        <select class="form-control p-0"  name="level" required="">                           
                             <option value="facile">Facile</option>
                             <option value="amateur">Amateur</option>
                             <option value="pro">Pro</option>
                       </select>

                    </div>
                   <!-- <input type="file" name="image" class="file" accept="image/*">
                        <div class="form-group m-b-40">
                            <input type="text" class="form-control" disabled placeholder="nom de fichier télécharger ..." id="file">
                            <div class="input-group-append">
                              <button type="button" class="browse btn btn-primary">Importer Photo Principale...</button>
                            </div>
                             <div class="ml-2 col-sm-6">
                                <img src="/images/noimage.jpg" id="preview" class="img-thumbnail">
                              </div>
                        </div> -->
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" readonly>
                                    <div class="input-group-btn">
                            
                                        <span class="fileUpload btn btn-info">
                                            <span class="upl" id="upload">Importer des images </span>
                                            <input type="file" class="upload up" id="images" name="images"  onchange="readURL(this);" />
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
                </form>
                <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script >
$(function() {
// Multiple images preview with JavaScript
var previewImages = function(input, imgPreviewPlaceholder) {
if (input.files) {
var filesAmount = input.files.length;
for (i = 0; i < filesAmount; i++) {
var reader = new FileReader();
reader.onload = function(event) {
$($.parseHTML('<img>')).attr('src', event.target.result).appendTo(imgPreviewPlaceholder);
}
reader.readAsDataURL(input.files[i]);
}
}
};
$('#images').on('change', function() {
previewImages(this, 'div.images-preview-div');
});
$('#images_a').on('change', function() {
previewImages(this, 'div.images-preview-div-2');
});
});
</script>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="EditModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Modifier pack</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="Edit_Modal-body">

            </div>
           
        </div>
    </div>
</div>


<div class="modal fade" id="ImageModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Galerie</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="Image_Modal-body">

            </div>
           
        </div>
    </div>
</div>
<div class="modal fade" id="addModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ajouter un programme </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="add_Modal-body">

            </div>
                  </div>
    </div>
</div>
<div class="modal fade" id="editImageModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Gallérie</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="edit_image_Modal-body">

            </div>
            
        </div>
    </div>
</div>


   <div class="modal fade" id="propDetails" role="dialog">
        <div class="modal-dialog modal-lg modal-edit">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Details activité </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="propDetModalBody">
                </div>
                
            </div>
        </div>
   </div>

 
   
<br><br>
@if (\Session::has('message'))
<div class="alert alert-success" role="alert">
   {!! \Session::get('message') !!}
</div>

@endif
            <table id="propertys" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Titre</th>
                        <th>Prix</th>
                        <th>Pays</th>
                        <th>Description</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Hébergement</th>
                        <th>niveau</th>
                        <th class="text-nowrap">Action</th>

                    </tr>
                </thead>
                <tbody>

            @foreach ($packs as $pack)
            <tr>
                <td> <img src="/images/{{$pack->photo}}" style="height:50px;width:50px" > </td>
                <td><a class="popup-with-form" style="cursor: pointer;" onclick="getPropDetails('{{$pack->id}}')">{{$pack->name}}</a></td>
                <td>{{$pack->price}}</td>
                <td>{{$pack->country}}</td>
                <td>{{\Illuminate\Support\Str::limit($pack->description, 70, ' ...')}}</td>
                <td>{{$pack->start}}</td>  
                <td>{{$pack->end}}</td>             
                <td>{{$pack->hotel_type}}</td>
                <td>{{$pack->level}}</td>

                <td class="text-nowrap">
                    <a href="javascript:void(0)" data-id='{{$pack->id}}'  data-url='ShowPack' class='EditModalBtn'> <i class="fas fa-pencil-alt"></i> </a>
                    |
                    <a href="javascript:void(0)" data-id='{{$pack->id}}'  data-url='DeletePack' class='DeleteModalBtn'> <i class="fas fa-trash-alt text-danger"></i> </a>
                    |
                    <a href="javascript:void(0)" data-id='{{$pack->id}}'  data-url='addProg' class='addModalBtn'> <i class="fas fa-plus"></i> </a>
                     |
                    <a href="/admin/programs/{{$pack->id}}" >program </a>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>
    <!-- Delete hotel modal confirmation -->
   <div class="modal fade" id="deleteModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">supprimer activité</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
            <div class="modal-body" id="deleteModal-body">   
                      
            </div>
           
    </div>
    </div>
    <!-- end delete hotel modal -->
<script>
   
   $(document).on('change','.up', function(){
        var names = [];
        var length = $(this).get(0).files.length;
          for (var i = 0; i < $(this).get(0).files.length; ++i) {
              names.push($(this).get(0).files[i].name);
          }
          // $("input[name=file]").val(names);
        if(length>2){
          var fileName = names.join(', ');
          $(this).closest('.form-group').find('.form-control').attr("value",length+" files selected");
        }
        else{
          $(this).closest('.form-group').find('.form-control').attr("value",names);
        }
     });
</script>
@endsection