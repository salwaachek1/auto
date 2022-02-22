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

            <table  class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
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

            @foreach ($activities as $act)
            <tr>
                <td>{{$act->car->model}} </td>
                <td>{{$act->user->name}}</td>
                <td>{{$act->before_kilos}}</td>
                <td>{{$act->after_kilos}}</td>
                <td>{{$act->expenses}}</td>
                <td>{{$act->fuel}}</td>
                <td>{{$act->destination}}</td>
                <td>{{$act->returning_date}}</td>
                <td class="text-nowrap">
                    <a href="javascript:void(0)" data-id='{{$act->id}}' data-entity='act'  data-url='edit' class='EditModalBtn'> <i class="fas fa-pencil-alt"></i> </a>
                    |
                    <a href="javascript:void(0)" data-id='{{$act->id}}' data-entity='act'  data-url='delete' class='DeleteModalBtn'> <i class="fas fa-trash-alt text-danger"></i> </a>
                    |
                    <a href="javascript:void(0)" data-id='{{$act->id}}' data-entity='act'  data-url='detail' class='DetailModalBtn'> <i class="fas fa-eye text-primary"></i> </a>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>
    
 <!-- delete/edit common modal -->
   <div class="modal fade" id="MainModal" role="dialog">
        <div class="modal-dialog">
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