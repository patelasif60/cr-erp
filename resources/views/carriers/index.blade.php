@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/inputTags.css')}}">
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Carriers</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <ul class="nav nav-tabs nav-justified">
                <li class="nav-item">
                    <a class="nav-link active" href="#tab_carrier_container" id="carrier_container_tab" role="tab" aria-controls="carrier_container_tab" area-selected="true" data-toggle="tab">Carrier</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="#tab_carrier_account" id="carrier_account_tab" role="tab" aria-controls="carrier_account_tab" area-selected="false" data-toggle="tab">Carrier Accounts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="#tab_order_assignment_by_account" id="order_assignment_by_account_tab" role="tab" aria-controls="order_assignment_by_account_tab" area-selected="false" data-toggle="tab">Order Assignment by Account</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="#tab_service_type_configuration" id="service_type_configuration_tab" role="tab" aria-controls="service_type_configuration_tab" area-selected="false" data-toggle="tab">Service Type configuration</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab_carrier_container" role="tabpanel" area-labelledby="carrier_container_tab">
                    <div class="card text-left">
                        {{-- @if(ReadWriteAccess('AddNewCarrier')) --}}
                        <div class="card-header text-right bg-transparent">
                            <a href="{{ route('carriers.create') }}" class="btn btn-primary btn-md m-1"><i class="fa fa-plus"></i> New Carrier</a>
                        </div>
                        {{-- @endif --}}
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="all_carriers" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Company Name</th>
                                            <th>Main Point of Contact</th>
                                            <th>Client Phone</th>
                                            <th>Client Email</th>
                                            <th>Client Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($data)
                                            @foreach($data as $row)
                                                <tr>
                                                    <td>{{ $row->company_name }}</td>
                                                    <td>{{ $row->main_point_of_contact }}</td>
                                                    <td>{{ $row->client_phone }}</td>
                                                    <td>{{ $row->client_email }}</td>
                                                    <td>{{ $row->client_status }}</td>
                                                    <td>
                                                        {{-- @if(ReadWriteAccess('EditCarrier')) --}}
                                                        <a href="{{ route('carriers.edit',$row->id) }}" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Edit">
                                                            <i class="nav-icon i-Pen-2 "></i>
                                                        </a>
                                                        {{-- @endif --}}
                                                        {{-- @if(ReadWriteAccess('DeleteSupplier')) --}}
                                                        <!-- <form class="d-inline" action="{{ route('carriers.destroy',$row->id) }}" method="POST"> -->
                                                        {{method_field('DELETE')}}
                                                        {{csrf_field()}}
                                                        <!-- <button type="submit" class="btn btn-danger mr-1" value="delete" onClick="return confirm('Are You Sure You Want To Delete This?')"><i class="nav-icon i-Close-Window "></i></button> -->
                                                        {{-- @endif --}}
                                                    <!-- </form> -->

                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab_carrier_account" role="tabpanel" area-labelledby="carrier_account_tab">
                    <div class="card text-left">
                        <div class="card-header text-right bg-transparent">
                            <a href="javascript:void(0)" onClick="GetCarrierModel('{{route('carriers.createCarrierAccount',0)}}')" class="btn btn-primary float-right mb-3"><i class="fa fa-plus"></i> New Carrier Account</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="all_carrier_account" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Carrier</th>
                                            <th>Account #</th>
                                            <th>API Key</th>
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
                <div class="tab-pane fade" id="tab_order_assignment_by_account" role="tabpanel" area-labelledby="order_assignment_by_account_tab">
                     <div class="card text-left">
                        <div class="card-body">
                            <div class="card-header bg-transparent">
                                <h6 class="card-title task-title m-0">Default Account Assignments</h6>
                                <a href="javascript:void(0)" onClick="GetCarrierModel('{{route('carriers.carrierOrderAssignment',1)}}')" class="btn btn-primary float-right mb-3"><i class="fa fa-plus"></i> Edit</a>
                            </div>
                            <div class="table-responsive" id="default_account_assignments">
                                
                            </div>
                        </div>
                    </div>
                    
                     <div class="card text-left mt-3">
                        <div class="card-body">
                            <div class="card-header bg-transparent">
                                <h6 class="card-title task-title m-0">Custom Account Configuration</h6>
                                <a href="javascript:void(0)" onClick="GetCarrierModel('{{route('carriers.carrierOrderAssignment',0)}}')" class="btn btn-primary float-right mb-3"><i class="fa fa-plus"></i> Add New</a>
                            </div>
                            <div class="table-responsive">
                                <table id="custom_account_assignments" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Client</th>
                                            <th>Rules</th>
                                            <th>Temperature</th>
                                           <!--  <th>Account #</th> -->
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
                <div class="tab-pane fade" id="tab_service_type_configuration" role="tabpanel" area-labelledby="service_type_configuration_tab">
                    <div class="card text-left">
                        <div class="card-header bg-transparent">                            
                            <a href="javascript:void(0)" onClick="GetCarrierModel('{{route('carriers.addcarrierServiceConf')}}')" class="btn btn-primary float-right mb-3"><i class="fa fa-plus"></i> Add New</a>
                        </div>
                        <div class="card-body">                            
                            <div class="table-responsive">
                                <table id="service_type_configuration" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>e-tailer Service</th>
                                            <th>UPS Service Type Assigned</th>
                                            <th>FedEx Service Type Assigned</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card text-left mt-3">
                        <div class="card-body">
                            <div class="card-header bg-transparent">
                                <h6 class="card-title task-title m-0">Order Automatic Upgrades</h6>
                                <a href="javascript:void(0)" onClick="GetCarrierModel('{{route('carriers.carrierorderautomaticupgrades',0)}}')" class="btn btn-primary float-right mb-3"><i class="fa fa-plus"></i> Add New</a>
                            </div>
                            <div class="table-responsive">
                                <table id="order_automatic_upgrades" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Service Type</th>
                                            <th>Group Detail</th>
                                            <th>Transit Day</th>
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
        </div>
    </div>
    <!-- end of col -->
    <div class="modal fade" id="MyCarrierModal" data-backdrop="static">
    </div>
@endsection

@section('page-js')
<script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>
    <script src="{{asset('assets/js/sweetalert.script.js')}}"></script>
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script src="{{asset('assets/index.js')}}"></script>
    <script>
       $(document).ready(function () {
            $('#all_carriers').DataTable();
            GetAllCarrierAccounts();
            GetDefaultCarrierDefaultOrderAssigned();
            GetAllCarrierAccountsAssignments();
            GetAllServiceConf();
            GetAutomaticupgrades();
       });

       function GetAllCarrierAccounts(){
            var dt = $('#all_carrier_account').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                
                ajax: '{!! route('carriers.datatable.CarrierAccounts') !!}',
                columns: [
                    { data: 'description', name: 'description' },
                    { data: 'carrier_name', name: 'carrier_name' },
                    { data: 'account_number', name: 'account_number' },
                    { data: 'api_key', name: 'api_key' },
                    { data: 'action', name: 'action' }
                ]
            });
       }

        function GetCarrierModel(url){
            $.ajax({
                url:url,
                method:'GET',
                success:function(res){
                    $("#MyCarrierModal").html(res);
                    $("#MyCarrierModal").modal();
                }
            });
        }
        deleteCarrierAccount = (id) =>{
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
                        url: '{{ route('carrieraccount.destroy') }}',
                        data: {'id':id},
                        success: function( response ){
                            $("#preloader").hide();
                            location.reload()
                        }
                    })
                }
            });
        }
        deleteCarrierAccountAssigment = (id) =>{
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
                        url: '{{ route('carrieraccount.deleteCarrierAccountAssigment') }}',
                        data: {'id':id},
                        success: function( response ){
                            $("#preloader").hide();
                             GetDefaultCarrierDefaultOrderAssigned();
                         GetAllCarrierAccountsAssignments();
                        }
                    })
                }
            });
        }

        function GetDefaultCarrierDefaultOrderAssigned(){
            $.ajax({
                url:'{!! route('carriers.GetDefaultOrderAccountAssignments') !!}',
                method:'GET',
                dataType:'html',
                success:function(data){
                    $("#default_account_assignments").html(data);
                }
            })
        }
        function GetAllCarrierAccountsAssignments(){
            var dt = $('#custom_account_assignments').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                
                ajax: '{!! route('carriers.datatable.CarrierAccountsAssignments') !!}',
                columns: [
                    { data: 'description', name: 'description' },
                    { data: 'client_name', name: 'client_name' },
                    { data: 'rules', name: 'rules' },
                    { data: 'group_details', name: 'group_details' },
                    { data: 'action', name: 'action' }
                ]
            });
       }
       function GetAllServiceConf(){
            var dt = $('#service_type_configuration').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                
                ajax: '{!! route('carriers.datatable.CarrierAllServiceConf') !!}',
                columns: [
                    { data: 'etailer_service_name', name: 'etailer_service_name' },
                    { data: 'ups_service_type_id', name: 'ups_service_type_id' },
                    { data: 'fedex_service_type_id', name: 'fedex_service_type_id' },
                    
                    { data: 'action', name: 'action' }
                ]
            });
       }
       function GetAutomaticupgrades(){
            var dt = $('#order_automatic_upgrades').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                
                ajax: '{!! route('carriers.datatable.GetAutomaticupgrades') !!}',
                columns: [
                    {data: 'client_name', name: 'client_name' },
                    { data: 'service_type_id', name: 'service_type_id' },
                    { data: 'group_detail', name: 'group_detail' },
                    { data: 'transit_day', name: 'transit_day' },
                    { data: 'action', name: 'action' }
                ]
            });
       }
       deleteOrderUpgrade = (id) =>{
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
                        url: '{{ route('carrieraccount.deleteOrderUpgrade') }}',
                        data: {'id':id},
                        success: function( response ){
                            $("#preloader").hide();
                            GetAutomaticupgrades();
                        }
                    })
                }
            });
        }
    
   </script>
@endsection
