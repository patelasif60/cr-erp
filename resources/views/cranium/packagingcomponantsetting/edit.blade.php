@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection
@section('main-content')
    @php $required_span = '<span class="text-danger">*</span>' @endphp
    <div class="breadcrumb">
        <h1>Configure Box Components</h1>
        <ul>
            <li><a href="">Configure Box Components</a></li>
            <li>Edit</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <form method="POST" id="add_components_setting">
                    <div class="card-header bg-transparent">
                        <h6 class="card-title task-title">Box Components by Temperature</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="template_name" class="ul-form__label">Package Material Name:<?php echo $required_span; ?></label>
                                <input type="text" disabled required class="form-control" id="template_name" name="template_name" value="{{$desc}}">
                            </div>
                            <div class="card-body table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                        @foreach($producttemp as $key=>$value)
                                            <th class="table_border" style="background-color:#92D050">{{$value}} <a href="javascript:void(0)" onClick="editTempPack({{$key}})" class="btn btn-primary btn-sm">Edit</a>
                                            </th>
                                        @endforeach
                                        </tr>
                                        
                                    </thead>
                                    <tbody>
                                        <tr>
                                            @foreach($producttemp as $producttempKey=>$producttempValue)
                                                <td style="vertical-align: top">
                                                    <table class="table">
                                                        <tr>
                                                            <td class="table_border">Material</td>
                                                            <td class="table_border">Quantity</td>
                                                        </tr>
                                                        @foreach($componentsSettings as $componentsKey=>$componentsVal)
                                                            @if($producttempKey == $componentsVal->product_temperature_id)
                                                                <tr>
                                                                    <td class="table_border">{{$componentsVal->PackagingMaterials->product_description}}</td>
                                                                    <td  class="table_border">{{$componentsVal->qty}}</td>
                                                                </tr>
                                                            @endif
                                                            
                                                        @endforeach
                                                    </table>
                                                </td>
                                            @endforeach
                                        </tr>
                                        
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <a href="{{ route('packagingcomponant.index') }}" class="btn btn-outline-secondary m-1">Cancel</a>
                            </div>
                        </div>
                        <div class="row js-editTemp d-none">
                            <div class="card-header bg-transparent">
                                <h6 class="card-title task-title">Configuration & Packaging Details</h6>
                            </div>
                        </div>
                        <div class="row js-editTemp mt-4 d-none">
                            <div class="col-lg-7">
                                <input type="hidden" id="search_dt">
                                <p><b>Select Components</b></p>
                                <table class="table table-border table-stripped dataTable_filter" id="parent_kit_packaging">
                                    <thead>
                                        <tr>
                                            <td>ETIN</td>
                                            <td>Product Description</td>
                                            <td>Action</td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="col-lg-5">
                                <p><b>Selected Components</b></p>
                                <input type="hidden" id="selected_packages" value="">
                                <input type="hidden" id="unit_in_pack" value="0" name="unit_in_pack">
                                <input type="hidden" id="parent_packaging_id" name="parent_packaging_id" value="{{$id}}">
                                <input type="hidden" id="temp_id" name="temp_id" value="">
                                <table class="table table-border table-stripped" id="parent_kit_packaging_selected">
                                    <thead>
                                        
                                        <tr>
                                            <td>ETIN</td>
                                            <td>Product Description</td>
                                            <td>Qty</td>
                                            <td>Action</td>
                                        </tr>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent js-editTemp d-none">
                        <div class="mc-footer">
                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn  btn-primary m-1">Submit</button>
                                    <a href="{{ route('packagingcomponant.index') }}" class="btn btn-outline-secondary m-1">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="selected_packages_qty" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header-->
                <div class="modal-header" style="background-color:#fff;">
                    <h3>Packaging Quantity</h3>
                    <!--Close/Cross Button-->
                    <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
                </div>


                    <div class="modal-body">
                        <label>Qty</label>
                        <input type="number" name="qty" class="form-control" id="qty" style="width:100%;" required/>
                        <input type="hidden" id="pack_id">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="add_pck_qty">Add</button>
                        <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a>
                    </div>

            </div>
        </div>
    </div>
    <!-- end of col -->
@endsection
@section('page-js')
<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script src="{{ asset('assets/js/validation/jquery.validate.js') }}"></script>
<script src="{{ asset('assets/js/validation/additional-methods.min.js') }}"></script>
<script type="text/javascript">
    var table;
    $(document).ready(function () {
        var element = {};
        element[<?=$id?>] = 0;
        $('#selected_packages').val(JSON.stringify(element));
    })
    getSupplierPackaging = () =>  {
        table = $('#parent_kit_packaging').DataTable({
        destroy: true,
        responsive: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax:{
                url: '{{ route('packagingcomponant.packagingcompnentslist') }}',
                method:'GET',
                data: function(d){
                    d.ids = $("#selected_packages").val();
                    d.type = 'settings';
                }            
            },
        rowId: 'id',
        columns: [
            {data: 'ETIN', name: 'ETIN',defaultContent:'-'},
            {data: 'product_description', name: 'product_description',defaultContent:'-'},
            {data: 'action', name: 'action',searchable:false},
        ],
        oLanguage: {
            "sSearch": "Search:"
        },
        });
    }
    openQtyModal = (id) =>{
        $("#selected_packages_qty").modal('show');
        $("#pack_id").val(id);
    }
    $("#add_pck_qty").click(function(e){
        var pack_id = $("#pack_id").val();
        var qty = $("#qty").val();
        if(qty === ''){
            var ele = "#qty";
            var value = 'Qty can not be empty';
            $(ele).addClass('error_border');
            $('<label class="error">'+ value +'</label>').insertAfter(ele);
            $("#error_container").append("<p class='bg-danger mb-1 text-white p-1'>"+ value +"</p>");
            toastr.error(value);
            return false;
        }
        if($('#selected_packages').val()=='' || $('#selected_packages').val() == null)
        {
            var element = {};
            element[pack_id] = qty;
            $('#selected_packages').val(JSON.stringify(element));    
        }
        else{
            selectedId = $('#selected_packages').val();
            obj = JSON.parse(selectedId);
            obj[pack_id] = qty;
            $('#selected_packages').val(JSON.stringify(obj));
        }
        $("#pack_id").val('');
        $("#qty").val('');
        $("#qty").removeClass('error_border');
        $('label.error').remove();
        
        etin = $("#"+pack_id).find("td:eq(0)").text()
        pack_detail = $("#"+pack_id).find("td:eq(1)").text()
        GetParentSelecedProducts(etin,pack_detail,qty,pack_id);
        $('#'+pack_id).remove();
        $("#selected_packages_qty").modal('hide');
        table.draw(false);

    })
    GetParentSelecedProducts = (etin,pack_detail,qty,pack_id) =>{
        html = '<tr id="'+pack_id+'"><td>'+etin+'</td><td>'+pack_detail+'</td><td><input type="number" class="form-control" name="components['+pack_id+']" value="'+qty+'" id="components_qty" style="width:55px;padding:0px"></td><td><a href="javascript:void(0)" class="btn btn-danger" onClick="removeProduct('+pack_id+')">Delete</a></td></tr>'
        $("#parent_kit_packaging_selected tbody").append(html);
    }
    removeProduct = (id) => {
        selectedId = $('#selected_packages').val();
        obj = JSON.parse(selectedId);
        delete obj[id];
        $('#selected_packages').val(JSON.stringify(obj));
        $('#'+id).remove();
        table.draw(false);
    }
    editTempPack = (tempId) =>{
        $(".js-editTemp").removeClass('d-none');
        $("#temp_id").val(tempId)

         $.ajax({
            type: "GET",
            url: '{{route('packagingcomponant.gettempcomponents')}}',
            data: {'tempId' : tempId ,'parentId' : <?=$id?>},
            success: function( response ){
               $("#parent_kit_packaging_selected tbody").html(response);
               $('#selected_packages').val($('#selectedPack').val());
               getSupplierPackaging();
            }
        })
    }
    $("#add_components_setting").validate({
        submitHandler(form){
            $(".submit").attr("disabled", true);
            $('div#preloader').show();
            var form_cust = $('#add_components_setting')[0];
            let form1 = new FormData(form_cust);
            $.ajax({
                type: "POST",
                url: '{{route('packagingcomponants.update',$id)}}',
                data: form1,
                processData: false,
                contentType: false,
                success: function( response ) {
                    $('div#preloader').hide();
                    if(response.error == false){
                        toastr.success(response.msg);
                        //window.location.href= response.url;
                        //window.location.reload();
                    }else{
                        toastr.error(response.msg);
                    }
                },
                error: function(data){
                    $(".submit").attr("disabled", false);
                    $('div#preloader').hide();
                    var errors = data.responseJSON;
                    $("#error_container").html('');
                    $.each( errors.errors, function( key, value ) {
                        var ele = "#"+key;
                        $(ele).addClass('error_border');
                        $('<label class="error">'+ value +'</label>').insertAfter(ele);
                        $("#error_container").append("<p class='bg-danger mb-1 text-white p-1'>"+ value +"</p>");
                        toastr.error(value);
                    });
                }
            })
            $(document).ajaxStop(function(){
                window.location.reload();
            });
            return false;
        }
    });
</script>
@endsection