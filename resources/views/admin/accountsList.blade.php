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
<button type="button" class="popup-with-form btn btn-block btn-primary btn-rounded" data-toggle="modal" data-target="#newDriver" >Ajouter un chauffeur</button>
<div id="newDriver" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="newDriver" aria-hidden="true">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
           <div class="modal-header">
                    <h4 class="modal-title">Ajouter compte</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
            <div class="modal-body">
                <form  method="post" enctype="multipart/form-data" class="floating-labels" action="/add-driver/create" >
                    @csrf
                    <fieldset style="border:0;">

                        <div class="form-group m-b-40">
                            <label for="name">Nom</label>
                            <input type="text" class="form-control" name="name"   >
                        </div>
                         <div class="form-group m-b-40">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email"  >
                        </div>
                            <div class="form-group " style="float:left; width:50%;">
                                <label for="place">Mot de passe</label>
                                <input type="password" class="form-control" name="password" >
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
            <table  class="table table-striped table-bordered" id="customDataTable"  style="width:100%">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Date création</th>
                        <th class="text-nowrap">Action</th>

                    </tr>
                </thead>
                <tbody> 
            @if(!$users->isEmpty())
            @foreach ($users as $user)
            <tr>
                <td data-th="Image"> <img src="{{ asset('storage/users/'.$user->photo_url) }}" style="height:50px;width:50px" > </td>
                <td data-th="Nom">{{$user->name}}</td>
                <td data-th="Email">{{$user->email}}</td>
                <td data-th="Date création">{{$user->created_at}}</td>
                <td data-th="Action" class="text-nowrap">
                    <a href="javascript:void(0)" data-id='{{$user->id}}' data-entity='user'  data-url='edit' class='EditModalBtn'> <i class="fas fa-pencil-alt"></i> </a>
                    |
                    <a href="javascript:void(0)" data-id='{{$user->id}}' data-entity='user'  data-url='delete' class='DeleteModalBtn'> <i class="fas fa-trash-alt text-danger"></i> </a>
                </td>
            </tr>
            @endforeach
            @else
            <tr><td colspan="5" style="text-align:center;">Liste est vide !</td></tr>
            @endif
        </tbody>

    </table>
    
 <!-- delete/edit common modal -->
   <div class="modal fade" id="MainModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Compte utilisateur</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
            <div class="modal-body" id="Modal-body">   
                      
            </div>
           
        </div>
    </div>
 <!-- -------- -->
    
@endsection