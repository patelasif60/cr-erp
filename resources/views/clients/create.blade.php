@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
    @php $required_span = '<span class="text-danger">*</span>' @endphp
    <div class="breadcrumb">
        <h1>Clients</h1>
        <ul>
            <li><a href="">Clients</a></li>
            <li>New</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">New Client</h6>
                </div>
                <form action="#" id="new_client">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-row ">
                                    <div class="form-group col-md-12">
                                        <label for="company_name" class="ul-form__label">Client Company Name:<?php echo $required_span; ?></label>
                                        <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Enter Client Company Name">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="status" class="ul-form__label">Status:</label>
                                        <select class="form-control" id="status" name="status" >
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                            <option value="Secondary">Secondary</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="business_relationship" class="ul-form__label">Business Relationship:</label>
                                        <select class="form-control" id="business_relationship" name="business_relationship" >
                                            <option value="Fulfillment">Fulfillment</option>
                                            <option value="Wholesale">Wholesale</option>
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
                                    <div class="form-group col-md-12">
                                        <label for="active_status" class="ul-form__label">Active Status:</label>
                                        <select class="form-control select2" id="active_status" name="active_status" required>
                                            <option value="1">Active</option>                                            
                                            <option value="2">On Hold</option>                                            
                                            <option value="3">Disconnected</option>                                         
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
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
                                    <label for="time_zone_id" class="ul-form__label">Address:</label>
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address">
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="time_zone_id" class="ul-form__label">Address 2:</label>
                                    <input type="text" class="form-control" id="address2" name="address2" placeholder="Enter Address 2">
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="time_zone_id" class="ul-form__label">Zip:</label>
                                    <input type="text" class="form-control" id="zip" name="zip" placeholder="Enter Zip">
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="time_zone_id" class="ul-form__label">City:</label>
                                    <input type="text" class="form-control" id="city" name="city" placeholder="Enter City">
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="time_zone_id" class="ul-form__label">State:</label>
                                    <input type="text" class="form-control" id="state" name="state" placeholder="Enter state">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="mc-footer">
                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn  btn-primary m-1">Submit</button>
                                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary m-1">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="col-md-12 mt-4">
                    <ul class="nav nav-tabs nav-justified">
                        <li class="nav-item">
                            <a class="nav-link active" href="#tab_product_management" id="product_management_tab" role="tab" aria-controls="product_management_tab" area-selected="true" data-toggle="tab">Product Management</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_client_config" id="client_config_tab" role="tab" aria-controls="client_config_tab" area-selected="false" data-toggle="tab">Client
                                Configuration</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_account_management" id="account_management_tab" role="tab" aria-controls="account_management_tab" area-selected="false" data-toggle="tab">Account
                                Management</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_contacts" id="contacts_tab" role="tab" aria-controls="contacts_tab" area-selected="false" data-toggle="tab">Contacts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_orders" id="orders_tab" role="tab" aria-controls="orders_tab" area-selected="false" data-toggle="tab">Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_documents" id="documents_tab" role="tab" aria-controls="documents_tab" area-selected="false" data-toggle="tab">Documents</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_reports" id="reports_tab" role="tab" aria-controls="reports_tab" area-selected="false" data-toggle="tab">Reports</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab_product_management" role="tabpanel" area-labelledby="product_management_tab">
                            <div class="row mt-4 p-5">
                                <div class="col-md-12">
                                    <p class="text-center">Please Fill & Submit Above Details First</p>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab_client_config" role="tabpanel" area-labelledby="client_config_tab">
                            <div class="row mt-4 p-5">
                                <div class="col-md-12">
                                    <p class="text-center">Please Fill & Submit Above Details First</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_account_management" role="tabpanel" area-labelledby="account_management_tab">
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
                        <div class="tab-pane fade" id="tab_orders" role="tabpanel" area-labelledby="orders_tab">
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
            $("#new_client").on('submit',function(e){
                e.preventDefault();
                $(".submit").attr("disabled", true);
                var form_cust = $('#new_client')[0];
                let form1 = new FormData(form_cust);
                $.ajax({
                    type: "POST",
                    url: '{{ route('clients.store') }}',
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
