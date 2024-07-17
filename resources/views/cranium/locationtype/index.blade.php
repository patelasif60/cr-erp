@extends('layouts.master')

@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
    <style>
        .error{
            color:red;
        }
    </style>
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Location Type</h1>
        <!-- <ul>
            <li><a href="">UI Kits</a></li>
            <li>Datatables</li>
        </ul> -->
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header text-right bg-transparent">
                    @if(ReadWriteAccess('AllSubMenusSelectionfunctions'))
                    <a href="javascript:void(0);" class="btn btn-primary btn-md m-1" onClick="openModal()"><i class="i-Add text-white mr-2"></i>Add Location Types</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="location_type_datatable" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Location Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="MyModal" tabindex="-1" role="dialog" aria-labelledby="MyModal" aria-hidden="true">
        <div class="modal-dialog">
            <!--Modal Content-->
            <div class="modal-content">
                <!-- Modal Header-->
                <div class="modal-header" style="background-color:#fff;">
                    <h3 id="hmodelHeader"></h3>
                    <!--Close/Cross Button-->
                     <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
                </div> 
                <form  method="POST" data-form="add" action="javascript:void(0)" id="add_form" >
                    @csrf
                    <div class="modal-body">
                        <lable for="type">Location Type</lable>
                        <input type="text" class="form-control" name="type" placesholder="Add new type" id="type" style="width:100%;"/> 
                    </div> 
                    <input type="hidden" name="id" id="id" value="">
                    <div class="modal-footer"> 
                        <button type="submit" class="btn btn-primary btn-txt">Add</button> 
                        <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end of col -->
@endsection

@section('page-js')
    <script src="{{ asset('assets/js/validation/jquery.validate.js') }}"></script>
    <script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>
    <script src="{{asset('assets/js/sweetalert.script.js')}}"></script>
    <script src="{{ asset('assets/js/validation/additional-methods.min.js') }}"></script>
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script>
       $(document).ready(function () {
            var table_html = '';
            var table_html_td = '';
            var i = 1;
            var dt = $('#location_type_datatable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                
                ajax: '{!! route('datatable.locationtypesList') !!}',
                columns: [
                    { data: 'type', name: 'type' },
                    { data: 'action', name: 'action', searchable: false }
                ]
            });
       });
       
        function openModal(){
            $('#MyModal').modal('show');
            $('#add_form').attr('data-form','add');
            $('#hmodelHeader').text('Add Location Type');
            $('#id').val('');
            $('.btn-txt').text('Add');

        }
        function openEditModal(id,type){
            $('#type').val(type);
            $('#id').val(id);
            $('#MyModal').modal('show');
            $('#add_form').attr('data-form','edit');
            $('#hmodelHeader').text('Edit Location Type');
            $('.btn-txt').text('Save');
        }

        $("#add_form").validate({
            submitHandler(form){
                var formtype = $(form).attr('data-form');
                $(".submit").attr("disabled", true);
                var form_cust = $('#add_form')[0]; 
                let form1 = new FormData(form_cust);
                var url = '{{ route('locationtypes.store') }}';
                if(formtype == 'edit'){
                    url = '{{ route('locationtypes.update') }}'
                }
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form1, 
                    processData: false,
                    contentType: false,
                    success: function( response ) {
                        if(response.error == 0){
                            toastr.success(response.msg);
                            setTimeout(function(){
                                location.reload();
                            },2000);
                        }else{
                            $(".submit").attr("disabled", false);
                            toastr.error(response.msg);
                        }
                    },
                    error: function(data){
                        $(".submit").attr("disabled", false);
                        var errors = data.responseJSON;
                        $.each( errors.errors, function( key, value ) {
                            var ele = "#"+key;
                            $(ele).addClass('error');
                            $('<label class="error">'+ value +'</label>').insertAfter(ele);
                        });
                  }
                })
                return false;
            }
        });
        deleteLocationType = (id) =>{
            swal({
                    title: 'Are you sure?',
                    text: "This information will be permanently deleted!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then(function(result){
                    if(result)
                    {
                        $.ajax({
                            type: "Delete",
                            url: '{{ route('locationtypes.destroy') }}',
                            data: {'id':id},
                            success: function( response ){
                                $("#preloader").hide();
                                location.reload()
                            }
                        })
                    }
            });
        }
    </script>
@endsection