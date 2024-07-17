@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
    @php $required_span = '<span class="text-danger">*</span>' @endphp
    <div class="breadcrumb">
        <h1>Users</h1>
        <ul>
            <li><a href="">Users</a></li>
            <li>New</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">New User</h6>
                </div>
                <form action="#" id="new_supplier">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-row ">
                                    <div class="form-group col-md-12">
                                        <label for="name" class="ul-form__label">Name:<?php echo $required_span; ?></label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="username" class="ul-form__label">User Name:<?php echo $required_span; ?></label>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter User Name">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="email" class="ul-form__label">Email:</label>
                                        <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="password" class="ul-form__label">Password:</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="password_confirmation" class="ul-form__label">Confirm Password:</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Enter Confirm Password">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="role" class="ul-form__label">Role:</label>
                                        <select class="form-control" id="role" name="role" onchange="enableDisableWh(this)">
                                            <option value="">Select Role</option>
                                            @if($roles)
                                                @foreach($roles as $row_role)
                                                    <option value="{{ $row_role->id }}">{{ $row_role->role }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12" id="warehouse_container">
                                        <label for="role" class="ul-form__label">Warehouse:</label>
                                        <select class="form-control" id="wh" name="wh" disabled>
                                            <option value="">Select Warehouse</option>
                                            @if($whs)
                                                @foreach($whs as $wh)
                                                    <option value="{{ $wh->id }}">{{ $wh->warehouses }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12" id="client_container" style="display:none">
                                        <label for="client" class="ul-form__label">Clients:</label>
                                        <select class="form-control select2" id="client" name="client">
                                            <option value="">Select Client</option>
                                            @if($clients)
                                                @foreach($clients as $row_clients)
                                                    <option value="{{ $row_clients->id }}">{{ $row_clients->company_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="mc-footer">
                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn  btn-primary m-1">Submit</button>
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary m-1">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end of col -->
@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script>
       $(document).ready(function () {
            $('#all_suppliers').DataTable();
            $("#new_supplier").on('submit',function(e){
                e.preventDefault();
                $(".submit").attr("disabled", true);
                var form_cust = $('#new_supplier')[0]; 
                let form1 = new FormData(form_cust);
                $.ajax({
                    type: "POST",
                    url: '{{ route('users.store') }}',
                    data: form1, 
                    processData: false,
                    contentType: false,
                    success: function( response ) {
                        $(".submit").attr("disabled", false);
                        if(response.error == false){
                            toastr.success(response.msg);
                            setTimeout(function(){
                                location.href= response.url;
                            }, 2000);
                        }else{
                            toastr.error(response.msg);
                        }
                    },
                    error: function(data){
                        $(".submit").attr("disabled", false);
                        $('.text-danger').remove();
                        var errors = data.responseJSON;
                        $.each( errors.errors, function( key, value ) {
                            var ele = "#"+key;
                            $(ele).addClass('border-danger');
                            $('<label class="text-danger">'+ value +'</label>').insertAfter(ele);
                        });
                    }
                })
                
            })
       });
       function enableDisableWh(event) {    
        const selected_role = $(event).val();
        
        var role = event.options[event.selectedIndex].innerHTML;
        var wh = document.getElementById('wh');
        if (role === 'WMS User' || role === 'WMS Manager') {
            wh.disabled = false;
        } else {
            wh.disabled = true;
        }

        if(selected_role === '6'){
            $("#warehouse_container").hide();
            $("#client_container").show();
        }else{
            $("#warehouse_container").show();
            $("#client_container").hide();
        }
       }
   </script>
@endsection
