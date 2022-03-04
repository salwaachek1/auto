@extends('layouts.common')

@section('content')
<style>
    .file {
  visibility: hidden;
  position: absolute;
}
.dropzone {
    background: white;
    border-radius: 5px;
    border: 2px dashed rgb(0, 135, 247);
    border-image: none;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
    }
    .modal-edit {
  width: 75%;
  margin: auto;
}
</style>
<button type="button" class="popup-with-form btn btn-block btn-primary btn-rounded" data-toggle="modal" data-target="#newCar" >Ajouter Voiture</button>
<div id="newCar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="newAct" aria-hidden="true">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
           <div class="modal-header">
                    <h4 class="modal-title">Ajouter voiture</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
            <div class="modal-body">
                <form  method="post" enctype="multipart/form-data" class="floating-labels" action="/add-car/create" >
                    @csrf
                    <fieldset style="border:0;">

                        <div class="form-group m-b-40">
                            <label for="model">Nom de modele</label>
                            <input type="text" class="form-control" name="model"   >
                        </div>
                         <div class="form-group m-b-40">
                            <label for="serial_number">Matricule</label>
                            <input type="text" class="form-control" name="serial_number"  >
                        </div>
                        <div >
                            <div class="form-group " style="float:left; width:50%;">
                                <label for="place">Lieu</label>
                                <input type="text" class="form-control" name="place" >
                            </div>
                            <div class="form-group" style="float:left; width:50%;">
                                <label for="kilo">Kilométrage</label>
                                <input type="number" class="form-control" name="kilo"  ><span class="highlight"></span> <span class="bar"></span>
                            </div>
                        </div >                          
                        
                         <div class="form-group m-b-40">
                            <label for="carburant_id">Type Carburant</label>
                            <select class="form-control p-0"  name="carburant_id" required="">      
                                @foreach($carburants as $carb)                     
                                <option value="{{$carb->id}}">{{$carb->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group m-b-40">
                            <div class="input-group">
                                <input type="text" class="form-control" readonly>
                                    <div class="input-group-btn">
                            
                                        <span class="fileUpload btn btn-info">
                                            <span class="upl" id="upload">Importer des images </span>
                                            <input type="file" class="upload up" id="images" name="images" onchange="readURL(this);" />
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

</script>

            </div>
        </div>
    </div>
</div>

   
<br><br>
@if (\Session::has('message'))
<div class="alert alert-success" role="alert" style="width:100%;margin:20px;text-align:center;">
   {!! \Session::get('message') !!}
</div>
@endif
@if($errors->any())
    <!-- {!! implode('', $errors->all('<div class="alert alert-danger" role="alert" style="width:100%;margin:20px;text-align:center;">:message</div>')) !!} -->
   <div class="alert alert-danger" role="alert" style="width:100%;margin:20px;text-align:center;"> {{ $errors->first() }}</div>
@endif
           
            <table  class="table table-striped table-bordered" style="width:100%" id="customDataTable" >
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Modèle</th>
                        <th>Matricule</th>
                        <th>Lieu</th>
                        <th>Carburant</th>
                        <th>Kilo</th>
                        <th>Disponibilité</th>
                        <th>Etat</th>
                        <th class="text-nowrap">Action</th>

                    </tr>
                </thead>
                <tbody>
             @if(!$cars->isEmpty())
            @foreach ($cars as $car)
            <tr>
                <td data-th="Image"> <img src="{{ asset('storage/images/'.$car->photo_url) }}" style="height:50px;width:50px" > </td>
                <td data-th="Modèle">{{$car->model}}</td>
                <td data-th="Matricule">{{$car->serial_number}}</td>
                <td data-th="Lieu">{{$car->place}}</td>
                <td data-th="Carburant">{{$car->carburant->name}}</td>
                <td data-th="Kilo">{{$car->kilo}}</td>  
                @if($car->is_dispo==1)
                <td data-th="Disponibilité">Disponible</td> 
                @else
                <td data-th="Disponibilité">Occupée</td> 
                @endif
                @if($car->is_working==1)
                <td data-th="Etat">Bonne état</td> 
                @else
                <td data-th="Etat">En panne</td> 
                @endif
                <td data-th="Action" class="text-nowrap">
                    <a href="javascript:void(0)" data-id='{{$car->id}}'  data-url='edit' data-entity='car' class='EditModalBtn'> <i class="fas fa-pencil-alt"></i> </a>
                    |
                    <a href="javascript:void(0)" data-id='{{$car->id}}'  data-url='delete' data-entity='car' class='DeleteModalBtn'> <i class="fas fa-trash-alt text-danger"></i> </a>
                    |
                    <a href="/car/selection/{{$car->id}}" > <i class="fas fa-eye text-primary"></i> </a>
                </td>
            </tr>
            @endforeach
            @else
            <tr><td colspan="9" style="text-align:center;">Liste est vide !</td></tr>  
            @endif

        </tbody>

    </table>
     {!! $cars->links() !!}
    
   
    
<!-- delete/edit common modal -->
   <div class="modal fade" id="MainModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Gestion voiture</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
            <div class="modal-body" id="Modal-body">   
                      
            </div>
           
        </div>
    </div>
 <!-- -------- -->

    
    

@endsection