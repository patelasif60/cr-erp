@extends('layouts.master')

@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
    <link rel="stylesheet" href="https://phpcoder.tech/multiselect/css/jquery.multiselect.css">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
    <style>
        .error{
            color:red;
        }
        .ms-options ul{
            margin:0;
            padding:0px;

        }

        .ms-options-wrap > button:focus, .ms-options-wrap > button{
            color: #000 !important;
        }
    </style>
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Configuration</h1>
        
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-justified">
                        <li class="nav-item">
                            <a class="nav-link active" href="#tab_order_processing_group" id="order_processing_group_tab" role="tab" aria-controls="order_processing_group_tab" area-selected="true" data-toggle="tab">Order Processing</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_method_of_pick_pack" id="method_of_pick_pack_tab" role="tab" aria-controls="method_of_pick_pack_tab" area-selected="false" data-toggle="tab">Method of Pick/Pack </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_exp_lot_management" id="exp_lot_management_tab" role="tab" aria-controls="exp_lot_management_tab" area-selected="false" data-toggle="tab">Expiration & Lot Management</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_picker_configuration" id="picker_configuration_tab" role="tab" aria-controls="picker_configuration_tab" area-selected="false" data-toggle="tab">Picker Configuration</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#tab_order_shipping_eligibility" id="order_shipping_eligibility_tab" role="tab" aria-controls="order_shipping_eligibility_tab" area-selected="false" data-toggle="tab">Order Day Shipping Eligibility</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tab_hot_route" id="hot_route_tab" role="tab" aria-controls="hot_route_tab" area-selected="false" data-toggle="tab">Hot Route</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab_order_processing_group" role="tabpanel" area-labelledby="order_processing_group_tab">
                            @include('cranium.wmsconfig.order_processing_groups')
                        </div>

                        <div class="tab-pane fade" id="tab_method_of_pick_pack" role="tabpanel" area-labelledby="method_of_pick_pack_tab">
                            @include('cranium.wmsconfig.method_of_pick_pack')
                        </div>
                        <div class="tab-pane fade" id="tab_exp_lot_management" role="tabpanel" area-labelledby="exp_lot_management_tab">
                            @include('cranium.wmsconfig.exp_lot_management')
                        </div>
                        <div class="tab-pane fade" id="tab_picker_configuration" role="tabpanel" area-labelledby="picker_configuration_tab">
                            @include('cranium.wmsconfig.picker_configuration')
                        </div>
                        <div class="tab-pane fade" id="tab_order_shipping_eligibility" role="tabpanel" area-labelledby="order_shipping_eligibility_tab">
                            @include('cranium.wmsconfig.order_shipping_eligibility')
                        </div>
                        <div class="tab-pane fade" id="tab_hot_route" role="tabpanel" area-labelledby="hot_route_tab">
                            @include('cranium.wmsconfig.hot_route_config')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="MyModalProcessingGroup">
    </div>

    <!-- end of col -->
@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script src="https://phpcoder.tech/multiselect/js/jquery.multiselect.js"></script>
    <script src="{{ asset('assets/js/validation/jquery.validate.js') }}"></script>
    <script src="{{ asset('assets/js/validation/additional-methods.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            GetOrderProcessingGroup();
            GetPickPackMethod();
            //Getpicker();
            GetSupplierData();
            GetClientData();
            globObject = null;
            getHotRouteList();
        });
        function GetOrderProcessingGroup(){
            var table = $('#order_process_datatable').DataTable({
                paging:   true,
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                        url: '{{ route('getorderprocessdata') }}',
                    },
                columns: [
                    {data: 'group_name', name: 'group_name'},
                    {data: 'group_details', name: 'group_details'},
                    {data: 'action', name: 'Action', orderable: false},
                ],
            });
        }

        function GetModelWMSConfig(url){
            $.ajax({
                url:url,
                method:'GET',
                success:function(res){
                    $("#MyModalProcessingGroup").html(res);
                    $("#MyModalProcessingGroup").modal();
                }
            });
        }

        function GetPickPackMethod(){
            var table = $('#getpickPackData').DataTable({
                paging:   true,
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                        url: '{{ route('getpickPackData') }}',
                    },
                columns: [
                    {data: 'client_name', name: 'client_name'},
                    {data: 'action', name: 'Action', orderable: false},
                ],
            });
        }

        function Getpicker(werid=null){
            var table = $('#picker_datatable').DataTable({
                paging:   true,
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                        url: '{{ route('getpickerconf') }}',
                        data: {
                            werid : werid,
                        },
                    },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'orderProcessname', name: 'orderProcessname'},
                    {data: 'batch_max_until_2pm', name: 'batch_max_until_2pm'},
                    {data: 'batch_max_2pm_to_4pm', name: 'batch_max_2pm_to_4pm'},
                    {data: 'batch_max_after_4pm', name: 'batch_max_after_4pm'},
                    {data: 'action', name: 'Action', orderable: false},
                ],
            });
        }
        function GetModelpicker(url){
            $.ajax({
                url:url,
                method:'GET',
                success:function(res){
                    $("#MyModalPicker").html(res);
                    $("#MyModalPicker").modal();
                }
            });
        }
        function shippingElgibility(){
            if($("#warehouses").val() > 0)
            {
                wareId = $("#warehouses").val();
                $.ajax({
                    type: "POST",
                    url: '{{route('shipping_eligibility')}}',
                    data: {warehouse : wareId},
                    success: function( response ) {
                        $("#myshipel").html(response);
                        $(".js-shipping").removeClass('d-none')
                    },
                })
            }
        }
        $("#edit_shipping_elegibility_form").validate({
            submitHandler(form){
                $(".submit").attr("disabled", true);
                var form_cust = $('#edit_shipping_elegibility_form')[0]; 
                let form1 = new FormData(form_cust);
                $.ajax({
                    type: "POST",
                    url: '{{ route('update_shipping_eligiblity') }}',
                    data: form1, 
                    processData: false,
                    contentType: false,
                    success: function( response ) {
                        if(response.error == 0){
                            toastr.success(response.msg);
                        }else{
                            $(".submit").attr("disabled", false);
                            toastr.error(response.msg);
                            
                            setTimeout(function(){
                             //location.reload();
                             globObject.value = '';
                            },2000);
                        }
                    },
                    error: function(data){
                        $(".submit").attr("disabled", false);
                        var errors = data.responseJSON;
                        $.each( errors.errors, function( key, value ) {
                            var ele = "#"+key;
                            $(ele).addClass('error');
                            $('<label class="error">'+ value +'</label>').insertAfter(ele);
                        });
                        
                    }
                })
                return false;
            }
        });
        function updateShipping(frm){
           globObject = frm;
           $("#edit_shipping_elegibility_form").submit();
        }

        function GetSupplierData(){
            var table = $('#supplier_data_datatable').DataTable({
                paging:   true,
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                        url: '{{ route('getSupplierData') }}',
                    },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'action', name: 'Action', orderable: false},
                ],
            });
        }

        function GetClientData(){
            var table = $('#client_data_datatable').DataTable({
                paging:   true,
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                        url: '{{ route('getClientData') }}',
                    },
                columns: [
                    {data: 'company_name', name: 'name'},
                    {data: 'action', name: 'Action', orderable: false},
                ],
            });
        }


        function UpdateExpStatus($this,url){
            var status = $($this).val();
            $.ajax({
                url:url,
                method:'POST',
                dataType:'JSON',
                data:{status:status},
                success:function(res){
                    toastr.success(res.msg);
                    GetSupplierData();
                }
            });   
            
        }

        function UpdateClientExpStatus($this,url){
            var status = $($this).val();
            $.ajax({
                url:url,
                method:'POST',
                dataType:'JSON',
                data:{status:status},
                success:function(res){
                    toastr.success(res.msg);
                    GetClientData();
                }
            });   
        }

        function DeletePickPackMethod(url){
            if(confirm('Are you sure you want to delete this?')){
                $.ajax({
                    url:url,
                    method:'GET',
                    success:function(res){
                        toastr.success(res.msg);
                        GetSupplierData();
                    }
                });   
            }
        }

        function pickerconfi(){
           if($("#pickwarehouses").val() > 0)
            {
                wareId = $("#pickwarehouses").val();
                Getpicker(wareId);
                $(".js-picker-conf").removeClass('d-none');
                // $.ajax({
                //     type: "POST",
                //     url: '{{route('shipping_eligibility')}}',
                //     data: {warehouse : wareId},
                //     success: function( response ) {
                //         $("#myshipel").html(response);
                //         $(".js-picker-conf").removeClass('d-none')
                //     },
                // })
            } 
        }

        function getHotRouteList(){
            var table = $('#hot_route_datatable').DataTable({
                paging:   true,
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                lengthMenu: [[10, 25, 100, -1], [10, 25, 100, "All"]],
                ajax:{
                        url: '{{ route('get_hot_route_list') }}',
                },
                columns: [
                    {
                        data: '', name: '',
                        render: function (data, type, row, meta){
                            return '<input type="checkbox" name="hr_id" value=' + row.id + ' onclick=\'enableDisableDeleteSelected();\'>';
                        },
                        orderable: false
                    },
                    {data: 'warehouses', name: 'warehouses'},
                    {data: 'company_name', name: 'company_name'},
                    {data: 'zip', name: 'Zip'},
                    {data: 'transit_days', name: 'Transit Days'},
                    {data: 'cut_off_time', name: 'Cut-Off Time'},
                    {data: 'action', name: 'Action'},
                ],
            });
        }
        
        function enableDisableDeleteSelected() {
            var selectedCount = document.querySelectorAll('input[name=hr_id]:checked').length;
            if (selectedCount <= 0) {
                $('#sel_del').attr("disabled", 'disabled');
            } else {
                $('#sel_del').removeAttr("disabled");
            }
        }

        function saveHotRoute(){

            var wh = $('#wh_td').val();
            if (!wh || wh == '') { toastr.error('Warehouse is mandatory.'); return; }
            
            var carrier = $('#carrier_type').val();
            if (!carrier || carrier == '') { toastr.error('Carrier is mandatory.'); return; }
            
            var zipCodes = $('#zip_codes').val();
            if (!zipCodes || zipCodes == '') { toastr.error('Zipcode(s) is mandatory.'); return; }

            var codes = zipCodes.replace(/\n|\r|\s+/g,'').split(',');
            for(code of codes) { if (code.length != 5) { toastr.error('Each Zipcode should be of 5 digits only.'); return; } }
            
            var tDay = $('#t_day').val();
            if (!tDay || tDay == '') { toastr.success('Transit Day(s) is mandatory.'); return; }
            
            var cutOffTime = $('#cut_off_time').val();            

            var form = new FormData();
            form.append('wh_td', wh);
            form.append('carrier_type', carrier);
            form.append('zip_codes', codes.join(','));
            form.append('transit_days', tDay);
            form.append('cut_off_time', cutOffTime);

            $.ajax({
                url: '/save_route',
                method: 'POST',
                data: form,
                processData: false,
                contentType: false,
                success: function(res){
                    if (res.error == 0) {                        
                        toastr.success(res.msg);
                        getHotRouteList();
                        $('#wh_td option:first').prop('selected', true).trigger( "change" );
                        $('#carrier_type option:first').prop('selected', true).trigger( "change" );
                        $('#cut_off_time option:first').prop('selected', true).trigger( "change" );
                        $('#zip_codes').val('');
                        $('#t_day').val('');
                    } else {
                        toastr.error(res.msg);
                    }
                }
            });
        }

        function deleteSelected() {
            var selectedRows = document.querySelectorAll('input[name=hr_id]:checked');
            var ids = [];
            for (var checkbox of selectedRows) {  
                if (checkbox.checked) { 
                    ids.push(checkbox.value);
                }
            }
            deleteRoute(ids.join(','));
        }

        function deleteRoute(id){

            swal({
				title: 'Are you sure you want to delete the route?',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Confirm'
			}).then((result) => {
                if(result) {
                    $.ajax({
                        url: '/delete_route/' + id,
                        method: 'DELETE',
                        processData: false,
                        contentType: false,
                        success: function(res){
                            if (res.error == 0) {                        
                                toastr.success(res.msg);
                                getHotRouteList();
                            } else {
                                toastr.error(res.msg);
                            }
                        }
                    });
                } else {
					return; 
				}
            });            
        }

        function editRoute(id) {
            $.ajax({
                url: '/get_route_by_id/' + id,
                method: 'GET',
                processData: false,
                contentType: false,
                success: function(res){
                    if (res.error == 0) {
                        $('#zip_codes').val(res.data.zip);
                        $('#t_day').val(res.data.transit_days);
                        $('#wh_td option[value='+res.data.wh_id+']').prop('selected', true).trigger("change");
                        $('#carrier_type option[value='+res.data.carrier_id+']').prop('selected', true).trigger("change");
                        $('#cut_off_time option[value="'+res.data.cut_off_time+'"]').prop('selected', true).trigger("change");
                    } else {
                        toastr.error(res.msg);
                    }
                }
            });
        }

    </script>
@endsection