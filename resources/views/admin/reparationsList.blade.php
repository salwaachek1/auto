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
<button type="button" class="popup-with-form btn btn-block btn-primary btn-rounded" data-toggle="modal" data-target="#newAct" >Lancer une réparation</button>
<div id="newAct" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="newAct" aria-hidden="true">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
           <div class="modal-header">
                    <h4 class="modal-title">Commencer une réparation</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
            <div class="modal-body">
                <form  method="post" enctype="multipart/form-data" class="floating-labels" action="/add-reparation/create" >
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
                            <label for="garage">Nom du garage</label>
                            <input type="text" class="form-control"name="garage" {{$state}} >
                        </div>
                         <div class="form-group m-b-40">
                            <label for="phone">Télèphone </label>
                            <input type="text" class="form-control" name="phone" {{$state}}  >
                        </div>                         
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

@if(!$reparations->isEmpty()) 
<form  method="post" enctype="multipart/form-data" id="form1" action="/reparations/delete"  style="width:100%">
@if(Auth::user()->role_id=="1") 
<label for="act" style="margin:10px;"><input type="checkbox" id="act" onClick="toggle(this)"  style="margin-right:10px;"> selectionner tout <a href="javascript:void(0)" onclick="document.getElementById('form1').submit();" > <i class="fas fa-trash-alt text-danger"></i> </a></label>
@endif    
                    @csrf
                  
            <table  class="table table-striped table-bordered" id="customDataTable" style="width:100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>Voiture</th>
                        <th>Garage</th>
                        <th>Dernier Chauffeur</th>
                        <th>Diagnostique</th>
                        <th>Pieces à remplacer</th>
                        <th>Dépenses</th>
                        <th>Date d'entrée</th>
                        <th>Date sortie</th>
                        <th>Télèphone</th>
                        <th class="text-nowrap">Action</th>

                    </tr>
                </thead>
                <tbody id="dataTable">            
            @foreach ($reparations as $rep)
            <tr>
                <td data-th=""><input type="checkbox" id="act" name="reparations[]" value="{{$rep->id}}"></td>
                <td data-th="Voiture" >{{$rep->car->model}} </td>
                <td data-th="Garage" >{{$rep->garage}}</td>
                <td data-th="Dernier Chauffeur" ></td>
                @if($rep->diagnosis==null)
                <td data-th="Diagnostique" > -- </td>
                @else
                <td data-th="Diagnostique">{{$rep->diagnosis}}</td>
                @endif
                @if($rep->replaced_parts==null)
                <td data-th="Pieces à remplacer">--</td>
                @else
                <td data-th="Pieces à remplacer">{{$rep->replaced_parts}}</td>
                @endif
                @if($rep->fees==null)
                <td data-th="Dépenses">--</td>
                @else
                <td data-th="Dépenses">{{$rep->fees}}</td>
                @endif                
                <td data-th="Date d'entrée" >{{$rep->created_at}}   </td>
                @if($rep->date_out==null)
                <td data-th="Date sortie"> <i class="fas fa-road"></i></td>
                @else
                <td data-th="Date sortie" >{{$rep->date_out}}</td>
                @endif
                <td data-th="Télèphone">{{$rep->phone}}</td>         
                <td data-th="Action" class="text-nowrap">
                    @if($rep->is_done==0)
                    <a href="javascript:void(0)" data-id='{{$rep->id}}' data-entity='reparation'  data-url='edit' class='EditModalBtn'> <i class="fas fa-pencil-alt"></i> </a>
                    @endif
                    @if($rep->is_done==1)
                    | <a href="javascript:void(0)" data-id='{{$rep->id}}' data-entity='reparation'  data-url='delete' class='DeleteModalBtn'> <i class="fas fa-trash-alt text-danger"></i> </a>
                    @endif                    
                    @if($rep->is_done==0)
                    |
                    <a href="javascript:void(0)" data-id='{{$rep->id}}' data-entity='reparation'  data-url='end' class='EndModalBtn'> <i class="fas fa-check text-primary"></i> </a>
                    @endif
                    |
                    <a href="javascript:void(0)" data-id='{{$rep->id}}' data-entity='reparation'  data-url='details' class='DetailModalBtn'> <i class="fas fa-eye text-primary"></i> </a>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>  
    </form>
    @elseif($reparations->isEmpty())
    <table  class="table table-striped table-bordered" id="customDataTable" style="width:100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>Voiture</th>
                        <th>Garage</th>
                        <th>Dernier Chauffeur</th>
                        <th>Diagnostique</th>
                        <th>Pieces à remplacer</th>
                        <th>Dépenses</th>
                        <th>Date d'entrée</th>
                        <th>Date sortie</th>
                        <th>Télèphone</th>
                        <th>Etat de réparation</th>
                        <th class="text-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody>
      <tr><td colspan="10" style="text-align:center;">Liste est vide !</td></tr>    
</tbody>
</table>

      @endif
      {!! $reparations->links() !!}
 <!-- delete/edit common modal -->
   <div class="modal fade" id="MainModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> <i class="fas fa-road"></i> Gestion Réparation</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="Modal-body">                         
                </div>           
        </div>
    </div>
 <!-- -------- -->
    
@endsection