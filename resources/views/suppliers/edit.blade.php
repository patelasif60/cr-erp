@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
    <style>
        .flatpickr-wrapper{
            width:100%;
        }
        .myClass{
            display: flex !important;
        }
        .form-file-control {
            display: block;
            width: 100%;
            border: 1px solid #ced4da;
            padding: 0.375rem 0.75rem;
            font-size: .813rem;
            line-height: 1.5;
            color: #665c70;
            background-color: #fff;
            background-clip: padding-box;
            border-radius: 0.25rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }
    </style>
@endsection

@section('main-content')
@php
    $required_span = '<span class="text-danger">*</span>';
    $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
@endphp
    <div class="breadcrumb">
        <h1>Suppliers</h1>
        <ul>
            <li><a href="javascript:void(0);">Suppliers</a></li>
            <li>Edit</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Edit Supplier</h6>
                </div>
                <form action="javascript:void(0);" id="update_supplier">
                    @method('put')
                    <input type="hidden" name="id" value="{{ $row->id }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="name" class="ul-form__label">Supplier Name:<?php echo $required_span; ?></label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Supplier Name" value="{{ $row->name }}">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="main_point_of_contact" class="ul-form__label">Main Point of Contact:<?php echo $required_span; ?></label>
                                        <input type="text" class="form-control" id="main_point_of_contact" name="main_point_of_contact" placeholder="main Point of Contact" value="{{ $row->main_point_of_contact }}">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="address" class="ul-form__label">Supplier Address:<?php echo $required_span; ?></label>
                                        <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="{{ $row->address }}">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="address2" class="ul-form__label">Supplier Address 2:<?php echo $required_span; ?></label>
                                        <input type="text" class="form-control" id="address2" name="address2" placeholder="Address 2" value="{{  $row->address2 }}">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="address2" class="ul-form__label">Supplier City:</label>
                                        <input type="text" class="form-control" id="city" name="city" placeholder="City" value="{{  $row->city }}">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="address2" class="ul-form__label">Supplier State:</label>
                                        <input type="text" class="form-control" id="state" name="state" placeholder="State" value="{{  $row->state }}">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="address2" class="ul-form__label">Supplier Zip:</label>
                                        <input type="text" class="form-control" id="zip" name="zip" placeholder="Zip" value="{{  $row->zip }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12">
                                    <label for="phone" class="ul-form__label">Supplier Phone:<?php echo $required_span; ?></label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter Supplier Phone" value="{{ $row->phone }}">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="email" class="ul-form__label">Supplier Email:<?php echo $required_span; ?></label>
                                    <input type="text" class="form-control" id="email" name="email" placeholder="Enter Supplier Email" value="{{ $row->email }}">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="website" class="ul-form__label">Supplier Website:</label>
                                    <input type="text" class="form-control" id="website" name="website" placeholder="Enter Supplier Website" value="{{ $row->website }}">
                                </div>
                                 <div class="form-group col-md-12">
                                    <label for="supplier_product_package_type" class="ul-form__label">Supplier Package Type:</label>
                                    <select class="form-control select2" id="supplier_product_package_type" name="supplier_product_package_type">
                                        <option value="">--Select--</option>
                                        @foreach($productPackageType as  $val)
                                            <option value="{{ $val }}" {{ $row->supplier_product_package_type == $val ? 'selected': '' }}>{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- <div class="form-group col-md-12">
                                    <label for="csv_formate" class="ul-form__label">Assigned Supplier Table:<?= $required_span ?></label>
                                    <select id="csv_formate" name="csv_formate" class="form-control">
                                        <option value="">Select</option>
                                        <option value="supplier_dot" @if($row->csv_formate == "supplier_dot") selected @endif>DOT</option>
                                        <option value="supplier_kehe" @if($row->csv_formate == "supplier_kehe") selected @endif>KEHE</option>
                                        <option value="supplier_dryers" @if($row->csv_formate == "supplier_dryers") selected @endif>DRYERS</option>
                                        <option value="supplier_hersley" @if($row->csv_formate == "supplier_hersley") selected @endif>HERSLEY</option>
                                        <option value="supplier_mars" @if($row->csv_formate == "supplier_mars") selected @endif>MARS</option>
                                        <option value="supplier_nestle" @if($row->csv_formate == "supplier_nestle") selected @endif>NESTLE</option>
                                        <option value="supplier_miscellaneous" @if($row->csv_formate == "supplier_miscellaneous") selected @endif>MISC.</option>
                                        <option value="3pl_client_product" @if($row->csv_formate == "3pl_client_product") selected @endif>3PL Client</option>
                                    </select>
                                </div> -->
                            </div>
                            <div class="col-md-3">
                                <div class="form-group col-md-12">
                                    <label for="status" class="ul-form__label">Status:</label>
                                    <select class="form-control" id="status" name="status" >
                                        <option value="Active" @if($row->status == "Active")
                                        selected
                                        @endif>Active</option>
                                        <option value="Inactive" @if($row->status == "Inactive")
                                            selected
                                            @endif>Inactive</option>
                                        <option value="Secondary" @if($row->status == "Secondary")
                                            selected
                                            @endif>Secondary</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="description" class="ul-form__label">Supplier Description:</label>
                                    <textarea name="description" id="description" cols="10" rows="3" class="form-control" placeholder="Enter Supplier Description">{{ $row->description }}</textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="next_order_date" class="ul-form__label">Next Order Date:</label>
                                    <input type="text" class="form-control flatpickr" id="next_order_date" name="next_order_date" value="{{ $row->next_order_date }}">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="time_zone_id" class="ul-form__label">Time Zone:</label>
                                    <select class="form-control select2" id="time_zone_id" name="time_zone_id">
                                        <option value="">--Select--</option>
                                        @if($time_zones)
                                            @foreach($time_zones as $id => $val)
                                                <option value="{{ $id }}" @if($row->time_zone_id == $id) selected @endif >{{ $val }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="account_manager" class="ul-form__label">Account Manager:</label>
                                     <select class="form-control select2" id="account_manager" name="account_manager">
                                        <option>--Select--</option>
                                        @if($managers)
                                            @foreach($managers as $id => $val)
                                                <option value="{{ $id }}" @if($id == $row->account_manager) selected @endif>{{ $val }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="sales_manager" class="ul-form__label">Sales Manager:</label>
                                    <select class="form-control select2" id="sales_manager" name="sales_manager">
                                        <option value="">--Select--</option>
                                        @if($managers)
                                            @foreach($managers as $id => $val)
                                                <option value="{{ $id }}" @if($id == $row->sales_manager) selected @endif>{{ $val }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="warehouses_assigned" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Warehouse(s) stocking this product">Warehouse(s) Assigned <span class="text-danger">*</span></label>
                                    <table class="table table-bordered" width="10%">
                                        <tr>
                                            <th></th>
                                            <th>Stocked</th>
                                        </tr>
                                        @if ($warehouses)
                                            @foreach($warehouses as $warehouse)
                                                <tr>
                                                    <th width="25%" class="text-left"><label for="{{ $warehouse }}">{{ $warehouse }}</label></th>
                                                    <td width="25%" class="text-center">
                                                        <input type="checkbox" name="warehouses_assigned[]" @if(in_array($warehouse,explode(',',$row->warehouse))) checked @endif id="{{ $warehouse }}" value="{{ $warehouse }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="mc-footer">
                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn  btn-primary m-1">Submit</button>
                                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary m-1">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="col-md-12 mt-4">
                    <ul class="nav nav-tabs nav-justified">
                        <li class="nav-item">
                            <a class="nav-link active" href="#tab_supplier_configuation" id="supplier_configuation_tab" role="tab" aria-controls="supplier_configuation_tab" area-selected="true" data-toggle="tab">Supplier Configuration</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_contacts" id="contacts_tab" role="tab" aria-controls="contacts_tab" area-selected="false" data-toggle="tab">Contacts</a>
                        </li>
                        @if($row->supplier_product_package_type == 'Product')
                        <li class="nav-item">
                            <a class="nav-link" href="#tab_product_management" id="product_management_tab" role="tab" aria-controls="   product_management_tab" area-selected="false" data-toggle="tab">Product Management</a>
                        </li>
                        @else
                        <li class="nav-item">
                            <a class="nav-link" href="#tab_packaging" id="packaging_tab" role="tab" aria-controls="packaging_tab" area-selected="false" data-toggle="tab">Packaging & Materials</a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_warehouse_orders" id="warehouse_orders_tab" role="tab" aria-controls="warehouse_orders_tab" area-selected="false" data-toggle="tab">Warehouse Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_dropship_orders" id="dropship_orders_tab" role="tab" aria-controls="dropship_orders_tab" area-selected="false" data-toggle="tab">Dropship Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_supplier_service" id="supplier_service_tab" role="tab" aria-controls="supplier_service_tab" area-selected="false" data-toggle="tab">Supplier Service</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_tbd" id="tbd_tab" role="tab" aria-controls="tbd_tab" area-selected="false" data-toggle="tab">TBD:</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tab_documents" id="documents_tab" role="tab" aria-controls="documents_tab" area-selected="false" data-toggle="tab">Documents</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tab_reports" id="reports_tab" role="tab" aria-controls="reports_tab" area-selected="false" data-toggle="tab">Reports</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab_supplier_configuation" role="tabpanel" area-labelledby="supplier_configuation_tab">
                            <form action="javascript:void(0);" method="POST" id="orderScheduleForm">
                                @csrf
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label class="border rounded p-2 font-weight-bold">Order Schedule</label>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Frequency</th>
                                                <th>Order Day</th>
                                                <th>Cutoff Time</th>
                                                <th>Delivery Day</th>
                                                <th>Owner</th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <select name="order_schedule" id="order_schedule" class="form-control">
                                                        <option value="Daily" @if($row->frequency == "Daily")selected @endif>Daily</option>
                                                        <option value="Monthly" @if($row->frequency == "Monthly")selected @endif>Monthly</option>
                                                        <option value="Weekly" @if($row->frequency == "Weekly")selected @endif>Weekly</option>
                                                        <option value="Yearly" @if($row->frequency == "Yearly")selected @endif>Yearly</option>
                                                        <option value="Ad Hoc" @if($row->frequency == "Ad Hoc")selected @endif>Ad Hoc</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="order_deadlines" id="order_deadlines" class="form-control">
                                                        <option value="">Select</option>
                                                        @foreach ($days as $day)
                                                            <option value="{{ $day }}" @if($day == $row->order_deadlines) selected @endif>{{ $day }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="cuttoff_time" id="cuttoff_time" class="form-control" value="{{ $row->cuttoff_time }}">
                                                </td>
                                                <td>
                                                    <select name="delivery_day" id="delivery_day" class="form-control">
                                                        <option value="">Select</option>
                                                        @foreach ($days as $day)
                                                            <option value="{{ $day }}" @if($day == $row->delivery_day) selected @endif>{{ $day }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="owner" id="owner" class="form-control">
                                                        <option value="">Select</option>
                                                        @foreach ($users as $user)
                                                            <option value="{{ $user }}" @if($user == $row->owner) selected @endif>{{ $user }}</option>
                                                        @endforeach
                                                    </select>

                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="account_number">Account Number:</label>
                                                <input type="number" min="1" name="account_number" id="account_number" class="form-control" value="{{ $row->e_trailer_account_number }}">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="minimums">Minimums:</label>
                                                <input type="text" name="minimums" id="minimums" class="form-control" value="{{ $row->minimums }}">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="order_restriction_details">Order Restriction Details:</label>
                                                <textarea name="order_restriction_details" id="order_restriction_details" cols="3" rows="1" class="form-control">{{ $row->order_restriction_details }}</textarea>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="delivery_schedule">Delivery Schedule:</label>
                                                <input type="text" name="delivery_schedule" id="delivery_schedule" class="form-control" value="{{ $row->delivery_schedule }}">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="lead_time_overview_notes">Lead Time Overview Notes:</label>
                                                <input type="text" name="lead_time_overview_notes" id="lead_time_overview_notes" class="form-control" value="{{ $row->lead_time_overview_notes }}">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="order_method">Order Method:</label>
                                                <input type="text" name="order_method" id="order_method" class="form-control" value="{{ $row->order_method }}">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="order_portal_url">Order Portal Url:</label>
                                                <input type="text" name="order_portal_url" id="order_portal_url" class="form-control" value="{{ $row->order_portal_url }}">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="order_portal_username">OrderPortal Username:</label>
                                                <input type="text" name="order_portal_username" id="order_portal_username" class="form-control" value="{{ $row->order_portal_username }}">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="order_portal_password">OrderPortal Password:</label>
                                                <input type="text" name="order_portal_password" id="order_portal_password" class="form-control" value="{{ $row->order_portal_password }}">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="card-footer bg-transparent mt-5">
                                    <div class="mc-footer">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <button type="submit" class="btn  btn-primary m-1">Submit</button>
                                                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary m-1">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="tab_contacts" role="tabpanel" area-labelledby="contacts_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card o-hidden mb-4">
                                        <div class="card-header">
                                            <h3 class="w-50 float-left card-title m-0">Contacts</h3>

                                            <div class="separator-breadcrumb">
                                                    <a href="javascript:void(0);" onclick="getModal('{{ route('suppliers.createSupplierContact',$row->id) }}')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                        <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Add New Contact
                                                    </a>
                                            </div>
                                            <div class="dropdown dropleft text-right w-50 float-right">
                                            </div>

                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="contacts" class="table table-bordered text-center">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col" id="idclass">#</th>
                                                            <th scope="col">Name</th>
                                                            <th scope="col">Title</th>
                                                            <th scope="col">Email</th>
                                                            <th scope="col">Office Phone</th>
                                                            <th scope="col">Cell Phone</th>
                                                            <th scope="col">Contact Notes</th>
                                                            <th scope="col">Primary</th>
                                                            <th scope="col" width="15%">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <!-- DATATABLE Here -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_product_management" role="tabpanel" area-labelledby="tab_product_management">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card o-hidden mb-4">
                                        <div class="card-header">
                                            <h3 class="w-25 float-left card-title m-0">Product Listings</h3>
                                            <div class="float-right">
                                                <!-- <a href="{{ url('/uploaddata?supplier='.$row->name) }}" class="btn btn-primary btn-icon mr-2">Add/Update Supplier Product(s) From CSV</a> -->
                                                @if(ReadWriteAccess('AddNewParentProduct'))
                                                    <a href="{{ url('/addnewmasterproduct?supplier='.$row->name)}}" class="btn btn-primary btn-icon" style=" float: right;" target="_blank">
                                                        <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Add New Parent Product
                                                    </a>
                                                @endif
                                                <a href="javascript:void(0);" onClick="getModal('{{ route('suppliers.upload_bulk_product',$row->id) }}')" class="btn btn-primary btn-icon mr-2" style=" float: right;">
                                                    <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Bulk Upload
                                                </a>
                                                <a href="javascript:void(0);" onClick="getModal('{{ route('map_supplier_product_file',[$row->id]) }}')" class="btn btn-primary btn-icon mr-2" style=" float: right;">
                                                    <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Map File
                                                </a>
                                            </div>
                                            <div class="dropdown dropleft text-right w-50 float-right">
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="datatable" class="table table-bordered text-center dataTable_filter">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">ETIN</th>
                                                            <th scope="col">Brand</th>
                                                            <th scope="col">Supplier Status</th>
                                                            <th scope="col">Product</th>
                                                            <th scope="col">Item No</th>
                                                            <th scope="col">Warehouse(s) Assigned</th>
                                                            <th scope="col">Cost</th>
                                                            <th scope="col">e-tailer Status</th>
                                                            <th scope="col">Action</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                    <!-- DATATABLE Here -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_warehouse_orders" role="tabpanel" area-labelledby="warehouse_orders_tab">
                            <div class="row">
                                <table class="table table-bordered" id="purchase_summary">
                                    <thead class="bg-gray-800 text-white">
                                        <tr>
                                            <th>Warehouse</th>
                                            <th>Order Number</th>
                                            <th>Invoice Number</th>
                                            <th>Order Date</th>
                                            <th>Delivery Date</th>
                                            <th>Status</th> 
                                            <th>Actions</th>                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($result)
                                            @foreach($result as $res)
                                                <tr>
                                                    <td>{{ $res['warehouse'] }}</td>
                                                    <td>{{ $res['order'] }}</td>
                                                    <td>{{ $res['invoice'] }}</td>
                                                    <td>{{ $res['order_date'] }}</td>
                                                    <td>{{ $res['delivery_date'] }}</td>
                                                    <td>{{ $res['po_status'] }}</td>
                                                    <td>
                                                        @if($res['po_status'] && ($res['po_status'] == 'Pending' || $res['po_status'] == 'Submitted'))
                                                            <a href="{{ url('/purchase_order/edit/' . $row->id . '/' . $res['id'] . '/supplier') }}" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Edit">
                                                                <i class="nav-icon i-Pen-2 "></i>
                                                            </a>
                                                        @endif
                                                        @if($res['report_path'])
                                                            <a href="{{ url('/' . $res['report_path']) }}" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Download Report">
                                                                <i class="nav-icon i-Down"></i>
                                                            </a>
                                                        @endif
                                                        @if($res['po_status'] && $res['po_status'] != 'Pending')
                                                            <a href="{{ url('/purchase_order/editasnbol/' . $row->id . '/' . $res['id'] . '/supplier') }}" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Submit ASN/BOL">
                                                                <i class="nav-icon">Submit ASN/BOL</i>
                                                            </a> 

                                                            
                                                            @if(isset($row->exp_lot) && $row->exp_lot != 'NO')
                                                                <a href="javascript:void(0)" onClick="getModal('{{ url('/purchase_order/' . $res['order'] . '/get_lot_and_exp') }}')" class="btn btn-primary mt-2"  data-toggle="tooltip" data-placement="top" title="Submit ASN/BOL">
                                                                    <i class="nav-icon">Submit Lot & Exp. #\'s</i>
                                                                </a>
                                                            @endif
                                                        @endif

                                                        
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">                                
                                <div class="col-md-12 text-left">
                                    <a href="{{ route('purchase_order.create_purchase_order', ['id' => $row->id, 'type' => 'supplier']) }}" class="btn btn-primary">New Purchase Order</a>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_dropship_orders" role="tabpanel" area-labelledby="dropship_orders_tab">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_packaging" role="tabpanel" area-labelledby="packaging_tab">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card o-hidden mb-4">
                                        <div class="card-header">
                                            <h3 class="w-25 float-left card-title m-0">Packaging & Materials</h3>
                                            <div class="float-right">
                                                <a href="{{ route('packagekitcreate',[$row->id]) }}" class="btn btn-primary btn-icon mr-2" style=" float: right;">
                                                    <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Create New Kits for Packageing
                                                </a>
                                                <a href="{{ route('addpackagematerial',[$row->id]) }}" class="btn btn-primary btn-icon mr-2" style=" float: right;">
                                                    <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Add Package & material
                                                </a>
                                                <a href="javascript:void(0);" onClick="openBulkModal()" class="btn btn-primary btn-icon mr-2" style=" float: right;">
                                                    <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Bulk Upload
                                                </a>
                                                <a href="javascript:void(0);" onClick="openModal()" class="btn btn-primary btn-icon mr-2" style=" float: right;">
                                                    <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Map File
                                                </a>
                                            </div>
                                            <div class="dropdown dropleft text-right w-50 float-right">
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="packaging" class="table table-bordered text-center dataTable_filter">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">ETIN</th>
                                                            <th scope="col">Product Description</th>
                                                            <th scope="col">Quantity Per Bundle</th>
                                                            <th scope="col">Product Temperature</th>
                                                            <th scope="col">Supplier Product Number</th>
                                                            <th scope="col">Cost</th>
                                                            <th scope="col">Status</th>
                                                            <th scope="col">Action</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                    <!-- DATATABLE Here -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_supplier_service" role="tabpanel" area-labelledby="supplier_service_tab">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card o-hidden mb-4">
                                        <div class="card-header">
                                            <h3 class="w-50 float-left card-title m-0">Account Notes</h3>
                                            <div class="separator-breadcrumb">
                                                <a href="javascript:void(0);" onclick="getModal('{{ route('suppliers.createNote',$row->id) }}')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                    <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; New Note
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="notes" class="table table-bordered text-center">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col" id="idclass">#</th>
                                                            <th scope="col">Event</th>
                                                            <th scope="col">Details</th>
                                                            <th scope="col">User</th>
                                                            <th scope="col">Date & Time</th>
                                                            <th scope="col">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <!-- DATATABLE Here -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_tbd" role="tabpanel" area-labelledby="tbd_tab">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_documents" role="tabpanel" area-labelledby="documents_tab">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card o-hidden mb-4">
                                        <div class="card-header">
                                            <h3 class="w-50 float-left card-title m-0">Documents</h3>
                                            <div class="separator-breadcrumb">
                                                <a href="javascript:void(0);" onclick="getModal('{{ route('suppliers.createDocument',$row->id) }}')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                    <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Add New Document
                                                </a>
                                            </div>
                                            <div class="dropdown dropleft text-right w-50 float-right">
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="documents" class="table table-bordered text-center">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col" id="idclass">#</th>
                                                            <th scope="col">Type</th>
                                                            <th scope="col">Name</th>
                                                            <th scope="col">Description</th>
                                                            <th scope="col">Date</th>
                                                            <th scope="col">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <!-- DATATABLE Here -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card o-hidden mb-4">
                                        <div class="card-header">
                                            <h3 class="w-50 float-left card-title m-0">Links</h3>
                                            <div class="separator-breadcrumb">
                                                <a href="javascript:void(0);" onclick="getModal('{{ route('suppliers.createLink',$row->id) }}')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                    <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Add New
                                                </a>
                                            </div>
                                            <div class="dropdown dropleft text-right w-50 float-right">
                                            </div>

                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="links" class="table table-bordered text-center">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col" id="idclass">#</th>
                                                            <th scope="col">URL</th>
                                                            <th scope="col">Name</th>
                                                            <th scope="col">Description</th>
                                                            <th scope="col">Date</th>
                                                            <th scope="col">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <!-- DATATABLE Here -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_reports" role="tabpanel" area-labelledby="reports_tab">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- tab close -->
            </div>
        </div>
    </div>
    <!-- end of col -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"></div>
    <div class="modal fade" id="packaging_modal" tabindex="-1" role="dialog" aria-labelledby="packaging_modal" aria-hidden="true">
        <div class="modal-dialog mt-5">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="createEventModalLabel">Map the file</h5>
                    <button type="button" class="close" onclick="closemodal('map_file')" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($row->csvHeader)
                        <table class="table table-border">
                            <tr>
                                <th>Supplier Table Data</th>
                                <th>Your Data</th>
                            </tr>
                        @foreach(json_decode($row->csvHeader->map_data) as $key => $val)
                            <tr>
                                <td>{{ $key }}</td>
                                <td>{{ $val }}</td>
                            </tr>
                        @endforeach
                        </table>
                        <a href="{{ route('suppliers.delete_supplier_product_header',$row->csvHeader->id) }}" class="btn btn-danger" onClick="return confirm('are you sure?')">Delete Mapping</a>
                    @else
                    <div id="form_csv">
                        <form data-form='map_file' class="form-horizontal" method="POST" action="" enctype="multipart/form-data" id="form_upload">
                            {{ csrf_field() }}
                            <input type="hidden" name="supplier_id" value="{{ $row->id }}">
                            <div class="form-group">
                                <label for="csv_file" class="col-md-4 control-label">CSV file to map</label>
                                <div class="col-md-12">
                                    <input id="csv_file" type="file" class="form-control" name="csv_file" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Map File
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endif
                    <div id="return_container"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="packaging_modal_bulk_upload" tabindex="-1" role="dialog" aria-labelledby="packaging_modal" aria-hidden="true">
        <div class="modal-dialog mt-5">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="createEventModalLabel">Insert CSV Data in TABLES</h5>
                    <button type="button" class="close" onclick="closemodal('bulk')" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if(!$row->csvHeader)
                        <p class="p-3 bg-danger text-white">Please map your file first</p>
                    @else
                        <form data-form='bulk_upload' class="form-horizontal" method="POST" enctype="multipart/form-data" id="form_upload">
                        {{ csrf_field() }}
                            <input type="hidden" name="supplier_name" value="{{$row->csvHeader->map_type}}" id="supplier_name">
                            <input type="hidden" name="supplier_id" value="{{ $row->id }}">
                            <div class="form-group{{ $errors->has('csv_file') ? ' has-error' : '' }}">
                                <label for="csv_file" class="col-md-4 control-label">CSV file to import</label>
                                <div class="col-md-12">
                                    <input id="csv_file" type="file" class="form-control" name="csv_file" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Insert Data
                                    </button>
                                </div>
                            </div>
                        </form>
                        <table class="table table-border">
                            <tr>
                                <th>Table Data</th>
                                <th>Your Data</th>
                            </tr>
                            
                           @foreach(json_decode($row->csvHeader->map_data) as $key => $val)
                                <tr>
                                    <td>{{ $key }}</td>
                                    <td>{{ $val }}</td>
                                </tr>
                            @endforeach
                        </table>
                        <a href="{{ route('clients.dalete_client_product_header',$row->csvHeader->id) }}" class="btn btn-danger" onClick="return confirm('are you sure?')">Delete Mapping</a>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>
    <script src="{{asset('assets/js/sweetalert.script.js')}}"></script>
    <script src="{{ asset('assets/js/validation/jquery.validate.js') }}"></script>
    <script src="{{ asset('assets/js/validation/additional-methods.min.js') }}"></script>
    <script>
        
        $(document).ready(function () {
            $('#cuttoff_time').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
            });
            // purchaseSummaryList();
            $('#purchase_summary').DataTable();
            @if($row->supplier_product_package_type == 'Product')
                GetProducts();
            @elseif($row->supplier_product_package_type == 'Package')
                getSupplierPackaging();
            @endif
        });

        $("#update_supplier").on('submit',function(e){
            e.preventDefault();
            $(".submit").attr("disabled", true);
            var form_cust = $('#update_supplier')[0];
            let form1 = new FormData(form_cust);
            $.ajax({
                type: "POST",
                url: '{{ route('suppliers.update',$row->id) }}',
                data: form1,
                processData: false,
                contentType: false,
                success: function( response ) {
                    $(".submit").attr("disabled", false);
                    if(response.error == false){
                        toastr.success(response.msg);
                        setTimeout(function(){
                            location.href= response.url;
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
        })

        $('#all_suppliers').DataTable();

        $("#orderScheduleForm").on('submit',function(e){
            e.preventDefault();
            $(".submit").attr("disabled", true);
            var form_cust = $('#orderScheduleForm')[0];
            let form1 = new FormData(form_cust);
            $.ajax({
                type: "POST",
                url: '{{ route('suppliers.updateSupplierConfig',$row->id) }}',
                data: form1,
                processData: false,
                contentType: false,
                success: function( response ) {
                    $(".submit").attr("disabled", false);
                    if(response.error == false){
                        toastr.success(response.msg);
                        setTimeout(function(){
                            location.href= response.url;
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

        })

        /*contactList();
        noteList();
        documentList();
        GetLinks();
        GetProducts();
        getSupplierPackaging();*/

        /*function purchaseSummaryList() {
            $('#purchase_summary').DataTable({
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                    url: '/datatable/purchaseSummary/' + {{ $row->id }},
                    method:'GET',                    
                },
                columns: [
                    {data: 'warehouse', name: 'warehouse', defaultContent:'-'},
                    {data: 'order', name: 'order', defaultContent:'-'},
                    {data: 'order_date', name: 'purchasing_asn_date', defaultContent:'-'},
                    {data: 'delivery_date', name: 'delivery_date', defaultContent:'-'},
                    {data: 'po_status', name: 'po_status', defaultContent:'-', editable: true}
                ]
            });
        }*/

        function contactList(){
            $('#contacts').DataTable({
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                        url: '{{ route('suppliers.datatable.contactList',$row->id) }}',
                        method:'GET',
                    },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'title', name: 'title'},
                    {data: 'email', name: 'email'},
                    {data: 'office_phone', name: 'office_phone'},
                    {data: 'cell_phone', name: 'cell_phone'},
                    {data: 'contact_note', name: 'contact_note'},
                    {data: 'status', name: 'status',searchable: false},
                    {data: 'action', name: 'Action', orderable: false, className: 'action'},
                ],
                columnDefs: [
                    {
                        "targets": [ 0 ],
                        "visible": false,
                    },
                    {
                        "targets": [8],
                        "className": 'action'
                    }
                ],
                oLanguage: {
                    "sSearch": "Search:"
                },
            });
        }

        function noteList(){
            $('#notes').DataTable({
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                        url: '{{ route('suppliers.datatable.noteList',$row->id) }}',
                        method:'GET',
                    },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'event', name: 'event'},
                    {data: 'details', name: 'details'},
                    {data: 'user', name: 'user'},
                    {data: 'date', name: 'date'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                columnDefs: [
                    {
                        "targets": [ 0 ],
                        "visible": false
                    }
                ],
                oLanguage: {
                    "sSearch": "Search:"
                },
            });
        }

        function documentList(){
            $('#documents').DataTable({
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                        url: '{{ route('suppliers.datatable.documentList',$row->id) }}',
                        method:'GET',
                    },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'type', name: 'type'},
                    {data: 'name', name: 'name'},
                    {data: 'description', name: 'description'},
                    {data: 'date', name: 'date'},
                    {data: 'action', name: 'Action', orderable: false},
                ],
                columnDefs: [
                    {
                        "targets": [ 0 ],
                        "visible": false
                    }
                ],
                oLanguage: {
                    "sSearch": "Search:"
                },
            });
        }

        function GetLinks() {
            $('#links').DataTable({
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                        url: '{{ route('suppliers.datatable.linkList',$row->id) }}',
                        method:'GET',
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'url', name: 'type'},
                    {data: 'name', name: 'name'},
                    {data: 'description', name: 'description'},
                    {data: 'date', name: 'date'},
                    {data: 'action', name: 'Action', orderable: false},
                ],
                columnDefs: [
                    {
                        "targets": [ 0 ],
                        "visible": false
                    }
                ],
                oLanguage: {
                    "sSearch": "Search:"
                },
            });
        }

        function getModal(url){
            $.ajax({
                type:'GET',
                url:url,
                success:function(response){
                    $('#exampleModal').html(response);
                    $('#exampleModal').modal('show');
                }
            })
        }

        function setPrimaryContact(checkbox_obj,id) {
            if(checkbox_obj.checked) {
                confirm('Do You want to set as primary?');
            }
            else{
                confirm('Do You want to remove as primary?');
            }
            $.ajax({
                type:'GET',
                url:'{{ route('suppliers.setPrimaryContact') }}',
                data:{id:id},
                success:function(response){
                    toastr.success(response.msg)
                    contactList();
                },
                error:function(response){
                    toastr.error("Something went wrong!")
                }
            })
        }

        function deleteContact(url){
            if(confirm('Are You Sure You Want To Delete This?')){
                $.ajax({
                    type:'GET',
                    url:url,
                    success:function(response){
                        toastr.success("Success")
                        contactList()
                    }
                })
            }
        }

        function deleteNote(url){
            if(confirm('Are You Sure You Want To Delete This?')){
                $.ajax({
                    type:'GET',
                    url:url,
                    success:function(response){
                        toastr.success("Success")
                        noteList()
                    }
                })
            }
        }

        function deleteDocument(url){
            if(confirm('Are You Sure You Want To Delete This?')){
                $.ajax({
                    type:'GET',
                    url:url,
                    success:function(response){
                        toastr.success("Success")
                        documentList()
                    }
                })
            }
        }

        function deleteLink(url){
            if(confirm('Are You Sure You Want To Delete This?')){
                $.ajax({
                    type:'GET',
                    url:url,
                    success:function(response){
                        toastr.success("Success")
                        GetLinks()
                    }
                })
            }
        }

        function GetProducts() {
            var table = $('#datatable').DataTable({
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                        url: '{{ route('getmasterproductsbysupplier',$row->id) }}',
                        method:'GET',
                },
                columns: [
                    {data: 'ETIN', name: 'ETIN',defaultContent:'-'},
                    {data: 'brand', name: 'brand',defaultContent:'-'},
                    {data: 'supplier_status', name: 'supplier_status',defaultContent:'-'},
                    {data: 'item_description', name: 'item_description',defaultContent:'-'},
                    {data: 'item_number', name: 'item_number',defaultContent:'-'},
                    {data: 'warehouse_assigned', name: 'warehouse_assigned',defaultContent:'-'},
                    {data: 'cost', name: 'cost',defaultContent:'-'},
                    {data:'etailer_status', name: 'etailer_status',defaultContent:'-'},
                    {data: 'action', name: 'action',searchable:false},
                ],
                columnDefs: [
                    {
                        "targets": [],
                        "visible": false,
                        "data": "item_number",
                        "render": function ( data ) {
                        return '<a href="javascript:void(0)">Download</a>';
                        }
                    }
                ],
                oLanguage: {
                    "sSearch": "Search:"
                },
            });
        }

        function syncKeheWithMasterProduct(id){
            window.location="{{url('/')}}/syncKeheWithMasterProduct/"+id;
        }

        function resyncKeheWithMasterProduct(id) {
            if(confirm ('Item Already Sync with Master Table. Want to Re-Sync??')) {
                syncKeheWithMasterProduct(id);
            } else {
                return false;
            }
        }

        function syncdotproduct(id){
            window.location="{{url('/')}}/syncDotWithMasterProduct/"+id;
        }

        function resyncdotproduct(id){
            if(confirm ('Item Already Sync with Master Table. Want to Re-Sync??')){
                syncdotproduct(id);
            }
            else{
                return false;
            }
        }

        function syncDryersWithMasterProduct(id){
            window.location="{{url('/')}}/syncDryersWithMasterProduct/"+id;
        }

        function resyncDryersWithMasterProduct(id){
            if(confirm ('Item Already Sync with Master Table. Want to Re-Sync??')){
                syncDryersWithMasterProduct(id);
            } else
            {
                return false;
            }
        }

        function syncMarsWithMasterProduct(id) {
            // var table = null;
            // var table = $('#datatable').DataTable();
            // $('#datatable tbody').on( 'click', 'tr', function () {
            //     var row = table.row( this ).data();
            window.location="{{url('/')}}/syncMarsWithMasterProduct/"+id;
            // });
        }

        function resyncMarsWithMasterProduct(id) {
            if(confirm ('Item Already Sync with Master Table. Want to Re-Sync??')){
                syncMarsWithMasterProduct(id);
            } else
            {
                return false;
            }
        }

        function syncHarsheyWithMasterProduct(id){
            window.location="{{url('/')}}/syncHarsheyWithMasterProduct/"+id;
        }

        function resyncHarsheyWithMasterProduct(id){
            if(confirm ('Item Already Sync with Master Table. Want to Re-Sync??')) {
                syncHarsheyWithMasterProduct(id);
            } else {
                return false;
            }
        }

        openModal = () => {
            $('#packaging_modal').modal('show')
        }

        openBulkModal = () => {
            $('#packaging_modal_bulk_upload').modal('show')
        }

        getSupplierPackaging = () =>  {
            var table = $('#packaging').DataTable({
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                        url: '{{ route('getpackagingbysupplier',$row->id) }}',
                        method:'GET',
                        data:{'type':'packaging'}
                    },
                columns: [
                    {data: 'ETIN', name: 'ETIN',defaultContent:'-'},
                    {data: 'product_description', name: 'product_description',defaultContent:'-'},
                    {data: 'quantity_per_bundle', name: 'quantity_per_bundle',defaultContent:'-'},
                    {data: 'product_temperature', name: 'product_temperature',defaultContent:'-'},
                    {data: 'supplier_product_number', name: 'supplier_product_number',defaultContent:'-'},
                    {data: 'cost', name: 'cost',defaultContent:'-'},
                    {data:'status', name: 'status',defaultContent:'-'},
                    {data: 'action', name: 'action',searchable:false},
                ],
                columnDefs: [
                    {
                        "targets": [],
                        "visible": false,
                        "data": "item_number",
                        "render": function ( data ) {
                            return '<a href="javascript:void(0)">Download</a>';
                        }
                    }
                ],
                oLanguage: {
                    "sSearch": "Search:"
                },
            });
        }

        $("#form_upload").validate({
            submitHandler(form) {
                var formtype = $(form).attr('data-form');
                $(".submit").attr("disabled", true);
                $("#preloader").show();
                var form_cust = $('#form_upload')[0];
                let form1 = new FormData(form_cust);
                var url = '{{ route('suppliers.upload_supplier_product') }}';
                if(formtype == 'map_file') {
                    url = '{{ route('suppliers.MapSupplierProduct') }}'
                }
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form1,
                    processData: false,
                    contentType: false,
                    success: function( response ) {
                        if(response.error == 0){
                            $("#preloader").hide();
                            if(formtype == 'map_file')
                            {
                                toastr.success(response.msg);
                                $("#return_container").html(response.result);
                                $("#form_csv").addClass('d-none');
                            }
                            else
                            {
                                location.reload()
                                swal(
                                    'Success',
                                    response.msg,
                                    'success'
                                ).then(function(){});
                            }
                        }else{
                            $(".submit").attr("disabled", false);
                            if(formtype == 'map_file')
                            {
                                toastr.error(response.msg);
                                $("#preloader").hide();
                            }
                            else{
                                $("#preloader").hide();
                                swal(
                                    'Error',
                                    response.msg,
                                    'warning'
                                ).then(function(){
                                    location.reload(true);
                                });
                            }
                        }
                    },
                    error: function(data){
                        $("#preloader").hide();
                        $(".submit").attr("disabled", false);
                        var errors = data.responseJSON;
                        $("#error_container").html('');
                        $.each( errors.errors, function( key, value ) {
                            var ele = "#"+key;
                            $(ele).addClass('error_border');
                            $('<label class="error">'+ value +'</label>').insertAfter(ele);
                            $("#error_container").append("<p class='bg-danger mb-1 text-white p-1'>"+ value +"</p>");
                            toastr.error(value);
                        });
                    }
                })
                return false;
            }
        });

        closemodal = (type) =>{
            if(type == 'map_file') {
                $("#form_csv").removeClass('d-none');
                $("#return_container").html('');
            }
            $("#csv_file").val('');   
        }
        
        deletePackagingMaterial = (id) => {
            swal({
                title: 'Are you sure?',
                text: "This information will be permanently deleted!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then(function(result) {
                if(result) {
                    $.ajax({
                        type: "Delete",
                        url: '{{ route('destroypackagematerial') }}',
                        data: {'id':id},
                        success: function( response ) {
                             $("#preloader").hide();
                            if(response.result){
                                toastr.success('Deleted Successfuly');
                                location.reload()
                            }else{
                                toastr.error("Already in use as component can't delete");
                            }
                        }
                    })
                }
            });
        }

    </script>


@endsection
