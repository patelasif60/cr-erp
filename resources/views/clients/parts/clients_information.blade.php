@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
    <style>
        .flatpickr-wrapper{
            width:100%;
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
        .action{
            padding-top: 17px;
            display: inline-flex;
        }
    </style>
@endsection

@section('main-content')
    @php $required_span = '<span class="text-danger">*</span>' @endphp
    <div class="breadcrumb">
        <h1>Clients</h1>
        <ul>
            <li><a href="{{ route('clients.index') }}">Clients</a></li>
            <li>Informations</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Edit Client</h6>
                </div>
                    <div class="card-body">
                        <form action="#" id="new_client">
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="id" value="{{ $row->id }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-row ">
                                        <div class="form-group col-md-12">
                                            <label for="company_name" class="ul-form__label">Client Company Name:<?php echo $required_span; ?></label>
                                            <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Enter Client Company Name" value="{{ $row->company_name }}">
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label for="status" class="ul-form__label">Status:</label>
                                            <select class="form-control" id="status" name="status" >
                                                <option value="Active" @if($row->status == 'Active') selected="selected" @endif>Active</option>
                                                <option value="Inactive" @if($row->status == 'Inactive') selected="selected" @endif>Inactive</option>
                                                <option value="Secondary" @if($row->status == 'Secondary') selected="selected" @endif>Secondary</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="business_relationship" class="ul-form__label">Business Relationship:</label>
                                            <select class="form-control" id="business_relationship" name="business_relationship" >
                                                <option value="Fulfillment" @if($row->business_relationship == 'Fulfillment') selected="selected" @endif>Fulfillment</option>
                                                <option value="Wholesale" @if($row->business_relationship == 'Wholesale') selected="selected" @endif>Wholesale</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group col-md-12">
                                            <label for="account_manager" class="ul-form__label">Account Manager:</label>
                                            <select class="form-control select2" id="account_manager" name="account_manager">
                                                <option value="">--Select--</option>
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
                                </div>
                                <div class="col-md-6">
                                    
                                    <div class="form-group col-md-12">
                                        <label for="time_zone_id" class="ul-form__label">Time Zone:</label>
                                        <select class="form-control select2" id="time_zone_id" name="time_zone_id">
                                            <option value="">--Select--</option>
                                            @if($time_zones)
                                            @foreach($time_zones as $id => $val)
                                                <option value="{{ $id }}"  @if($row->time_zone_id == $id) selected @endif>{{ $val }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="time_zone_id" class="ul-form__label">Address:</label>
                                        <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address" value="{{ $row->address }}">
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="time_zone_id" class="ul-form__label">Address 2:</label>
                                        <input type="text" class="form-control" id="address2" name="address2" placeholder="Enter Address 2" value="{{ $row->address2 }}">
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="time_zone_id" class="ul-form__label">Zip:</label>
                                        <input type="text" class="form-control" id="zip" name="zip" placeholder="Enter Zip" value="{{ $row->zip }}">
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="time_zone_id" class="ul-form__label">City:</label>
                                        <input type="text" class="form-control" id="city" name="city" placeholder="Enter City" value="{{ $row->city }}">
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="time_zone_id" class="ul-form__label">State:</label>
                                        <input type="text" class="form-control" id="state" name="state" placeholder="Enter state" value="{{ $row->state }}">
                                    </div>
                                    
                                    @if(count($priceGroup)>0)
                                    <div class="form-group col-md-12">
                                        <label for="sales_manager" class="ul-form__label">Price Group:</label>
                                        <select class="form-control select2" onchange="pricegroupChange()" id="price_group" name="price_group">
                                            @foreach($priceGroup as $key => $val)
                                                <option value="{{ $val->id }}">{{ $val->group_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="row" style="margin-top:30px;">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn  btn-primary m-1">Submit</button>
                                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary m-1">Cancel</a>
                                </div>
                            </div>
                        </form>
                        

                    </div>

            </div>
        </div>
    </div>
    <!-- end of col -->

@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>
<script src="{{asset('assets/js/sweetalert.script.js')}}"></script>
    <script>
    


    $("#new_client").on('submit',function(e){
        e.preventDefault();
        $(".submit").attr("disabled", true);
        var form_cust = $('#new_client')[0];
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '{{ route('clients.update',$row->id) }}',
            data: form1,
            processData: false,
            contentType: false,
            success: function( response ) {
                $(".submit").attr("disabled", false);
                if(response.error == false){
                    toastr.success(response.msg);
                    setTimeout(function(){
                        // location.href= response.url;
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

    })




   </script>
@endsection
