@extends('layouts.master')

@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/css/select2/new/select2.min.css')}}">
    <style>
        .error{
            color:red;
        }
    </style>
@endsection
@section('main-content')
<div class="breadcrumb">
        <h1>Packaging Components</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="col-md-12 mt-4">
        <ul class="nav nav-tabs nav-justified">
            <li class="nav-item">
                <a class="nav-link active" href="#tab_comp_list" id="comp_list_tab" role="tab" aria-controls="comp_list_tab" area-selected="true" data-toggle="tab">Component List</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#tab_custom_outer" id="custom_outer_tab" role="tab" aria-controls="custom_outer_tab" area-selected="true" data-toggle="tab">Custom Outer Box</a>
            </li>						
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab_comp_list" role="tabpanel" area-labelledby="comp_list_tab">
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">
                        <div class="card text-left">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="components_table" class="display table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr class="bg-dark text-white">
                                                <th>ETIN</th>
                                                <th>Product Description</th>
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
            <div class="tab-pane fade" id="tab_custom_outer" role="tabpanel" area-labelledby="custom_outer_tab">
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">
                        <a class="btn btn-primary float-right text-white" onclick="GetModel('/new_custom_outer')">
                            <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Add New Custom Outer Box
                        </a>
                    </div>
                    <div class="col-md-12 mb-4">
                        <div class="card text-left">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="custom_outer_table" class="display table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr class="bg-dark text-white">
                                                <th>ETIN</th>
                                                <th>Item Description</th>
                                                <th>Client</th>
                                                <th>No of Items</th>
                                                <th>Transit Days</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($ccob))
                                                @foreach ($ccob as $cb)                                                    
                                                    <tr>
                                                        <td>{{ $cb->package_material->ETIN }}</td>
                                                        <td>{{ $cb->package_material->product_description }}</td>
                                                        <td>{{ $cb->client->company_name }}</td>
                                                        <td>{{ $cb->max_item_count }}</td>
                                                        <td>{{ $cb->transit_days }}</td>
                                                        <td><a class="btn btn-warning" onclick="showEditDialog({{ $cb->id }});">Edit</a></td>
                                                    </tr>     
                                                @endforeach                                     
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    

@endsection

@section('page-js')
<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/select2/new/select2.min.js')}}"></script>
<script>
   $(document).ready(function () {
        var table_html = '';
        var table_html_td = '';
        var i = 1;
        var dt = $('#components_table').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ordering: false,
            
            ajax: '{!! route('packagingcomponant.packagingcompnentslist') !!}',
            columns: [
                { data: 'ETIN', name: 'ETIN' },
                { data: 'product_description', name: 'product_description' },
                { data: 'action', name: 'action', searchable: false }
            ]
        });

        $('#custom_outer_table').DataTable();
   });

   function showEditDialog(id) {
        var url = "/edit_custom_outer/" + id;
        GetModel(url);
    }

</script>
@endsection