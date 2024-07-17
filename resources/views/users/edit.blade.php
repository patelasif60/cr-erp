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
            <li>Edit</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Edit User</h6>
                </div>
                <form action="#" id="new_user">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="id" value="{{ $row->id }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-row ">
                                    <div class="form-group col-md-12">
                                        <label for="name" class="ul-form__label">Name:<?php echo $required_span; ?></label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="{{ $row->name }}">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="username" class="ul-form__label">User Name:<?php echo $required_span; ?></label>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter User Name" value="{{ $row->username }}">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="email" class="ul-form__label">Email:</label>
                                        <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email" value="{{ $row->email }}">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="role" class="ul-form__label">Role:</label>
                                        <select class="form-control" id="role" name="role" onchange="enableDisableWh(this)">
                                            <option value="">Select Role</option>
                                            @if($roles)
                                                @foreach($roles as $row_role)
                                                    <option value="{{ $row_role->id }}" @if($row->role == $row_role->id) selected @endif>{{ $row_role->role }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12" id="warehouse_container" style="display:@if($row->client == 6) none; @else block; @endif">
                                        <label for="role" class="ul-form__label">Warehouse:</label>
                                        <select class="form-control" id="wh" name="wh" disabled>
                                            <option value="">Select Warehouse</option>
                                            @if($whs)
                                                @foreach($whs as $wh)
                                                    <option value="{{ $wh->id }}" @if($row->wh_id == $wh->id) selected @endif>{{ $wh->warehouses }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12" id="client_container"  style="display:@if($row->client == 6) block; @else none; @endif">
                                        <label for="client" class="ul-form__label">Clients:</label>
                                        <select class="form-control select2" id="client" name="client">
                                            <option value="">Select Client</option>
                                            @if($clients)
                                                @foreach($clients as $row_clients)
                                                    <option value="{{ $row_clients->id }}" @if($row->client == $row_clients->id) selected @endif>{{ $row_clients->company_name }}</option>
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
        <div class="col-md-6 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Change Password</h6>
                </div>
                <form action="#" id="change_password">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-row ">
                                    <div class="form-group col-md-12">
                                        <label for="password" class="ul-form__label">Password:</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="password_confirmation" class="ul-form__label">Confirm Password:</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Enter Confirm Password">
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
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-left mt-3">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Notification</h6>
                </div>
                <form action="#" id="notification_form">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-stripped">
                                    <tr>
                                        <th>Notification</th>
                                        <th>Check</th>
                                    </tr>
                                    <tr>
                                        <td>Notification Type</td>
                                        <td>
                                            <select class="form-control select2" id="notification_type" name="notification_type[]" multiple="multiple">
                                                <option value="in_app" @if(isset($notification->notification_type) &&  in_array('in_app',explode(',',$notification->notification_type))) selected @endif>In app</option>
                                                <option value="email" @if(isset($notification->notification_type) &&  in_array('email',explode(',',$notification->notification_type))) selected @endif>Email</option>
                                            </select>
                                        </td>   
                                    </tr>
                                    <tr>
                                        <td>Product Management</td>
                                        <td><input type="checkbox" name="product_management" value="1" <?php if(isset($notification->product_management) && $notification->product_management == 1){ echo 'checked'; } ?>></td>   
                                    </tr>
                                    <tr>
                                        <td>Inventory Management Low Stock</td>
                                        <td><input type="checkbox" name="inventory_low_stock" id="inventory_low_stock" value="1" <?php if(isset($notification->inventory_low_stock) && $notification->inventory_low_stock == 1){ echo 'checked'; } ?>></td>   
                                    </tr>
                                    <tr>
                                        <td>Inventory Management High Stock</td>
                                        <td><input type="checkbox" name="inventory_high_stock" id="inventory_high_stock" value="1" <?php if(isset($notification->inventory_high_stock) && $notification->inventory_high_stock == 1){ echo 'checked'; } ?>></td>   
                                    </tr>
                                    <tr>
                                        <td>Inventory Management Out of Stock</td>
                                        <td><input type="checkbox" name="inventory_out_of_stock" id="inventory_out_of_stock" value="1" <?php if(isset($notification->inventory_out_of_stock) && $notification->inventory_out_of_stock == 1){ echo 'checked'; } ?>></td>   
                                    </tr>
                                </table>
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
@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script>
        enableDisableWh(document.getElementById('role'))
       $(document).ready(function () {
            var role = <?php echo Auth::user()->role; ?>;
            if(role == 3){
                $('#role').css('pointer-events','none');
            }
            $("#new_user").on('submit',function(e){
                e.preventDefault();
                $(".submit").attr("disabled", true);
                var form_cust = $('#new_user')[0]; 
                let form1 = new FormData(form_cust);
                $.ajax({
                    type: "POST",
                    url: '{{ route('users.update',$row->id) }}',
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
                        var errors = data.responseJSON;
                        $.each( errors.errors, function( key, value ) {
                            var ele = "#"+key;
                            $(ele).addClass('border-danger');
                            $('<label class="text-danger">'+ value +'</label>').insertAfter(ele);
                        });
                    }
                })
                
            });

            $("#change_password").on('submit',function(e){
                e.preventDefault();
                $(".submit").attr("disabled", true);
                var form_cust = $('#change_password')[0]; 
                let form1 = new FormData(form_cust);
                $.ajax({
                    type: "POST",
                    url: '{{ route('users.update_password',$row->id) }}',
                    data: form1, 
                    processData: false,
                    contentType: false,
                    success: function( response ) {
                        $(".submit").attr("disabled", false);
                        if(response.error == false){
                            toastr.success(response.msg);
                            setTimeout(function(){
                                
                                location.reload();
                            },2000);
                        }else{
                            toastr.error(response.msg);
                        }
                    },
                    error: function(data){
                        $(".submit").attr("disabled", false);
                        var errors = data.responseJSON;
                        $.each( errors.errors, function( key, value ) {
                            var ele = "#"+key;
                            $(ele).addClass('border-danger');
                            $('<label class="text-danger">'+ value +'</label>').insertAfter(ele);
                        });
                    }
                })
                
            });

            $("#notification_form").on('submit',function(e){
                e.preventDefault();
                $(".submit").attr("disabled", true);
                var form_cust = $('#notification_form')[0]; 
                let form1 = new FormData(form_cust);
                $.ajax({
                    type: "POST",
                    url: '{{ route('users.update_user_notification',$row->id) }}',
                    data: form1, 
                    processData: false,
                    contentType: false,
                    success: function( response ) {
                        $(".submit").attr("disabled", false);
                        if(response.error == false){
                            toastr.success(response.msg);
                            setTimeout(function(){
                                
                                location.reload();
                            },2000);
                        }else{
                            toastr.error(response.msg);
                        }
                    },
                    error: function(data){
                        $(".submit").attr("disabled", false);
                        var errors = data.responseJSON;
                        $.each( errors.errors, function( key, value ) {
                            var ele = "#"+key;
                            $(ele).addClass('border-danger');
                            $('<label class="text-danger">'+ value +'</label>').insertAfter(ele);
                        });
                    }
                })
                
            });

            

            
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
