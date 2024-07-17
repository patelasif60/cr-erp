@extends('layouts.master')

@section('page-css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.css" rel="stylesheet">  
<link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
    <style>
        .error{
            color:red;
        }
    </style>
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Role Permissions</h1>
        <!-- <ul>
            test
            <li><a href="">UI Kits</a></li>
            <li>Datatables</li>
        </ul> -->
    </div>
    <div class="separator-breadcrumb border-top"></div>
    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header text-right bg-transparent">
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" style="text-align: center;">
                            <thead>
                                <tr>
                                    <th class="text-justify">Menus & Sub-menus Visibility</th>
                                    <!-- <th>Menus & Sub-menus Link</th> -->
                                    <th>User</th>
                                    <th>Manager</th>
                                    <th>Administrator</th>                                    
                                    <th>WMS Manager</th>
                                    <th>WMS User</th>
                                </tr>
                            </thead>
                            <tbody id="menus_body">
                            @if($roles_permissions_menus)
                                @foreach($roles_permissions_menus as $row)
                                    <tr id="{{$row->id}}">
                                        <td class="text-justify">{{$row->module_title}}</td>
                                        <!-- <td class="text-justify">{{$row->module_link}}</td> -->
                                        <td><input type="checkbox" style="zoom:1.5;" id="add" role="user" module="{{$row->module_link}}" <?php if($row->user == 1) echo "checked";?>></td>
                                        <td><input type="checkbox" style="zoom:1.5;" id="add" role="manager" module="{{$row->module_link}}" <?php if($row->manager == 1) echo "checked";?>></td>
                                        <td><input type="checkbox" style="zoom:1.5;" id="add" role="administrator" module="{{$row->module_link}}" <?php if($row->administrator == 1) echo "checked";?>></td>
                                        <td><input type="checkbox" style="zoom:1.5;" id="add" role="wms_manager" module="{{$row->module_link}}" <?php if($row->wms_manager == 1) echo "checked";?>></td>
                                        <td><input type="checkbox" style="zoom:1.5;" id="add" role="wms_user" module="{{$row->module_link}}" <?php if($row->wms_user == 1) echo "checked";?>></td>
                                    </tr>
                                @endforeach
                            @endif
                                
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-primary" id="save_menus_order" onclick="save_menus_order()">Save Menus Order</button>
                    <input type="hidden" name="menus_order" id="menus_order">
                </div>
            </div>

            <div class="card text-left mt-5">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" style="text-align: center;">
                            <thead>
                                <tr>
                                    <th class="text-justify">Functions</th>
                                    <!-- <th>Menus & Sub-menus Link</th> -->
                                    <th>User</th>
                                    <th>Manager</th>
                                    <th>Administrator</th>
                                    <th>WMS Manager</th>
                                    <th>WMS User</th>
                                </tr>
                            </thead>
                            <tbody id="functions_body">
                            @if($roles_permissions_functions)
                                @foreach($roles_permissions_functions as $row)
                                    <tr id="{{$row->id}}">
                                        <td class="text-justify">{{$row->module_title}}</td>
                                        <!-- <td class="text-justify">{{$row->module_link}}</td> -->
                                        <td><input type="checkbox" style="zoom:1.5;" id="add"  role="user" module="{{$row->module_link}}" <?php if($row->user == 1) echo "checked";?>></td>
                                        <td><input type="checkbox" style="zoom:1.5;" id="add" role="manager" module="{{$row->module_link}}" <?php if($row->manager == 1) echo "checked";?>></td>
                                        <td><input type="checkbox" style="zoom:1.5;" id="add" role="administrator" module="{{$row->module_link}}" <?php if($row->administrator == 1) echo "checked";?>></td>
                                        <td><input type="checkbox" style="zoom:1.5;" id="add" role="wms_manager" module="{{$row->module_link}}" <?php if($row->wms_manager == 1) echo "checked";?>></td>
                                        <td><input type="checkbox" style="zoom:1.5;" id="add" role="wms_user" module="{{$row->module_link}}" <?php if($row->wms_user == 1) echo "checked";?>></td>
                                    </tr>
                                @endforeach
                            @endif
                                
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-primary" onclick="save_functions_order()">Save Functions Order</button>
                    <input type="hidden" name="functions_order" id="functions_order">
                </div>
            </div>

            <div class="card text-left mt-5">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" style="text-align: center;">
                            <thead>
                                <tr>
                                    <th class="text-justify">WMS Menu</th>
                                    <th>User</th>
                                    <th>Manager</th>
                                    <th>Administrator</th>
                                    <th>WMS Manager</th>
                                    <th>WMS User</th>
                                </tr>
                            </thead>
                            <tbody id="wms_body">
                            @if($roles_permissions_wms)
                                @foreach($roles_permissions_wms as $row)
                                    <tr id="{{$row->id}}">
                                        <td class="text-justify">{{$row->module_title}}</td>
                                        <td><input type="checkbox" style="zoom:1.5;" id="add"  role="user" module="{{$row->module_link}}" <?php if($row->user == 1) echo "checked";?>></td>
                                        <td><input type="checkbox" style="zoom:1.5;" id="add" role="manager" module="{{$row->module_link}}" <?php if($row->manager == 1) echo "checked";?>></td>
                                        <td><input type="checkbox" style="zoom:1.5;" id="add" role="administrator" module="{{$row->module_link}}" <?php if($row->administrator == 1) echo "checked";?>></td>                                          
                                        <td><input type="checkbox" style="zoom:1.5;" id="add" role="wms_manager" module="{{$row->module_link}}" <?php if($row->wms_manager == 1) echo "checked";?>></td>
                                        <td><input type="checkbox" style="zoom:1.5;" id="add" role="wms_user" module="{{$row->module_link}}" <?php if($row->wms_user == 1) echo "checked";?>></td>
                                    </tr>
                                @endforeach
                            @endif
                                
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-primary" onclick="save_wms_order()">Save WMS Menu</button>
                    <input type="hidden" name="wms_menu" id="wms_menu">
                </div>
            </div>

            <div class="card text-left mt-5">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" style="text-align: center;">
                            <thead>
                                <tr>
                                    <th class="text-justify">Notification</th>
                                    <!-- <th>Menus & Sub-menus Link</th> -->
                                    <th>User</th>
                                    <th>Manager</th>
                                    <th>Administrator</th>
                                    <th>WMS Manager</th>
                                    <th>WMS User</th>
                                </tr>
                            </thead>
                            <tbody id="functions_body">
                            @if($roles_permissions_notification)
                                @foreach($roles_permissions_notification as $row)
                                    <tr id="{{$row->id}}">
                                        <td class="text-justify">{{$row->module_title}}</td>
                                        <!-- <td class="text-justify">{{$row->module_link}}</td> -->
                                        <td><input type="checkbox" style="zoom:1.5;" id="add"  role="user" module="{{$row->module_link}}" <?php if($row->user == 1) echo "checked";?>></td>
                                        <td><input type="checkbox" style="zoom:1.5;" id="add" role="manager" module="{{$row->module_link}}" <?php if($row->manager == 1) echo "checked";?>></td>
                                        <td><input type="checkbox" style="zoom:1.5;" id="add" role="administrator" module="{{$row->module_link}}" <?php if($row->administrator == 1) echo "checked";?>></td>
                                        <td><input type="checkbox" style="zoom:1.5;" id="add" role="wms_manager" module="{{$row->module_link}}" <?php if($row->wms_manager == 1) echo "checked";?>></td>
                                        <td><input type="checkbox" style="zoom:1.5;" id="add" role="wms_user" module="{{$row->module_link}}" <?php if($row->wms_user == 1) echo "checked";?>></td>
                                    </tr>
                                @endforeach
                            @endif
                                
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-primary" onclick="save_notifications_order()">Save Notifications Order</button>
                    <input type="hidden" name="functions_order" id="functions_order">
                </div>
            </div>
        
    </div>
    <!-- end of col -->
@endsection

@section('page-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js"></script> 
<script src="{{asset('assets/js/sweetalert.script.js')}}"></script>
<script>

$('#menus_body').sortable({
    update: function(event, ui) {
        var productOrder = $(this).sortable('toArray').toString();
        $("#menus_order").val(productOrder);
    }
});

$('#functions_body').sortable({
    update: function(event, ui) {
        var productOrder = $(this).sortable('toArray').toString();
        $("#functions_order").val(productOrder);
    }
});

function save_menus_order(){
    var order = $('#menus_order').val();
    $.ajax({
        type: 'POST',
        data: {order:order},
        url:  "<?php echo route('roles.save_menus_order')?>",
        success: function(response){
            location.reload();
        }		
    });
}

function save_functions_order(){
    var order = $('#functions_order').val();
    $.ajax({
        type: 'POST',
        data: {order:order},
        url:  "<?php echo route('roles.save_functions_order')?>",
        success: function(response){
            location.reload();
        }		
    });
}

function save_notifications_order(){
    var order = $('#functions_order').val();
    $.ajax({
        type: 'POST',
        data: {order:order},
        url:  "<?php echo route('roles.save_notifications_order')?>",
        success: function(response){
            location.reload();
        }		
    });
}

function save_wms_order(){
    var order = $('#wms_menu').val();
    $.ajax({
        type: 'POST',
        data: {order:order},
        url:  "<?php echo route('roles.save_wms_order')?>",
        success: function(response){
            location.reload();
        }		
    });
}

  $(document).ready(function(){
	    $(document).on("click","#add",function() {
		    var role = $(this).attr('role');
		    var module_link = $(this).attr('module');
		    if($(this).is(":checked")){
		        var valueck = "1";
		    }else{
			    var valueck = "0";
			}
		    $.ajax({
		        type: 'POST',
                data: {module_link:module_link,role:role,valueck:valueck},
		        url:  "<?php echo route('roles.display_access')?>",
		        success: function(response){
                    toastr.success(response.msg);
			    }		
            });
        
	    });
  });
 </script>
 @endsection