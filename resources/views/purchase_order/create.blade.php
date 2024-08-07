@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
    <style>
        .flatpickr-wrapper{
            width:100% !important;
        }
    </style>
@endsection

@section('main-content')
    @php $required_span = '<span class="text-danger">*</span>' @endphp
    <div class="breadcrumb">
        <h1>Purchasing</h1>
        <ul>
            <li><a href="">Purchasing</a></li>
            @if (isset($row))
                <li><a href="{{ route('suppliers.index') }}">Supplier</a></li>
                <li><a href="{{ route('suppliers.edit',$row->id) }}">{{$row->name}}</a></li>
            @else
                <li><a href="{{ route('clients.index') }}">Client</a></li>
                <li><a href="{{ route('clients.edit',$c_row->id) }}">{{$c_row->company_name}}</a></li>
            @endif
            <li>New</li>
        </ul>
    </div>
    
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">New Purchase Order</h6>
                    <ul>
                        <li>Warehouse: <label id='wh_name'>NA</label></li>
                        <li>Frozen Weight: <label id='frz_wt'>NA</label></li>
                        <li>Dry Weight: <label id='dry_wt'>NA</label></li>
                        <li>Refrigerated Weight: <label id='ref_wt'>NA</label></li>
                    </ul>
                    <div class="col-md-6">
                        <div id="error_container"></div>
                    </div>
                </div>
                <form action="#" id="new_po">
                    @csrf
                    <div class="card-body">                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group col-md-12">
                                    <label for="supplier_product_package_type" class="ul-form__label">Warehouse:<?php echo $required_span; ?></label>
                                    <select class="form-control" id="warehouse_id" name="warehouse" onchange="getProductList()">
                                        <option value="">--Select--</option>
                                        @if ($warehouses)
                                            @foreach($warehouses as $warehouse)                       
                                                <option value="{{ $warehouse->id }}">{{ $warehouse->warehouses }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="name" class="ul-form__label">Order Date:<?php echo $required_span; ?></label>
                                    <div>
                                        <input type="text" class="date_picker form-control " id="order_date" name="order_date" placeholder="Enter Order Date" style="width:100%" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12">
                                    <label for="name" class="ul-form__label">Delivery Date:<?php echo $required_span; ?></label>
                                    <div>
                                        <input type="text" class="form-control date_picker" id="delivery_date" name="delivery_date" placeholder="Enter Delivery Date" style="width:100%"  />
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="name" class="ul-form__label">Delivery Fees:</label>
                                    <input type="text" class="form-control" id="delivery_fees" name="delivery_fees" placeholder="Enter Delivery Fees" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12">
                                    <label for="name" class="ul-form__label">Freight Fees:</label>
                                    <input type="text" class="form-control" id="freight_fees" name="freight_fees" placeholder="Enter Freight Fees" />
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="name" class="ul-form__label">Surcharge Fees:</label>
                                    <input type="text" class="form-control" id="surcharge" name="surcharge" placeholder="Enter Surcharge" />
                                </div>
                            </div>                            
                            <div class="col-lg-12">
                                <table class="table table-border" id="purchase_order_products">
                                    <thead>
                                        <tr>
                                            <th>ETIN</th>
                                            <th>Product Number</th>
                                            <th>Product Listing Name</th>
                                            <th>Status</th>
                                            <th>Lead Time</th>
                                            <th>Product Availability</th>
                                            <th>On hand Qty</th>
                                            <th>On Order Qty</th>
                                            <th>Weeks Worth QTY</th>
                                            <th>Min Order QTY</th>
                                            <th>Suggested Order QTY</th>
                                            <th>Order QTY</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <button type="button" class="btn  btn-primary m-1" id="saveAsDraft">Save Draft PO</button>
                                <button type="button" class="btn  btn-primary m-1" id="submitPo">Submit Purchase Order</button>
                                @if (isset($row))
                                    <a href="{{ route('suppliers.edit', $row->id) }}" class="btn btn-outline-secondary m-1">Cancel</a>
                                @else
                                    <a href="{{ route('clients.edit', $c_row->id) }}" class="btn btn-outline-secondary m-1">Cancel</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- <div class="card-footer bg-transparent">
                        <div class="mc-footer">
                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn  btn-primary m-1">Submit</button>
                                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary m-1">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </form>

            </div>
        </div>
    </div>
    <!-- end of col -->
@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script>
    
       $(document).ready(function () {
        $('#cuttoff_time').flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
        });     
        
        $('.date_picker').flatpickr({
        static: true,
        enableTime: false,
        dateFormat: "Y-m-d",
    });

       });

        function getProductList() {            
            var wh = document.getElementById("warehouse_id");
            var warehouseId = wh.value;
            document.getElementById('wh_name').innerText = wh.children.item(wh.selectedIndex).innerText;
            PurchaseOrderProducts(warehouseId);
            setTimeout(() => {
               calculateWeights();               
            }, 3000);
        }

        function calculateWeights() {
            $('div#preloader').show();
            var values = $('#purchase_order_products').DataTable().data().toArray();
            var dryWeight = 0, frozenWeight = 0, refWeight = 0;
            for (i in values) {
                var weight = +values[i].weight
                var temp =  String(values[i].temp)
                var orderQty = +values[i].order_qty
                if (temp === 'dry') {
                    dryWeight += (weight * orderQty)
                } else if (temp === 'frozen') {
                    frozenWeight += (weight * orderQty)
                } else if (temp === 'refrigerated') {
                    refWeight += (weight * orderQty)
                }
            }
            document.getElementById('frz_wt').innerText = frozenWeight + ' lbs'
            document.getElementById('dry_wt').innerText = dryWeight + ' lbs'
            document.getElementById('ref_wt').innerText = refWeight + ' lbs'
            $('div#preloader').hide();
        }

        function PurchaseOrderProducts(warehouseId) {
            table1 = $('#purchase_order_products').DataTable({
                // dom:"Bfrtip",
                paging:   true,
                destroy: true,
                responsive: false,
                processing: true,
                serverSide: false,
                autoWidth: false,
                colReorder: true,
                scrollX: true,
                lengthMenu: [[25, 100, -1], [25, 100, "All"]],
                pageLength: 25,

                ajax:{
                        url: '{{ route('datatable.PurchaseOrderProducts') }}',
                        method: 'GET',
                        data: function(d) {
                            d['supplier'] = '{{ isset($row)  ? $row->id : '' }}';
                            d['client'] = '{{ isset($c_row)  ? $c_row->id : '' }}';
                            d['warehouse_id'] = warehouseId;				
                        }
                    },
                columns: [
                    {data:'ETIN' , name:'ETIN'},
                    {data:'supplier_product_number' , name:'supplier_product_number',defaultContent:'-'},
                    {data:'product_listing_name' , name:'product_listing_name'},
                    {data:'status' , name:'status',defaultContent:'-'},
                    {data:'lead_time' , name:'lead_time',defaultContent:'-'},
                    {data:'product_availability', name: 'product_availability', defaultContent: '-'},
                    {data:'on_hand_qty' , name:'on_hand_qty',defaultContent:'0'},
                    {data:'on_order_qty' , name:'on_order_qty',defaultContent:'0'},
                    {data:'week_worth_qty' , name:'week_worth_qty',defaultContent:'0'},
                    {data:'min_order_qty' , name:'min_order_qty',defaultContent:'0'},
                    {data:'suggested_order_qty' , name:'suggested_order_qty',defaultContent:'0'},
                    {data:'order_qty' , name:'order_qty',defaultContent:'0', 
                                render: function (data, type, row) {
                                    return '<input class="form-control trackInput" id="order_qty" name="order_qty" type="text"  value = ' + row.order_qty + '  />';}
                    },
                    {data:'temp' , name:'temp',defaultContent:'0', visible: false, searchable: false},
                    {data:'weight' , name:'weight',defaultContent:'0', visible: false, searchable: false},
                ],
                "drawCallback": function( settings ) {
                    $(".trackInput").on("change",function(){
                        var $row = $(this).parents("tr");
                        var rowData = table1.row($row).data();
                        rowData.order_qty = $(this).val();
                        calculateWeights();
                    });
                }
            });        
	    }

       //Save as Draft
        $('#saveAsDraft').click(function (e){            
            $('input').attr('required', false);
            $('div#preloader').show();
            e.preventDefault();
            var form_cust = $('#new_po')[0];
            let form1 = new FormData(form_cust);

            var warehouseId = document.getElementById("warehouse_id").value;
            form1.append('warehouse_id', warehouseId);
            form1.append('supplier_id', '{{ isset($row) ? $row->id : '' }}');
            form1.append('client_id', '{{ isset($c_row) ? $c_row->id : '' }}');

            var url = '/purchase_order/saveAsDraft';

            var table = document.getElementById('purchase_order_products');
            var items = [];

            var values = $('#purchase_order_products').DataTable().data().toArray();
            for (i in values) {
                items.push({
                    'etin': values[i].ETIN,
                    'product_number': values[i].supplier_product_number,
                    'product_listing_name': values[i].product_listing_name,
                    'status': values[i].status,
                    'on_hand_qty': values[i].on_hand_qty,
                    'on_order_qty': values[i].on_order_qty,
                    'weeks_worth_qty': values[i].weeks_worth_qty,
                    'min_order_qty': values[i].min_order_qty,
                    'suggested_order_qty': values[i].suggested_order_qty,
                    'order_qty': values[i].order_qty,
                })
            }

            // items.splice(0, 1);
            form1.append('items', JSON.stringify(items));

            $('div#preloader').hide();
            $.ajax({
                type: "POST",
                url: url,
                data: form1,
                processData: false,
                contentType: false,
                success: function( response ) {
                    $('div#preloader').hide();
                    if(response.error == 0) {
                        toastr.success(response.msg);
                        window.location.href = response.url
                    } else {
                        toastr.error(response.msg);
                    }
                },
                error: function(data){
                    $(".submit").attr("disabled", false);
                    $('div#preloader').hide();
                    var errors = data.responseJSON;
                    $("#error_container").html('');
                    $('label[class=error]').remove();
                    // $.each( errors.errors.original, function( key, value ) {
                    //     var ele = "#"+key;
                    //     $(ele).addClass('error_border');
                    //     $('<label class="error">'+ value +'</label>').insertAfter(ele);
                    //     $("#error_container").append("<p class='bg-danger mb-1 text-white p-1'>"+ value +"</p>");
                    //     toastr.error(value);
                    // });

                    $.each( errors.errors, function( key, value ) {
                        var ele = "#"+key;
                        $(ele).addClass('error');
                        $('<label class="error">'+ value +'</label>').insertAfter(ele);
                    });
                    
                }
            });
        });

        $('#submitPo').click(function (e){            
            $('input').attr('required', false);
            $('div#preloader').show();
            e.preventDefault();
            var form_cust = $('#new_po')[0];
            let form1 = new FormData(form_cust);

            var warehouseId = document.getElementById("warehouse_id").value;
            form1.append('warehouse_id', warehouseId);
            form1.append('supplier_id', '{{ isset($row) ? $row->id : '' }}');
            form1.append('client_id', '{{ isset($c_row) ? $c_row->id : '' }}');

            var url = '/purchase_order/submit_po';

            var table = document.getElementById('purchase_order_products');
            var items = [];

            var values = $('#purchase_order_products').DataTable().data().toArray();
            for (i in values) {
                items.push({
                    'etin': values[i].ETIN,
                    'product_number': values[i].supplier_product_number,
                    'product_listing_name': values[i].product_listing_name,
                    'status': values[i].status,
                    'lead': values[i].lead_time,
                    'product_availability': values[i].product_availability,
                    'on_hand_qty': String(values[i].on_hand_qty),
                    'on_order_qty': String(values[i].on_order_qty),
                    'week_worth_qty': String(values[i].week_worth_qty),
                    'min_order_qty': String(values[i].min_order_qty),
                    'suggested_order_qty': String(values[i].suggested_order_qty),
                    'order_qty': String(values[i].order_qty),
                })
            }
            form1.append('items', JSON.stringify(items));

            $('div#preloader').hide();
            $.ajax({
                type: "POST",
                url: url,
                data: form1,
                processData: false,
                contentType: false,
                success: function( response ) {
                    $('div#preloader').hide();
                    if(response.error == 0) {
                        toastr.success(response.msg);
                        window.location.href = response.url
                    } else {
                        toastr.error(response.msg);
                    }
                },
                error: function(data){
                    $(".submit").attr("disabled", false);
                    $('div#preloader').hide();
                    $("#error_container").html('');
                    $('label[class=error]').remove();
                    var errors = data.responseJSON;
                    $.each( errors.errors, function( key, value ) {
                        var ele = "#"+key;
                        $(ele).addClass('error');
                        $('<label class="error">'+ value +'</label>').insertAfter(ele);
                        
                    });
                    $.each( errors.errors_item, function( key, value ) {
                        $("#error_container").append("<p class='bg-danger mb-1 text-white p-1'>"+ value +"</p>");
                    });
                    
                    // $.each( errors.errors.original, function( key, value ) {
                    //     var ele = "#"+key;
                    //     $(ele).addClass('error_border');
                    //     $('<label class="error">'+ value +'</label>').insertAfter(ele);
                    //     $("#error_container").append("<p class='bg-danger mb-1 text-white p-1'>"+ value +"</p>");
                    //     toastr.error(value);
                    // });
                }
            });
        });
   </script>
@endsection
