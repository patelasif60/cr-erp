@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
    @php $required_span = '<span class="text-danger">*</span>' @endphp
    <div class="breadcrumb">
        <h1>Carriers</h1>
        <ul>
            <li>Carrier</li>
            <li>New</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">New Carrier</h6>
                </div>
                <form action="javascrpt:void(0)" id="new_carrier">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-row ">
                                    <div class="form-group col-md-12">
                                        <label for="company_name" class="ul-form__label">Company Name:<?php echo $required_span; ?></label>
                                        <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Enter Client Company Name">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="main_point_of_contact" class="ul-form__label">Main Point of Contact:{!! $required_span !!}</label>
                                        <input type="text" class="form-control" id="main_point_of_contact" name="main_point_of_contact" placeholder="Main Point of Contact">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="client_address" class="ul-form__label">Address:{!! $required_span !!}</label>
                                        <input type="text" class="form-control" id="client_address" name="client_address" placeholder="Client Address">

                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="client_address2" class="ul-form__label">Address2:{!! $required_span !!}</label>
                                        <input type="text" class="form-control" id="client_address2" name="client_address2" placeholder="Client Address2">

                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="client_city" class="ul-form__label">City:</label>
                                        <input type="text" class="form-control" id="client_city" name="client_city" placeholder="Client City">

                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="client_state" class="ul-form__label">State:</label>
                                        <input type="text" class="form-control" id="client_state" name="client_state" placeholder="Client State">

                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="client_zip" class="ul-form__label">Zip:</label>
                                        <input type="text" class="form-control" id="client_zip" name="client_zip" placeholder="Client Zip">

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12">
                                    <label for="client_phone" class="ul-form__label">Phone:{!! $required_span !!}</label>
                                    <input class="form-control" id="client_phone" name="client_phone">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="client_email" class="ul-form__label">Email:{!! $required_span !!}</label>
                                    <input class="form-control" id="client_email" name="client_email">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="client_website" class="ul-form__label">Website:</label>
                                    <input class="form-control" id="client_website" name="client_website">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12">
                                    <label for="client_status" class="ul-form__label">Status:</label>
                                        <select class="form-control" id="client_status" name="client_status" >
                                            <option value="Active" selected>Active</option>
                                            <option value="Inactive">Inactive</option>
                                            <option value="Secondary">Secondary</option>
                                        </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" class="btn  btn-primary m-1">Submit</button>
                                <a href="{{ route('carriers.index') }}" class="btn btn-outline-secondary m-1">Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="col-md-12 mt-4">
                    <ul class="nav nav-tabs nav-justified">
                        <li class="nav-item">
                            <a class="nav-link active" href="#tab_supplier_configuation" id="supplier_configuation_tab" role="tab" aria-controls="supplier_configuation_tab" area-selected="true" data-toggle="tab">Carrier Configuration</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_contacts" id="contacts_tab" role="tab" aria-controls="contacts_tab" area-selected="false" data-toggle="tab">Shipments</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tab_product_management" id="product_management_tab" role="tab" aria-controls="product_management_tab" area-selected="false" data-toggle="tab">Contacts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_warehouse_orders" id="warehouse_orders_tab" role="tab" aria-controls="warehouse_orders_tab" area-selected="false" data-toggle="tab">Carrier Service</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_dropship_orders" id="dropship_orders_tab" role="tab" aria-controls="dropship_orders_tab" area-selected="false" data-toggle="tab">Packages in Peril</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_supplier_service" id="supplier_service_tab" role="tab" aria-controls="supplier_service_tab" area-selected="false" data-toggle="tab">Data Analytics</a>
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
            $("#new_carrier").on('submit',function(e){
                e.preventDefault();
                $(".submit").attr("disabled", true);
                var form_cust = $('#new_carrier')[0];
                let form1 = new FormData(form_cust);
                $.ajax({
                    type: "POST",
                    url: '{{ route('carriers.store') }}',
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
