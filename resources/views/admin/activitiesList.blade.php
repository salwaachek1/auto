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
.table-responsive{
    width: 100%;
    margin-bottom: 15px;
    overflow-y: hidden;
    -ms-overflow-style: -ms-autohiding-scrollbar;
    border: 1px solid #ddd;
}    

</style>
<button type="button" class="popup-with-form btn btn-block btn-primary btn-rounded" data-toggle="modal" data-target="#newAct" >Lancer une activité</button>
<div id="newAct" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="newAct" aria-hidden="true">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
           <div class="modal-header">
                    <h4 class="modal-title">Commencer une activité</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
            <div class="modal-body">
                <form  method="post" enctype="multipart/form-data" class="floating-labels" action="/add-activity/create" >
                    @csrf
                    <fieldset style="border:0;">
                        <div class="form-group m-b-40">
                            @if(!$cars->isEmpty())
                            <label for="car_id">Voiture</label>
                            <select class="form-control p-0"  name="car_id" required="">      
                                @foreach($cars as $car)                     
                                <option value="{{$car->id}}">{{$car->model}}</option>
                                @endforeach
                            </select>
                            @else
                            <div class="alert-danger" role="alert" style="text-align:center;padding:10px;"><i class="fas fa-car fa-2x"></i></a> Aucune voiture est disponible !</div>
                            @endif
                        </div>
                        <div class="form-group m-b-40">
                            <label for="before_kilos">Kilométrage initial</label>
                            <input type="number" class="form-control"name="before_kilos" {{$state}} ><span class="highlight"></span> <span class="bar"></span>
                        </div>
                         <div class="form-group m-b-40">
                            <label for="destination">Destination</label>
                            <input type="text" class="form-control" name="destination" {{$state}}  >
                        </div>
                        <div class="form-group m-b-40">
                            <label for="previous_fuel_amount">Carburant initiale</label>
                             <input type="number" class="form-control" name="previous_fuel_amount"  {{$state}} ><span class="highlight"></span> <span class="bar"></span>
                        </div> 
                         
                        <div class="form-group m-b-40">
                            <div class="input-group">
                                <input type="text" class="form-control" readonly>
                                    <div class="input-group-btn">
                            
                                        <span class="fileUpload btn btn-info">
                                            <span class="upl" id="upload">Importer l'image des kilos initiales</span>
                                            <input type="file" {{$state}} class="upload up" id="images" name="images" onchange="readURL(this);" />
                                        </span><!-- btn-orange -->
                                    </div><!-- btn -->
                             </div><!-- group -->
                        </div><!-- form-group -->
                        <br><br><br>
                        <div class="images-preview-div"> </div>
                        <div class="form-group m-b-40">
                            <button type="submit" {{$state}} class="btn btn-success waves-effect waves-light m-r-10">Sauvgarder</button>
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
@if(isset($msgs))
 <div class="alert alert-danger" role="alert" style="width:100%;margin:20px;text-align:center;"> {{ $msgs }} </div>
@endif
@if($errors->any())
    <!-- {!! implode('', $errors->all('<div class="alert alert-danger" role="alert" style="width:100%;margin:20px;text-align:center;">:message</div>')) !!} -->
   <div class="alert alert-danger" role="alert" style="width:100%;margin:20px;text-align:center;"> {{ $errors->first() }} </div>
@endif
<input class="form-control" style="width:40%;margin:10px;" id="search" type="text"  placeholder="recherche.."/> 

@if(!$activities->isEmpty()) 
<form  method="post" enctype="multipart/form-data" id="form1" action="/activities/delete"  style="width:100%">
@if(Auth::user()->role_id=="1") 
<label for="act" style="margin:10px;"><input type="checkbox" id="act" onClick="toggle(this)"  style="margin-right:10px;"> selectionner tout <a href="javascript:void(0)" onclick="document.getElementById('form1').submit();" > <i class="fas fa-trash-alt text-danger"></i> </a></label>
@endif    
                    @csrf
                  
            <table  class="table table-striped table-bordered" id="customDataTable" style="width:100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>Voiture</th>
                        <th>Chauffeur</th>
                        <th>Kilométrage initial</th>
                        <th>Kilométrage </th>
                        <th>Dépenses</th>
                        <th>Carburant acheté</th>
                        <th>Destination</th>
                        <th>Départ</th>
                        <th>Retour</th>
                        <th class="text-nowrap">Action</th>

                    </tr>
                </thead>
                <tbody id="dataTable">            
            @foreach ($activities as $act)
            <tr>
                <td data-th=""><input type="checkbox" class="act-id" id="act" name="activities[]" value="{{$act->id}}"></td>
                <td data-th="Voiture" class="act-model">{{$act->car->model}} </td>
                <td data-th="Chauffeur" class="act-driver">{{$act->user->name}}</td>
                <td data-th="Kilométrage initial" class="act-before-kilo">{{$act->before_kilos}}</td>
                @if($act->after_kilos==null)
                <td data-th="Kilométrage" > -- </td>
                @else
                <td data-th="Kilométrage">{{$act->after_kilos}}</td>
                @endif
                @if($act->expenses==null)
                <td data-th="Dépenses">--</td>
                @else
                <td data-th="Dépenses" class="act-expenses">{{$act->expenses}}</td>
                @endif
                @if($act->fuel==null)
                <td data-th="Carburant acheté">--</td>
                @else
                <td data-th="Carburant acheté" class="act-fuel">{{$act->fuel}}</td>
                @endif
                <td data-th="Destination" class="act-destination">{{$act->destination}}</td>
                <td data-th="Depart" class="act-created-at">{{$act->created_at}}   </td>
                @if($act->returning_date==null)
                <td data-th="Retour"> <i class="fas fa-road"></i></td>
                @else
                <td data-th="Retour" class="act-returning">{{$act->returning_date}}</td>
                @endif
                <td data-th="Action" class="text-nowrap">
                    @if($act->is_done==0)
                    <a href="javascript:void(0)" data-id='{{$act->id}}' data-entity='activity'  data-url='edit' class='EditModalBtn'> <i class="fas fa-pencil-alt"></i> </a>
                    @endif
                    @if((Auth::user()->role_id=="1")&&($act->is_done==1))
                    | <a href="javascript:void(0)" data-id='{{$act->id}}' data-entity='activity'  data-url='delete' class='DeleteModalBtn'> <i class="fas fa-trash-alt text-danger"></i> </a>
                    @endif                    
                    @if($act->is_done==0)
                    |
                    <a href="javascript:void(0)" data-id='{{$act->id}}' data-entity='activity'  data-url='end' class='EndModalBtn'> <i class="fas fa-check text-primary"></i> </a>
                    @endif
                    |
                    <a href="javascript:void(0)" data-id='{{$act->id}}' data-entity='activity'  data-url='details' class='DetailModalBtn'> <i class="fas fa-eye text-primary"></i> </a>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>  
    </form>
    @elseif($activities->isEmpty())
    <table  class="table table-striped table-bordered" id="customDataTable" style="width:100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>Voiture</th>
                        <th>Chauffeur</th>
                        <th>Kilométrage initial</th>
                        <th>Kilométrage </th>
                        <th>Dépenses</th>
                        <th>Carburant acheté</th>
                        <th>Destination</th>
                        <th>Retour</th>
                        <th class="text-nowrap">Action</th>

                    </tr>
                </thead>
                <tbody>
      <tr><td colspan="10" style="text-align:center;">Liste est vide !</td></tr>    
</tbody>
</table>

      @endif
      {!! $activities->links() !!}
 <!-- delete/edit common modal -->
   <div class="modal fade" id="MainModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Activité</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="Modal-body">                         
                </div>           
        </div>
    </div>
 <!-- -------- -->
    
@endsection