@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
    @php $required_span = '<span class="text-danger">*</span>' @endphp
    <div class="breadcrumb">
        <h1>Suppliers</h1>
        <ul>
            <li><a href="">Suppliers</a></li>
            <li>New</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">New Supplier</h6>
                </div>
                <form action="javascrpt:void(0)" id="new_supplier">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="name" class="ul-form__label">Supplier Name:<?php echo $required_span; ?></label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Supplier Name">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="main_point_of_contact" class="ul-form__label">Main Point of Contact:<?php echo $required_span; ?></label>
                                        <input type="text" class="form-control" id="main_point_of_contact" name="main_point_of_contact" placeholder="main Point of Contact">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="address" class="ul-form__label">Supplier Address:<?php echo $required_span; ?></label>
                                        <input type="text" class="form-control" id="address" name="address" placeholder="Address">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="address2" class="ul-form__label">Supplier Address 2:<?php echo $required_span; ?></label>
                                        <input type="text" class="form-control" id="address2" name="address2" placeholder="Address 2">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="address2" class="ul-form__label">Supplier City:</label>
                                        <input type="text" class="form-control" id="city" name="city" placeholder="City">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="address2" class="ul-form__label">Supplier State:</label>
                                        <input type="text" class="form-control" id="state" name="state" placeholder="State">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="address2" class="ul-form__label">Supplier Zip:</label>
                                        <input type="text" class="form-control" id="zip" name="zip" placeholder="Zip">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group col-md-12">
                                    <label for="phone" class="ul-form__label">Supplier Phone:<?php echo $required_span; ?></label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter Supplier Phone">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="email" class="ul-form__label">Supplier Email:<?php echo $required_span; ?></label>
                                    <input type="text" class="form-control" id="email" name="email" placeholder="Enter Supplier Email">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="website" class="ul-form__label">Supplier Website:</label>
                                    <input type="text" class="form-control" id="website" name="website" placeholder="Enter Supplier Website">
                                </div>
                                 <div class="form-group col-md-12">
                                    <label for="supplier_product_package_type" class="ul-form__label">Supplier Package Type:<?php echo $required_span; ?></label>
                                    <select class="form-control select2" id="supplier_product_package_type" name="supplier_product_package_type">
                                        <option value="">--Select--</option>
                                        @foreach($productPackageType as  $val)
                                            <option value="{{ $val }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group col-md-12">
                                    <label for="status" class="ul-form__label">Status:</label>
                                    <select class="form-control" id="status" name="status" >
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                        <option value="Secondary">Secondary</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="description" class="ul-form__label">Supplier Description:</label>
                                    <textarea name="description" id="description" cols="10" rows="3" class="form-control" placeholder="Enter Supplier Description"></textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="next_order_date" class="ul-form__label">Next Order Date:</label>
                                    <input type="date" class="form-control" id="next_order_date" name="next_order_date">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="time_zone_id" class="ul-form__label">Time Zone:</label>
                                    <select class="form-control select2" id="time_zone_id" name="time_zone_id">
                                        <option value="">--Select--</option>
                                        @if($time_zones)
                                            @foreach($time_zones as $id => $val)
                                                <option value="{{ $id }}">{{ $val }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="account_manager" class="ul-form__label">Account Manager:</label>
                                     <select class="form-control select2" id="account_manager" name="account_manager">
                                        <option value="">--Select--</option>
                                        @if($managers)
                                            @foreach($managers as $id => $val)
                                                <option value="{{ $id }}">{{ $val }}</option>
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
                                            <option value="{{ $id }}">{{ $val }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="warehouses_assigned" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Warehouse(s) stocking this product">Warehouse(s) Assigned <span class="text-danger">*</span></label>
                                    <table class="table table-bordered" id="warehouses_assigned">
                                        <tr>
                                            <th></th>
                                            <th>Stocked</th>
                                        </tr>
                                        @if ($warehouse)
                                            @foreach($warehouse as $warehouses)
                                                <tr>
                                                    <th width="50%" class="text-left"><label for="{{ $warehouses }}">{{ $warehouses }}</label></th>
                                                    <td width="50%" class="text-center"><input type="checkbox" name="warehouses_assigned[]" id="{{ $warehouses }}" value="{{ $warehouses }}"></td>
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
                        <li class="nav-item">
                            <a class="nav-link" href="#tab_product_management" id="product_management_tab" role="tab" aria-controls="product_management_tab" area-selected="false" data-toggle="tab">Product
                                Management</a>
                        </li>
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
                            <div class="row mt-4 p-5">
                                <div class="col-md-12">
                                    <p class="text-center">Please Fill & Submit Above Details First</p>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab_contacts" role="tabpanel" area-labelledby="contacts_tab">
                            <div class="row mt-4 p-5">
                                <div class="col-md-12">
                                    <p class="text-center">Please Fill & Submit Above Details First</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_product_management" role="tabpanel" area-labelledby="tab_product_management">
                            <div class="row mt-4 p-5">
                                <div class="col-md-12">
                                    <p class="text-center">Please Fill & Submit Above Details First</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_warehouse_orders" role="tabpanel" area-labelledby="warehouse_orders_tab">
                            <div class="row mt-4 p-5">
                                <div class="col-md-12">
                                    <p class="text-center">Please Fill & Submit Above Details First</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_dropship_orders" role="tabpanel" area-labelledby="dropship_orders_tab">
                            <div class="row mt-4 p-5">
                                <div class="col-md-12">
                                    <p class="text-center">Please Fill & Submit Above Details First</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_supplier_service" role="tabpanel" area-labelledby="supplier_service_tab">
                            <div class="row mt-4 p-5">
                                <div class="col-md-12">
                                    <p class="text-center">Please Fill & Submit Above Details First</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_tbd" role="tabpanel" area-labelledby="tbd_tab">
                            <div class="row mt-4 p-5">
                                <div class="col-md-12">
                                    <p class="text-center">Please Fill & Submit Above Details First</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_documents" role="tabpanel" area-labelledby="documents_tab">
                            <div class="row mt-4 p-5">
                                <div class="col-md-12">
                                    <p class="text-center">Please Fill & Submit Above Details First</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_reports" role="tabpanel" area-labelledby="reports_tab">
                            <div class="row mt-4 p-5">
                                <div class="col-md-12">
                                    <p class="text-center">Please Fill & Submit Above Details First</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- tab close -->
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
                    url: '{{ route('suppliers.store') }}',
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
       });
   </script>
@endsection
