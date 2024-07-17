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
        /* #package_surcharge>table>tr>td{
            display: flex;
        } */
        .action{
            /* display: flex; */
            /* padding-top: 17px; */
            /* display: inline-flex; */
            /* display: table-cell; */
        }
        td.action{
            display: flex;
        }
        /* th {
            text-align: center;
            padding: 15px 0px 15px 0px;
        } */
    </style>
@endsection

@section('main-content')
    @php $required_span = '<span class="text-danger">*</span>' @endphp
    <div class="breadcrumb">
        <h1>Carriers</h1>
        <ul>
            <li><a href="{{ route('carriers.index') }}">Carriers</a></li>
            <li>View</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Edit Carrier</h6>
                </div>
                    <div class="card-body">
                        <form action="#" id="new_client">
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="id" value="{{ $row->id }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-row ">
                                        <div class="form-group col-md-12">
                                            <label for="company_name" class="ul-form__label">Company Name:<?php echo $required_span; ?></label>
                                            <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Enter Client Company Name" value="{{ $row->company_name }}">
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="main_point_of_contact" class="ul-form__label">Main Point of Contact:{!! $required_span !!}</label>
                                            <input type="text" class="form-control" id="main_point_of_contact" name="main_point_of_contact" placeholder="Main Point of Contact" value="{{ $row->main_point_of_contact }}">
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="client_address" class="ul-form__label">Address:{!! $required_span !!}</label>
                                            <input type="text" class="form-control" id="client_address" name="client_address" placeholder="Client Address" value="{{ $row->client_address }}">

                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="client_address2" class="ul-form__label">Address2:{!! $required_span !!}</label>
                                            <input type="text" class="form-control" id="client_address2" name="client_address2" placeholder="Client Address2" value="{{ $row->client_address2 }}">

                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="client_city" class="ul-form__label">City:</label>
                                            <input type="text" class="form-control" id="client_city" name="client_city" placeholder="Client City" value="{{ $row->client_city }}">

                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="client_state" class="ul-form__label">State:</label>
                                            <input type="text" class="form-control" id="client_state" name="client_state" placeholder="Client State" value="{{ $row->client_state }}">

                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="client_zip" class="ul-form__label">Zip:</label>
                                            <input type="text" class="form-control" id="client_zip" name="client_zip" placeholder="Client Zip" value="{{ $row->client_zip }}">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group col-md-12">
                                        <label for="client_phone" class="ul-form__label">Phone:{!! $required_span !!}</label>
                                        <input class="form-control" id="client_phone" name="client_phone" value="{{ $row->client_phone }}">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="client_email" class="ul-form__label">Email:{!! $required_span !!}</label>
                                        <input class="form-control" id="client_email" name="client_email" value="{{ $row->client_email }}">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="client_website" class="ul-form__label">Website:</label>
                                        <input class="form-control" id="client_website" name="client_website" value="{{ $row->client_website }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="client_status" class="ul-form__label">Status:</label>
                                        <select class="form-control" id="client_status" name="client_status" >
                                            <option value="Active" @if($row->client_status == "Active")
                                            selected
                                            @endif>Active</option>
                                            <option value="Inactive" @if($row->client_status == "Inactive")
                                                selected
                                                @endif>Inactive</option>
                                            <option value="Secondary" @if($row->client_status == "Secondary")
                                                selected
                                                @endif>Secondary</option>
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
                        </form>
                        <!-- tab start -->

                        <div class="col-md-12 mt-4">
                            <ul class="nav nav-tabs nav-justified">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#tab_product_management" id="product_management_tab" role="tab" aria-controls="product_management_tab" area-selected="true" data-toggle="tab">Carrier
                                        Configuration</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link " href="#tab_client_config" id="client_config_tab" role="tab" aria-controls="client_config_tab" area-selected="false" data-toggle="tab">Shipments</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link " href="#tab_account_management" id="account_management_tab" role="tab" aria-controls="account_management_tab" area-selected="false" data-toggle="tab">Contacts</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link " href="#tab_contacts" id="contacts_tab" role="tab" aria-controls="contacts_tab" area-selected="false" data-toggle="tab">Carrier Service</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link " href="#tab_orders" id="orders_tab" role="tab" aria-controls="orders_tab" area-selected="false" data-toggle="tab">Packages in Peril</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link " href="#tab_data_analytics" id="data_analytics_tab" role="tab" aria-controls="data_analytics_tab" area-selected="false" data-toggle="tab">Data Analytics</a>
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
                                    <div class="row">
                                        <div class="col-md-12 text-right mb-4">
                                            <label class="text-dark">Go To:</label>                                            
                                            @if (strtolower($row->company_name) === 'ups')
                                                <a href="{{ route('get_rates_listing','ups_zone_rates_by_ground') }}" class="btn btn-secondary"  target="_blank">Ground Rates by Zone</a>
                                                <a href="{{ route('get_rates_listing','ups_zone_rates_air') }}" class="btn btn-secondary"  target="_blank">Air Rates by Zone</a>
                                                <a href="{{ route('get_rates_listing','ups_das_zip') }}" class="btn btn-secondary" target="_blank">DAS Zip Codes</a>
                                            @elseif (strtolower($row->company_name) === 'fedex')
                                                <a href="{{ route('get_rates_listing','fedex_zone_rates_by_ground') }}" class="btn btn-secondary"  target="_blank">Ground Rates by Zone</a>
                                                <a href="{{ route('get_rates_listing','fedex_zone_rates_air') }}" class="btn btn-secondary"  target="_blank">Air Rates by Zone</a>
                                            @endif                                            
                                        </div>
                                    </div>
                                    <form action="javasript:void(0);" method="POST" id="updateConfig">
                                        @csrf
                                    <div class="row">
                                            <div class="col-md-6">
                                                <input type="hidden" name="carrier_id" value="{{ $row->id }}">
                                                <div class="card o-hidden mb-4">
                                                    <div class="card-header">
                                                        <h3 class="w-50 float-left card-title m-0">Standard Fees</h3>
                                                        <div class="separator-breadcrumb">
                                                            <a href="javascript:void(0);" onclick="enableDisable('standard')" class="btn btn-primary float-right">Edit</a>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <table class="table-responsive">
                                                            <tr>
                                                                <th>Dim Weight Divisor</th>
                                                                <th><input type="number" step="any" class="form-control standard" readonly name="dim_weight_divisor" @isset($sfee->dim_weight_divisor)
                                                                    value="{{ $sfee->dim_weight_divisor }}" @endisset >
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <th>Additional Handling</th>
                                                                <th>Zone 2 </th>
                                                                <th>Zone 3-4 </th>
                                                                <th>Zones 5+</th>
                                                            </tr>
                                                            <tr>
                                                                <td>Weight >50lbs</td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="weight_gt_50_lbs_1" @isset($sfee->weight_gt_50_lbs_1)
                                                                    value="{{ $sfee->weight_gt_50_lbs_1 }}"
                                                                @endisset ></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="weight_gt_50_lbs_2" @isset($sfee->weight_gt_50_lbs_2)
                                                                    value="{{ $sfee->weight_gt_50_lbs_2 }}"
                                                                @endisset ></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="weight_gt_50_lbs_3" @isset($sfee->weight_gt_50_lbs_3)
                                                                    value="{{ $sfee->weight_gt_50_lbs_3 }}"
                                                                @endisset ></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Length + Girth >105"</td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="length_girth_gt_105_in_1" @isset($sfee->length_girth_gt_105_in_1)
                                                                    value="{{ $sfee->length_girth_gt_105_in_1 }}"
                                                                @endisset ></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="length_girth_gt_105_in_2" @isset($sfee->length_girth_gt_105_in_2)
                                                                    value="{{ $sfee->length_girth_gt_105_in_2 }}"
                                                                @endisset ></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="length_girth_gt_105_in_3" @isset($sfee->length_girth_gt_105_in_3)
                                                                    value="{{ $sfee->length_girth_gt_105_in_3 }}"
                                                                @endisset ></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Length >48"</td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="length_gt_48_in_1" @isset($sfee->length_gt_48_in_1)
                                                                    value="{{ $sfee->length_gt_48_in_1 }}"
                                                                @endisset ></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="length_gt_48_in_2" @isset($sfee->length_gt_48_in_2)
                                                                    value="{{ $sfee->length_gt_48_in_2 }}"
                                                                @endisset ></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="length_gt_48_in_3" @isset($sfee->length_gt_48_in_3)
                                                                    value="{{ $sfee->length_gt_48_in_3 }}"
                                                                @endisset ></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Width >30"</td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="width_gt_30_in_1" @isset($sfee->width_gt_30_in_1)
                                                                    value="{{ $sfee->width_gt_30_in_1 }}"
                                                                @endisset ></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="width_gt_30_in_2" @isset($sfee->width_gt_30_in_2)
                                                                    value="{{ $sfee->width_gt_30_in_2 }}"
                                                                @endisset ></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="width_gt_30_in_3" @isset($sfee->width_gt_30_in_3)
                                                                    value="{{ $sfee->width_gt_30_in_3 }}"
                                                                @endisset ></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Packaging Exceptions</td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="packaging_exeptions_1" @isset($sfee->packaging_exeptions_1)
                                                                    value="{{ $sfee->packaging_exeptions_1 }}"
                                                                @endisset ></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="packaging_exeptions_2" @isset($sfee->packaging_exeptions_2)
                                                                    value="{{ $sfee->packaging_exeptions_2 }}"
                                                                @endisset ></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="packaging_exeptions_3" @isset($sfee->packaging_exeptions_3)
                                                                    value="{{ $sfee->packaging_exeptions_3 }}"
                                                                @endisset ></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="card o-hidden mb-4">
                                                    <div class="card-body">
                                                        <table class="table-responsive">
                                                            <tr>
                                                                <th>Large Package Surcharge</th>
                                                                <th>Zone 2</th>
                                                                <th>Zone 3-4</th>
                                                                <th>Zones 5+</th>
                                                            </tr>
                                                            <tr>
                                                                <td>Commercial</td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="commercial_1" @isset($sfee->commercial_1) value="{{ $sfee->commercial_1 }}" @endif></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="commercial_2" @isset($sfee->commercial_2) value="{{ $sfee->commercial_2 }}" @endif></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="commercial_3" @isset($sfee->commercial_3) value="{{ $sfee->commercial_3 }}" @endif></td>
                                                            </tr>
                                                            <tr>
                                                            <td>Residential</td>
                                                            <td>
                                                                <input type="number" step="any" class="form-control standard" readonly name="residential_1" @isset($sfee->residential_1) value="{{ $sfee->residential_1 }}" @endif>
                                                            </td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="residential_2" @isset($sfee->residential_2) value="{{ $sfee->residential_2 }}" @endif></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="residential_3" @isset($sfee->residential_3) value="{{ $sfee->residential_3 }}" @endif></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="card o-hidden mb-4">
                                                    <div class="card-body">
                                                        <table class="table-responsive">
                                                            <tr>
                                                                <th>Delivery Area Surcharge</th>
                                                                <th>Ground</th>
                                                                <th>Air</th>
                                                            </tr>
                                                            <tr>
                                                                <td>Commercial DAS</td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="commercial_ground" @isset($sfee->commercial_ground) value="{{ $sfee->commercial_ground  }}" @endisset></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="commercial_air" @isset($sfee->commercial_air) value="{{ $sfee->commercial_air }}" @endif></td>

                                                            </tr>
                                                            <tr>
                                                                <td>Residential DAS</td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="residential_ground" @isset($sfee->residential_ground) value="{{ $sfee->residential_ground }}"  @endif></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="residential_air" @isset($sfee->residential_air) value="{{ $sfee->residential_air }}" @endif></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Commercial Ext DAS</td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="commercial_extended_ground" @isset($sfee->commercial_extended_ground) value="{{ $sfee->commercial_extended_ground }}" @endif></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="commercial_extended_air" @isset($sfee->commercial_extended_air) value="{{ $sfee->commercial_extended_air }}" @endif></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Residential Ext DAS</td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="residential_extended_ground" @isset($sfee->residential_extended_ground) value="{{ $sfee->residential_extended_ground }}" @endif></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="residential_extended_air" @isset($sfee->residential_extended_air) value="{{ $sfee->residential_extended_air }}" @endif></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Residential Area Surcharge</td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="residential_surcharge_ground" @isset($sfee->residential_surcharge_ground) value="{{ $sfee->residential_surcharge_ground }}" @endif></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="residential_surcharge_air" @isset($sfee->residential_surcharge_air) value="{{ $sfee->residential_surcharge_air }}" @endif></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="card o-hidden mb-4">
                                                    <div class="card-body">
                                                        <table class="table-responsive">
                                                            <tr>
                                                                <th>Remote Area Surcharge</th>
                                                                <th>Ground</th>
                                                                <th>Air</th>

                                                            </tr>
                                                            <tr>
                                                                <td>Continental US</td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="continental_us_ground" @isset($sfee->continental_us_ground) value="{{ $sfee->continental_us_ground }}" @endif></td>
                                                                <td></td>

                                                            </tr>
                                                            <tr>
                                                                <td>Alaska</td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="alaska_ground" @isset($sfee->alaska_ground) value="{{ (float)$sfee->alaska_ground }}" @endif></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Hawaii</td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="hawaii_ground" @isset($sfee->hawaii_ground) value="{{ $sfee->hawaii_ground }}" @endif></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Dry Ice Surcharge</td>
                                                                <td><input type="text" step="any" class="form-control standard" readonly name="dry_ice_surcharge_ground" value="N/A"></td>
                                                                <td><input type="number" step="any" class="form-control standard" readonly name="dry_ice_surcharge_air" @isset($sfee->dry_ice_surcharge_air) value="{{ $sfee->dry_ice_surcharge_air }}" @endif></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card o-hidden mb-4">
                                                    <div class="card-header">
                                                        <h3 class="w-50 float-left card-title m-0">Fuel Surcharge</h3>
                                                        <div class="separator-breadcrumb">
                                                                <a href="javascript:void(0);" onclick="getModal('{{ route('carriers.createFee',$row->id) }}')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                                    <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Add New
                                                                </a>
                                                        </div>
                                                        <div class="dropdown dropleft text-right w-50 float-right">
                                                        </div>

                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table id="dynamic_fees" class="table table-bordered text-center">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col">Effective Date</th>
                                                                        <th scope="col">Ground(%)</th>
                                                                        <th scope="col">Domestic Air(%)</th>
                                                                        <th scope="col">International Air(%)</th>
                                                                        <th scope="col">Status</th>
                                                                        <th scope="col">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card o-hidden mb-4">
                                                    <div class="card-header">
                                                        <h3 class="w-50 float-left card-title m-0">Peak Surcharge</h3>
                                                        <div class="separator-breadcrumb">
                                                                <a href="javascript:void(0);" onclick="getModal('{{ route('carriers.createSurcharge',$row->id) }}')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                                    <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Peak Surcharge
                                                                </a>

                                                                {{-- <a href="javascript:void(0);" onclick="enableDisable('surcharge')" class="btn btn-primary btn-icon m-1 float-right">
                                                                    <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Peak Surcharge
                                                                </a> --}}
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-1 m-1">
                                                        <div class="table-responsive">
                                                            <table id="package_surcharge" class="table table-bordered text-cente">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col">Status</th>
                                                                        <th scope="col">Effective Date</th>
                                                                        <th scope="col">End Date</th>
                                                                        <th scope="col">Sure Post</th>
                                                                        <th scope="col">Ground Residential</th>
                                                                        <th scope="col">Air Residential</th>
                                                                        <th scope="col">Additional Handling</th>
                                                                        <th scope="col">Large Package > 50 lbs.</th>
                                                                        <th scope="col">Action</th>
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
                                    <button type="submit" class="btn  btn-primary m-1">Save</button>
                                    </form>

                                </div>
                                <div class="tab-pane fade show" id="tab_client_config" role="tabpanel" area-labelledby="client_config_tab">
                                     TBD
                                </div>
                                <div class="tab-pane fade show" id="tab_account_management" role="tabpanel" area-labelledby="account_management_tab">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card o-hidden mb-4">
                                                <div class="card-header">
                                                    <h3 class="w-50 float-left card-title m-0">Contacts</h3>
                                                    <div class="separator-breadcrumb">
                                                            <a href="javascript:void(0);" onclick="getModal('{{ route('carriers.createContact',$row->id) }}')" class="btn btn-primary btn-icon m-1" style=" float: right;">
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
                                <div class="tab-pane fade show" id="tab_contacts" role="tabpanel" area-labelledby="contacts_tab">
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="card o-hidden mb-4">
                                                <div class="card-header">
                                                    <h3 class="w-50 float-left card-title m-0">Account Notes</h3>
                                                    <div class="separator-breadcrumb">
                                                        <a href="javascript:void(0);" onclick="getModal('{{ route('carriers.createNote',$row->id) }}')" class="btn btn-primary btn-icon m-1" style=" float: right;">
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
                                <div class="tab-pane fade show" id="tab_orders" role="tabpanel" area-labelledby="orders_tab">
                                    TBD
                                </div>
                                <div class="tab-pane fade show" id="tab_data_analytics" role="tabpanel" area-labelledby="data_analytics_tab">
                                    TBD
                                </div>
                                <div class="tab-pane fade show" id="tab_documents" role="tabpanel" area-labelledby="documents_tab">
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="card o-hidden mb-4">
                                                <div class="card-header">
                                                    <h3 class="w-50 float-left card-title m-0">Documents</h3>
                                                    <div class="separator-breadcrumb">
                                                        <a href="javascript:void(0);" onclick="getModal('{{ route('carriers.createDocument',$row->id) }}')" class="btn btn-primary btn-icon m-1" style=" float: right;">
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
                                                        <a href="javascript:void(0);" onclick="getModal('{{ route('carriers.createLink',$row->id) }}')" class="btn btn-primary btn-icon m-1" style=" float: right;">
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
                                <div class="tab-pane fade show" id="tab_reports" role="tabpanel" area-labelledby="reports_tab">

                                </div>
                            </div>
                        </div>
                        <!-- tab end -->
                    </div>

            </div>
        </div>
    </div>
    <!-- end of col -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"></div>
@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>
    <script src="{{asset('assets/js/sweetalert.script.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js"></script>
    {{-- <script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/jquery.inputmask.bundle.js"></script> --}}

<script>
    contactList();
    noteList();
    documentList();
    GetLinks();
    carrierFeeList();
    surchargeList();

$(document).ready(function(){
    $('input.surcharge').prop("readonly", true);
    // $('select.surcharge').prop("disabled", true);

    $('.date-time').mask('00/00/0000', {
        placeholder: "mm/dd/yyyy"
    });
    // $('.date-time').flatpickr({
    //     static:true,
    //     defaultDate: "today",
    //     enableTime: false,
    //     dateFormat: "m/d/Y",

    // });
})

    $("#new_client").on('submit',function(e){
        e.preventDefault();
        $(".submit").attr("disabled", true);
        var form_cust = $('#new_client')[0];
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '{{ route('carriers.update',$row->id) }}',
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

    $("#updateConfig").on('submit',function(e){
        e.preventDefault();
        $(".submit").attr("disabled", true);
        var form_cust = $('#updateConfig')[0];
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '{{ route('carriers.updateConfig') }}',
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

    })

function contactList(){
    $('#contacts').DataTable({
        destroy: true,
        responsive: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax:{
                url: '{{ route('carriers.datatable.contactList',$row->id) }}',
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

function carrierFeeList(){
    $('#dynamic_fees').DataTable({
        destroy: true,
        responsive: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax:{
                url: '{{ route('carriers.datatable.carrierFeeList',$row->id) }}',
                method:'GET',
            },
        columns: [
            {data: 'effective_date', name: 'effective_date', orderable: false},
            {data: 'ground', name: 'ground', orderable: false},
            {data: 'air', name: 'air', orderable: false},
            {data: 'international_air', name: 'international_air', orderable: false},
            {data: 'is_primary', name: 'is_primary', orderable: false},
            {data: 'action', name: 'Action', orderable: false, className: 'action'},
        ],
        "order": [],
        columnDefs: [
            {
                "targets": [ 0 ],
                "visible": true,
            },
            {
                "targets": [3],
                "className": ''
            },
        ],
        "bFilter": false,
        "pageLength": 5,
        "lengthMenu": [ [5,10, 25, 50, -1], [5,10, 25, 50, "All"] ]
    });
}

function surchargeList(){
    $('#package_surcharge').DataTable({
        destroy: true,
        responsive: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax:{
                url: '{{ route('carriers.datatable.surchargeList',$row->id) }}',
                method:'GET',
            },
        columns: [
            {data: 'status', name: 'status'},
            {data: 'effective_date', name: 'effective_date', orderable: false},
            {data: 'end_date', name: 'end_date', orderable: false},
            {data: 'sure_post', name: 'sure_post', orderable: false},
            {data: 'ground_residential', name: 'ground_residential', orderable: false},
            {data: 'air_residential', name: 'air_residential', orderable: false},
            {data: 'additional_handling', name: 'additional_handling', orderable: false},
            {data: 'large_package_gt_50_lbs', name: 'large_package_gt_50_lbs', orderable: false},
            {data: 'action', name: 'Action', orderable: false, className: 'action'},
        ],
        "bFilter": false,
        "pageLength": 5,
        "lengthMenu": [ [5,10, 25, 50, -1], [5,10, 25, 50, "All"] ]
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
            url: '{{ route('carriers.datatable.noteList',$row->id) }}',
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
                url: '{{ route('carriers.datatable.documentList',$row->id) }}',
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

function GetLinks(){
    $('#links').DataTable({
        destroy: true,
        responsive: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax:{
                url: '{{ route('carriers.datatable.linkList',$row->id) }}',
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

function setPrimaryContact(checkbox_obj,id){
    if(checkbox_obj.checked) {
        confirm('Do You want to set as primary?');
    }
    else{
        confirm('Do You want to remove as primary?');
    }
    $.ajax({
        type:'GET',
        url:'{{ route('carriers.setPrimaryContact') }}',
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

function setPrimaryFee(checkbox_obj,id){
    if(checkbox_obj.checked) {
        confirm('Do You want to set as primary?');
    }
    else{
        confirm('Do You want to remove as primary?');
    }
    $.ajax({
        type:'GET',
        url:'{{ route('carriers.setPrimaryFee') }}',
        data:{id:id},
        success:function(response){
            toastr.success(response.msg)
            carrierFeeList();
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

function deleteFee(url){
    if(confirm('Are You Sure You Want To Delete This?')){
        $.ajax({
            type:'GET',
            url:url,
            success:function(response){
                toastr.success("Success")
                carrierFeeList()
            }
        })
    }
}
function deleteSurcharge(url){
    if(confirm('Are You Sure You Want To Delete This?')){
        $.ajax({
            type:'GET',
            url:url,
            success:function(response){
                toastr.success("Success")
                surchargeList()
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
function enableDisable(val){
   $("input."+val).prop("readonly", false);
   $("select."+val).removeAttr("disabled");;
}
</script>
@endsection
