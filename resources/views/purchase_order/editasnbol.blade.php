@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
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
            <li>Edit</li>
            <li>Order - {{ $ps->order }}</li>
        </ul>
    </div>
    <div class="col-md-6">
        <div id="error_container"></div>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Submit ASN/BOL Quantity</h6>
                    <ul>
                        <li>Warehouse: <label id='wh_name'>NA</label></li>
                        <li>PO # <label id='po_num'>{{ isset($ps->order) ? $ps->order : 'NA' }}</label></li>
                        <li>Frozen Weight: <label id='frz_wt'>NA</label></li>
                        <li>Dry Weight: <label id='dry_wt'>NA</label></li>
                        <li>Refrigerated Weight: <label id='ref_wt'>NA</label></li>
                    </ul>
                </div>
                <form action="#" id="new_po">
                    @csrf
                    <div class="card-body">                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group col-md-12">
                                    <label for="supplier_product_package_type" class="ul-form__label">Warehouse:<?php echo $required_span; ?></label>
                                    <select class="form-control select2" id="warehouse" name="warehouse" onchange="getProductList()" disabled>
                                        <option value="">--Select--</option>
                                        @if ($warehouses)
                                            @foreach($warehouses as $warehouse)                       
                                                <option value="{{ $warehouse->id }}" <?php if(isset($ps)){ if($ps->warehouse_id == $warehouse->id) echo "selected";}?> >{{ $warehouse->warehouses }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="name" class="ul-form__label">Order Date:<?php echo $required_span; ?></label>
                                    <input disabled type="date" class="form-control" id="order_date" name="order_date" placeholder="Enter Order Date" value="{{ $ps->purchasing_asn_date }}"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12">
                                    <label for="name" class="ul-form__label">Delivery Date:<?php echo $required_span; ?></label>
                                    <input disabled type="date" class="form-control" id="delivery_date" name="delivery_date" placeholder="Enter Delivery Date" value="{{ $ps->delivery_date }}"/>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="name" class="ul-form__label">Delivery Fees:</label>
                                    <input disabled type="text" class="form-control" id="delivery_fees" name="delivery_fees" placeholder="Enter Delivery Fees" value="{{ $ps->delivery_inbound_fees }}"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12">
                                    <label for="name" class="ul-form__label">Freight Fees:</label>
                                    <input disabled type="text" class="form-control" id="freight_fees" name="freight_fees" placeholder="Enter Freight Fees" value="{{ $ps->freight_shipping_charge }}"/>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="name" class="ul-form__label">Surcharge Fees:</label>
                                    <input disabled type="text" class="form-control" id="surcharge" name="surcharge" placeholder="Enter Surcharge" value="{{ $ps->surcharge_1 }}"/>
                                </div>
                            </div>                            
                            <div class="col-lg-12">
                                <table class="table table-border" id="purchase_order_products">
                                    <thead>
                                        <tr>
                                            <th>ETIN</th>
                                            <th>Product Number</th>
                                            <th>Product Listing Name</th>                                            
                                            <th>Order QTY</th>
                                            <th>ASN QTY</th>
                                            <th>ASN #</th>
                                            <th>BOL QTY</th>
                                            <th>BOL #</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>                            
                        </div>
                        @if(isset($purchase_details['id']) && ($purchase_details['status'] == "" || in_array($purchase_details['status'],['Ready','Submitted','Pending'])))
                            <div class="row">
                                <div class="col-lg-12 text-right">

                                    <button type="button" class="btn  btn-primary m-1" id="submitAsnBol">Submit ASN/BOL</button>
                                    @if (isset($row))
                                        <a href="{{ route('suppliers.edit', $row->id) }}" class="btn btn-outline-secondary m-1">Cancel</a>
                                    @else
                                        <a href="{{ route('clients.edit', $c_row->id) }}" class="btn btn-outline-secondary m-1">Cancel</a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- end of col -->

    <!-- ADD ASN/BOL Modal -->
    <div class="modal fade" id="asnbol" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header" style="background-color:#eee">
                <h5 class="modal-title" id="exampleModalLongTitle">Add ASN/BOL #</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" name="bol_number" id="bol_number" class="form-control" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="add_bol_number">Add BOL #</button>
            </div>
            </div>
        </div>
    </div>

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
         getProductList();                       
        });

        <?php if(!$purchase_details['asn'] || !$purchase_details['bol_number']) {?>
            $('#asnbol').modal('show');
        <?php } ?>

        $('#add_bol_number').click(function(){
            var asnbol_num = $('#bol_number').val();
            var pattern = ['/','?','+','*',','];
            var errorFlag= 0;
            pattern.forEach(function(word){
                errorFlag = errorFlag + asnbol_num.includes(word);
            });
            if(errorFlag > 0){
                toastr.error("/ ?  *  +  , can't be use for BOL Number.");
                $('div#preloader').hide();
                return false;
            }

            var values = $('#purchase_order_products').DataTable().data().toArray();
            for (i in values) {
                // values[i].bol  = asnbol_num;
                @if($purchase_details['asn'] == '')
                values[i].asn  = asnbol_num;
                @elseif($purchase_details['bol_number'] == '')
                values[i].bol  = asnbol_num;
                @endif
            }
            // $('.trackInput1').val(asnbol_num);
            @if($purchase_details['asn'] == '')
                $('.trackInput2').val(asnbol_num);
            @elseif($purchase_details['bol_number'] == '')
                $('.trackInput1').val(asnbol_num);
            @endif

            $('#asnbol').modal('hide');
        });


 
         function getProductList() {
            $('div#preloader').show();
            var wh = document.getElementById("warehouse");
            var warehouseId = wh.value;
            document.getElementById('wh_name').innerText = wh.children.item(wh.selectedIndex).innerText;
            PurchaseOrderProducts(warehouseId, onload);
            setTimeout(() => {
                calculateWeights(); 
                $('div#preloader').hide();
            }, 3000);
         }

         function calculateWeights() {
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
        }
 
         function PurchaseOrderProducts(warehouseId, onload) {
             table1 = $('#purchase_order_products').DataTable({
                 // dom:"Bfrtip",
                 paging:   true,
                 destroy: true,
                 responsive: false,
                 processing: true,
                 serverSide: true,
                 autoWidth: false,
                 colReorder: true,
                 scrollX: true,
                 lengthMenu: [[25, 100, -1], [25, 100, "All"]],
                 pageLength: 25,
 
                 ajax:{
                        url: '{{ route('datatable.SavedPurchaseOrderProductsForAsn') }}',
                        method: 'GET',
                        data: function(d) {
                        d['supplier_id'] = '{{ isset($row)  ? $row->id : '' }}';
                        d['supplier_name'] = '{{ isset($row)  ? $row->name : '' }}';
                        d['client_id'] = '{{ isset($c_row)  ? $c_row->id : '' }}';
                        d['client_name'] = '{{ isset($c_row)  ? $c_row->company_name : '' }}';
                        d['warehouse_id'] = warehouseId;
                        d['po'] = '{{ $ps->order }}'
                        }
                    },
                 columns: [
                    {data:'ETIN' , name:'ETIN'},
                    {data:'supplier_product_number' , name:'supplier_product_number',defaultContent:'-'},
                    {data:'product_listing_name' , name:'product_listing_name'},
                    {data:'order_qty' , name:'order_qty'},
                    {data:'asn_qty' , name:'asn_qty', defaultContent:'0', 
                                render: function (data, type, row) {
                                    return '<input class="form-control trackInput3" id="asn_qty" name="asn_qty" type="text"  value = ' + row.asn_qty + '  />';}
                    },
                    {data:'asn' , name:'asn', defaultContent:'-', 
                                render: function (data, type, row) {
                                    return '<input class="form-control trackInput2" id="asn" name="asn" type="text"  value = ' + row.asn + '  />';}
                    },
                    {data:'asn_bol_shipped_qty' , name:'asn_bol_shipped_qty', defaultContent:'0', 
                                render: function (data, type, row) {
                                    return '<input class="form-control trackInput" id="asn_bol_shipped_qty" name="asn_bol_shipped_qty" type="text"  value = ' + row.asn_bol_shipped_qty + '  />';}
                    },
                    {data:'bol' , name:'bol', defaultContent:'-', 
                                render: function (data, type, row) {
                                    return '<input class="form-control trackInput1" id="bol" name="bol" type="text"  value = ' + row.bol + '  />';}
                    },
                    {data:'temp' , name:'temp',defaultContent:'0', visible: false, searchable: false},
                    {data:'weight' , name:'weight',defaultContent:'0', visible: false, searchable: false},
                 ],
                 "drawCallback": function( settings ) {
                     $(".trackInput").on("change",function(){
                         var $row = $(this).parents("tr");
                         var rowData = table1.row($row).data();
                         console.log('iiii',rowData);
                         rowData.asn_bol_shipped_qty = $(this).val();
                     });
                     $(".trackInput1").on("change",function(){
                         var $row = $(this).parents("tr");
                         var rowData = table1.row($row).data();
                         rowData.bol = $(this).val();
                     });
                     $(".trackInput2").on("change",function(){
                         var $row = $(this).parents("tr");
                         var rowData = table1.row($row).data();
                         rowData.asn = $(this).val();
                     });
                     $(".trackInput3").on("change",function(){
                         var $row = $(this).parents("tr");
                         var rowData = table1.row($row).data();
                         rowData.asn_qty = $(this).val();
                     });
                 }
             });        
         }
 
         $('#submitAsnBol').click(function (e){            
            $('input').attr('required', false);
            $('div#preloader').show();
            e.preventDefault();
            var form_cust = $('#new_po')[0];
            let form1 = new FormData(form_cust);
           
            var values = $('#purchase_order_products').DataTable().data().toArray();
            var errorFlag= 0;
            var pattern = ['/','?','+','*',','];
            for (i in values) {
                let bol = String(values[i].bol);
                pattern.forEach(function(word){
                    errorFlag = errorFlag + bol.includes(word);
                });
            }
            if(errorFlag > 0){
                toastr.error("/ ?  *  +  , can't be use for BOL Number.");
                $('div#preloader').hide();
                return false;
            }

            var warehouseId = document.getElementById("warehouse").value;
            form1.append('supplier_id', '{{ isset($row) ? $row->id : '' }}');
            form1.append('client_id', '{{ isset($c_row) ? $c_row->id : '' }}');
            form1.append('summary_id', '{{ $ps->id }}')
            form1.append('po', '{{ $ps->order }}')

            var url = '/purchase_order/submit_asn_bol';

            var table = document.getElementById('purchase_order_products');
            var items = [];
            
            

            for (i in values) {
                console.log(items,values);
                var asn_bol_shipped_qty = String(values[i].asn_bol_shipped_qty);
                var bol = String(values[i].bol);
                var asn_qty = String(values[i].asn_qty);
                var asn = String(values[i].asn);
                items.push({
                    'etin': values[i].ETIN,
                    'order_qty': String(values[i].order_qty),
                    'asn_bol_shipped_qty': !asn_bol_shipped_qty 
                                                || asn_bol_shipped_qty.trim() === '' 
                                                || asn_bol_shipped_qty.trim().length === 0
                                                ? "NULL" : asn_bol_shipped_qty,
                    'bol': bol === '-' ? '' : bol ,
                    'asn_qty': !asn_qty 
                                                || asn_qty.trim() === '' 
                                                || asn_qty.trim().length === 0
                                                ? "NULL" : asn_qty,
                    'asn': asn === '-' ? '' : asn ,
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
                    var errors = data.responseJSON;
                    $("#error_container").html('');
                    $.each( errors.errors.original, function( key, value ) {
                        var ele = "#"+key;
                        $(ele).addClass('error_border');
                        $('<label class="error">'+ value +'</label>').insertAfter(ele);
                        $("#error_container").append("<p class='bg-danger mb-1 text-white p-1'>"+ value +"</p>");
                        toastr.error(value);
                    });
                }
            });
         });

    </script>
@endsection
