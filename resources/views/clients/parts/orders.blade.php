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
            <li>Orders</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Orders</h6>
                    <div class="separator-breadcrumb">
                        <a href="{{route('orders.create',$row->id)}}" target="_blank"  class="btn btn-primary btn-icon m-1" style=" float: right;">
                            <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Add New Manual Order
                        </a>
                    </div>
                    <div class="dropdown dropleft text-right w-50 float-right">
                    </div>
                </div>
                    <div class="card-body">
                        <table id="datatableOrder" class="table table-bordered text-center " style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col">Order Date</th>
                                    <th scope="col">e-tailer Order Number</th>
                                    <th scope="col">Client</th>
                                    <th scope="col">Order Source</th>
                                    <th scope="col">Destination</th>
                                    <th scope="col">Channel Delivery Date</th>
                                    <th scope="col">Ship By</th>
                                    <th scope="col">Order Status</th>
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
    <!-- end of col -->

@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>
<script src="{{asset('assets/js/sweetalert.script.js')}}"></script>
    <script>


   
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    GetActiveProducts();
    //Active Product List
    function GetActiveProducts(){
        $("#preloader").show();
        var url = '{{ route('getOptimizedorders') }}';
        table1 = $('#datatableOrder').DataTable({
            // dom:"Bfrtip",
            paging:   true,
            destroy: true,
            responsive: false,
            processing: true,
            serverSide: true,
            autoWidth: false,
            colReorder: true,
            searching:true,
            colReorder: {
                order: [  ]
            },
            // scrollX: true,
            // stateSave: true,

            lengthMenu: [[10,25, 100, -1], [10, 25, 100, "All"]],
            pageLength: 10,

            ajax:{
                    url: url,
                    method:'POST',
                    data: function(d) {
                        // console.log(d);
                        var frm_data = $('#form_filters').serializeArray();
                        $.each(frm_data, function(key, val) {
                            d[val.name] = val.value;
                        });
                        
                            d['client_id'] = '{{$row->id}}';
                            d['client_order'] = 1;
                        
                    }
                },
            columns: [
                {data: 'created_at', name: 'created_at',defaultContent:'', searchable: false},{data: 'etailer_order_number', name: 'etailer_order_number',defaultContent:'', searchable: false},{data: 'client_name', name: 'client_name',defaultContent:'', searchable: false},{data: 'order_source', name: 'order_source',defaultContent:'', searchable: false},{data: 'ship_to_state', name: 'ship_to_state',defaultContent:'', searchable: false},{data: 'channel_estimated_delivery_date', name: 'channel_estimated_delivery_date',defaultContent:'', searchable: false},{data: 'ship_by_date', name: 'ship_by_date',defaultContent:'', searchable: false},{data: 'order_status', name: 'order_status',defaultContent:'', searchable: false},{data:'action', name: 'action'}
            ],
                // columnDefs: [
                // 	{
                // 		"targets": [8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28],
                // 		"visible": false,
                // 	},
            // ],
            oLanguage: {
                "sSearch": "Search:",

            },
            fnInitComplete: function (oSettings, json) {
                $("#preloader").hide();
            },
            rowCallback: function(row, data) {
                $('td:eq(1)', row).css('color', '#4d2673');
                $('td:eq(1)', row).css('font-weight', 'bold');
                $('td:eq(1)', row).css('cursor', 'pointer');
            }

        });
        var col_order = table1.colReorder.order();
        table1.colReorder.order(col_order)
        // console.log(col_order)
        table1.on( 'column-reorder', function ( e, settings, details ) {
            $("#btn_open_save_as_modal").css("display", "");
            var order = table1.colReorder.order();
            $('#column_orders').val(order)
            $('#btn_save_smart_filter').show();
        } );
        $('.listing-filter-columns').each(function(e){
            //    console.log($(this).val(), this.checked,e)
            if(this.checked === false){
                    table1.column( $(this).val() ).visible( false );
            }
        });
    }


   </script>
@endsection
