@extends('layouts.master')

@section('page-css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/custom/css/custom.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
<link rel="stylesheet" href="https://phpcoder.tech/multiselect/css/jquery.multiselect.css">
<style>
    .ms-options > ul{
        list-style-type: none !important;
    }
    .ms-options-wrap > .ms-options{
        position: relative;
    }
    .table-responsive .dropdown-menu{
        /* position: relative; */
        min-width:300px;
        z-index: 950 !important;
        padding:10px;
    }
    .filter-input-text{
        width:200%;
    }
    .dropdown-menu.show1 {
        display: block;
    }


</style>
@endsection

@section('main-content')
<input type="hidden" value={{$row_id}} name="summery_id" id ="summery_id">
<div class="breadcrumb">
    <div class="form-group col-12">
        <h3>Approve Following Product</h3>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card js-location-approved">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-10">
                        <h3 class="w-50 float-left card-title m-0">Location Approved Product</h3>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="datatable2" class="table table-bordered text-center dataTable_filter">
                    <thead>
                        <tr>
                            <th scope="col" id="idclass">#</th>
                            <th><input type="checkbox" id="all_new_approve1" name="all_new_approve1" /></th>
                            <th scope="col">Location</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">UPC</th>
                            <th scope="col">Current Quantity</th>
                            <th scope="col">Counted Quantity</th>
                            <th scope="col">Exp/Lot NUmber</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <button id="newApprove" class="btn btn-primary ml-3 js-cycle" data-flag="1">Approve Selected</button>
                <button id="newReject" class="btn btn-primary ml-3 js-cycle" data-flag="0">Recount Selected</button>
            </div>           
        </div>       
    </div>
</div>
@endsection

@section('page-js')
<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>
    <script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>
    <script src="{{asset('assets/js/sweetalert.script.js')}}"></script>
    <script src="https://phpcoder.tech/multiselect/js/jquery.multiselect.js"></script>
<script>
    function GetWarehouseApprovedProducts(){
        summery_id = $("#summery_id").val();
         var csrfToken = $('meta[name="csrf-token"]').attr('content');
        table1 = $('#datatable2').DataTable({
            // dom:"Bfrtip",
            paging:   true,
            destroy: true,
            responsive: false,
            processing: true,
            serverSide: true,
            autoWidth: false,
            colReorder: true,
            searching:true,
            ajax:{
                    url: '{{route('awaitapprovedproducts')}}',
                    method:'POST',
                    data: {
                        cc_sum_id: summery_id,
                        _token: csrfToken,
                    },
                },
            lengthMenu: [[25,50, 100, 500], [25,50, 100, 500]],
            pageLength: 25,
            columns: [
                {data: 'id', name: 'id'},
                {data: 'approve_check', name: 'approve_check'},
                {data: 'address', name: 'Location', defaultContent:'-'},
                {data: 'product_listing_name', name: 'Product Name', defaultContent:'-'},
                {data: 'upc', name: 'UPC', defaultContent:'-'},
                {data: 'total_on_hand', name: 'Current Quantity', defaultContent:'-'},
                {data: 'total_counted', name: 'Counted Quantity', defaultContent:'-'},
                {data: 'upc', name: 'Exp/Lot NUmber', defaultContent:'-'},
            ],
             columnDefs: [
                 {
                    "targets": [ 0 ],
                    "visible": false
                },
                {
                    orderable: true,
                    targets: 2
                },
            ],
            order: [[2, 'asc']],
            fnInitComplete: function (oSettings, json) {
                $("#preloader").hide();
            }
        });
    };
 GetWarehouseApprovedProducts()
$('.js-cycle').click(function(){
    //flag = $(this).attr('data-flag');
    summery_id = $("#summery_id").val();
    var val = [];
    $('.newApproveCheckBox2:checked:not(:disabled)').each(function(i){
        val[i] = $(this).val();
    });
    if(val.length !== 0){
        textmsg = $(this).attr('data-flag')==1 ? 'Are You Sure approve cycle Count' : 'Are You Sure recount cycle Count';
        if(confirm(textmsg)){
            $.ajax({
                type: "POST",
                url: '{{route('approveawaitcyclecountproduct')}}',
                data: {
                    checked : val,
                    summery_id:summery_id,
                    flag:$(this).attr('data-flag')
                },
                success: function( response ) {
                    if(response.error == false){
                        toastr.success(response.msg);
                        setTimeout(function(){
                            window.location.href = "/cyclecount"
                        },2000);
                    }else{
                        toastr.error(response.msg);
                    }
                }
            });
        }
    }else{
        alert("Please Select Product To Approve");
    }
}); 
</script>
@endsection