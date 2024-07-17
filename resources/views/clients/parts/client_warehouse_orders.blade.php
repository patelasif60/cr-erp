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
            <li><a href="{{ route('clients.clients.orders') }}">Clients</a></li>
            <li>Warehouse Orders</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Warehouse Orders</h6>
                    
                </div>
                <div class="card-body">
                    <div class="row">
                        <table class="table table-border" id="purchase_summary">
                            <thead>
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
                                                    <a href="{{ url('/purchase_order/edit/' . $row->id . '/' . $res['id'] . '/client') }}" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Edit">
                                                        <i class="nav-icon i-Pen-2 "></i>
                                                    </a>
                                                @endif
                                                @if($res['report_path'])
                                                    <a href="{{ url('/' . $res['report_path']) }}" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Download Report">
                                                        <i class="nav-icon i-Down"></i>
                                                    </a>
                                                @endif
                                                
                                                @if($res['po_status'] && $res['po_status'] != 'Pending')
                                                    <a href="{{ url('/purchase_order/editasnbol/' . $row->id . '/' . $res['id'] . '/client') }}" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Submit ASN/BOL">
                                                        <i class="nav-icon">Submit ASN/BOL</i>
                                                    </a>
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
                            <a href="{{ route('purchase_order.create_purchase_order',['id' => $row->id, 'type' => 'client']) }}" class="btn btn-primary">New Purchase Order</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- end of col -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"></div>
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true"></div>
@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>
<script src="{{asset('assets/js/sweetalert.script.js')}}"></script>
    <script>

       $(document).ready(function () {

            GetProducts();
       });
    function GetProducts(){
        var table = $('#datatable').DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
			ajax:{
                    url: '{{ route('getmasterproductsbyclient',$row->id) }}',
                    method:'GET',
                    data: {
                        etin_filter:$("#etin_filter").val(),
                        listing_name_filter:$("#listing_name_filter").val(),
						unit_description_filter:$("#unit_description_filter").val(),
						product_filter:$("#product_filter").val(),
						upc_filter:$("#upc_filter").val(),
						status_filter:$("#status_filter").val(),
                        price_group : $('#price_group').val()
                    }
                },
            columns: [
                {data: 'ETIN', name: 'ETIN'},
				{data: 'product_listing_name', name: 'product_listing_name'},
                {data: 'group_price', name: 'group_price'},
                {data: 'channel', name: 'channel'},
                {data: 'inventory', name: 'inventory'},
                {data: 'upc', name: 'upc'},
				{data: 'status', name: 'status'},
                {data: 'action', name: 'Action', orderable: false},
            ],

            // columnDefs: [
            //     {
            //         "targets": [ 0 ],
            //         "visible": false
            //     }
            // ],
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
                $("#exampleModal").html('');
                $('#exampleModal').html(response);
                $('#exampleModal').modal('show');
            }
        })
    }

    function editModal(url) {
        $.ajax({
            type:'GET',
            url:url,
            success:function(response){
                $("#editModal").html('');
                $('#editModal').html(response);
                $('#editModal').modal('show');
            }
        })
    }



   </script>
@endsection
