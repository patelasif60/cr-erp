@extends('layouts.master')
@section('page-css')
    <style type="text/css">
        .Product_Cost{
            background-color: #CFE4FF;
            color: black;
            border-radius: 3px;
            padding: 5px;
            cursor: pointer;
            /*display: inline-block;*/
        }
        .Shipping_Cost{
            background-color: #FFDDA6;
            color: black;
            border-radius: 3px;
            padding: 5px;
            cursor: pointer;
            /*display: inline-block;*/
        }
        .Business_expenses{
            background-color: #FFD9D9;
            color: black;
            border-radius: 3px;
            padding: 5px;
            cursor: pointer;
            /*display: inline-block;*/
        }
        .Packaging_Matirial_Cost{
            background-color: #C3F7C8;
            color: black;
            border-radius: 3px;
            padding: 5px;
            cursor: pointer;
            /*display: inline-block;*/
        }
        .Mark_Of_Price_Group{
            background-color: #F4D9FF;
            color: black;
            border-radius: 3px;
            padding: 5px;
            cursor: pointer;
            /*display: inline-block;  */
        }
        .gray{
            background-color: gray;
            color: white;
            border-radius: 3px;
            padding: 5px;
        }
    </style>
@endsection
@section('main-content')
    @php $required_span = '<span class="text-danger">*</span>' @endphp
    <div class="breadcrumb">
        <h1>Price Group</h1>
        <ul>
            <li><a href="">Price Group</a></li>
            <li>New</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>
    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                 <form action="javascrpt:void(0)" id="add_price_group">
                    <div class="card-header bg-transparent">
                        <h6 class="card-title task-title">New Price Group</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="group_name" class="ul-form__label">Price Group Name:<?php echo $required_span; ?></label>
                                <input type="text" required class="form-control" id="group_name" name="group_name" placeholder="Enter Group Name">
                            </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="store_automator_id" class="ul-form__label">Store Automator ID:<?php echo $required_span; ?></label>
                                <input type="text" required class="form-control" id="store_automator_id" name="store_automator_id" placeholder="Enter Id">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="carrier_id" class="ul-form__label">Carriers:<?php echo $required_span; ?></label>
                                <select name="carrier_id" id="carrier_id" class="form-control" required>
                                    <option value="">Select Carrier</option>
                                    @if($carriers)
                                        @foreach ($carriers as $id => $val)
                                            <option value="{{ $id }}">{{ $val }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                            <div class="form-group col-md-6">
                                <label for="description" class="ul-form__label">Price Group Description:<?php echo $required_span; ?></label>
                                <textarea required name="description" id="description" cols="10" rows="3" class="form-control" placeholder="Enter Description"></textarea>
                            </div>
                            <div class="form-group col-md-6 mb-4">
                                <label for="group_type" class="ul-form__label">Price Group Type:<?php echo $required_span; ?></label>
                                <select class="form-control select2" id="group_type" name="group_type">
                                    <option value="">--Select--</option>
                                    <option value="3PL">3PL</option>
                                    <option value="Wholesale">Wholesale</option>
                                    <option value="In house">In house</option>
                                    <option value="dropship">dropship</option>
                                </select>
                            </div>
                            <h6 class="card-title col-md-12 mt-4 task-title">Package And Material Formula</h6>
                            <div class="form-group col-md-6 js-package_and_matirial">
                                <label for="group_formula" class="ul-form__label">Price Calculation & Formula For Package and material:<?php echo $required_span; ?></label>
                                 <select  onchange="showMultiply()" class="form-control select2" id="group_formula_package_and_matirial" name="group_formula['package_and_matirial']">
                                    <option value="Exact">Exact</option>
                                    <option value="Multiply">Multiply</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3 js-parcentage_package_and_matirial d-none">
                                <label for="group_formula" class="ul-form__label">Multiply Value:</label>
                                <input type="text" class="form-control" id="parcentage" name="parcentage['package_and_matirial']" placeholder="Enter Value">
                            </div>
                            <div class="form-group col-md-6 js-coolant_cost">
                                <label for="group_formula" class="ul-form__label">Price Calculation & Formula For Coolant Cost:<?php echo $required_span; ?></label>
                                 <select  onchange="showMultiply()" class="form-control select2" id="group_formula_coolant_cost" name="group_formula['coolant_cost']">
                                    <option value="Exact">Exact</option>
                                    <option value="Multiply">Multiply</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3 js-parcentage_coolant_cost d-none">
                                <label for="group_formula" class="ul-form__label">Multiply Value:</label>
                                <input type="text" class="form-control" id="parcentage" name="parcentage['coolant_cost']" placeholder="Enter Value">
                            </div>
                            <h6 class="card-title col-md-12 mt-4 task-title">Shipping Formula</h6>
                            <div class="form-group col-md-6 js-residential_surcharge">
                                <label for="group_formula" class="ul-form__label">Price Calculation & Formula For Residentail Surcharge:<?php echo $required_span; ?></label>
                                 <select  onchange="showMultiply()" class="form-control select2" id="group_formula_residential_surcharge" name="group_formula['residential_surcharge']">
                                    <option value="Exact">Exact</option>
                                    <option value="Multiply">Multiply</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3 js-parcentage_residential_surcharge d-none">
                                <label for="group_formula" class="ul-form__label">Multiply Value:</label>
                                <input type="text" class="form-control" id="parcentage" name="parcentage['residential_surcharge']" placeholder="Enter Value">
                            </div>
                            <div class="form-group col-md-6 js-remote_area_surcharge">
                                <label for="group_formula" class="ul-form__label">Price Calculation & Formula For Remote Area Surcharge:<?php echo $required_span; ?></label>
                                 <select  onchange="showMultiply()" class="form-control select2" id="group_formula_remote_area_surcharge" name="group_formula['remote_area_surcharge']">
                                    <option value="Exact">Exact</option>
                                    <option value="Multiply">Multiply</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3 js-parcentage_remote_area_surcharge d-none">
                                <label for="group_formula" class="ul-form__label">Multiply Value:</label>
                                <input type="text" class="form-control" id="parcentage" name="parcentage['remote_area_surcharge']" placeholder="Enter Value">
                            </div>
                            <div class="form-group col-md-6 js-delivary_area_surcharge">
                                <label for="group_formula" class="ul-form__label">Price Calculation & Formula For Delivary Area Surcharge:<?php echo $required_span; ?></label>
                                 <select  onchange="showMultiply()" class="form-control select2" id="group_formula_delivary_area_surcharge" name="group_formula['delivary_area_surcharge']">
                                    <option value="Exact">Exact</option>
                                    <option value="Multiply">Multiply</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3 js-parcentage_delivary_area_surcharge d-none">
                                <label for="group_formula" class="ul-form__label">Multiply Value:</label>
                                <input type="text" class="form-control" id="parcentage" name="parcentage['delivary_area_surcharge']" placeholder="Enter Parcentage ">
                            </div>
                            <div class="form-group col-md-6 js-extended_delivary_area_surcharge">
                                <label for="group_formula" class="ul-form__label">Price Calculation & Formula For Extended Delivary Area Surcharge:<?php echo $required_span; ?></label>
                                 <select  onchange="showMultiply()" class="form-control select2" id="group_formula_extended_delivary_area_surcharge" name="group_formula['extended_delivary_area_surcharge']">
                                    <option value="Exact">Exact</option>
                                    <option value="Multiply">Multiply</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3 js-parcentage_extended_delivary_area_surcharge d-none">
                                <label for="group_formula" class="ul-form__label">Multiply Value:</label>
                                <input type="text" class="form-control" id="parcentage" name="parcentage['extended_delivary_area_surcharge']" placeholder="Enter Parcentage ">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="weight_multiplier" class="ul-form__label">Weight Multiplier:</label>
                                <input type="number" class="form-control" id="weight_multiplier" name="weight_multiplier" placeholder="Enter Value">
                           </div>
                            <h6 class="card-title col-md-12 mt-4 task-title">Business Expensess Formula</h6>
                            <div class="form-group col-md-6">
                                <label for="credit_card_fees" class="ul-form__label">Credit Card Fees:</label>
                                <input type="number" class="form-control" id="credit_card_fees" name="credit_card_fees" placeholder="Enter Value">
                           </div>
                            <div class="form-group col-md-6">
                                <label for="marketplace_fees" class="ul-form__label">Marketplace Fees:</label>
                                <input type="number" class="form-control" id="marketplace_fees" name="marketplace_fees" placeholder="Enter Value">
                            </div>
                            <h6 class="card-title col-md-12 mt-4 task-title">PriceGroup Formula</h6>
                            <div class="form-group col-md-6">
                                <label for="markup_price_group" class="ul-form__label">% Markup: Price Group:</label>
                                <input type="number" class="form-control" id="markup_price_group" name="markup_price_group" placeholder="Enter Value">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="markup_total_cost" class="ul-form__label">% Markup: Total Cost:</label>
                                <input type="number" class="form-control" id="markup_total_cost" name="markup_total_cost" placeholder="Enter Value">
                           </div>
                            <div class="form-group col-md-6">
                                <label for="markup_product_materials_cost" class="ul-form__label">% Markup: Product & Materials Cost:</label>
                                <input type="number" class="form-control" id="markup_product_materials_cost" name="markup_product_materials_cost" placeholder="Enter Value">
                            </div>
                            <div class="form-group col-md-6"></div>
                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="row form-group col-md-6">
                                    <div class="form-group col-md-6">
                                        <label for="lobs" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Clients & sites the product is assigned to">Clients & Sites Not Assigned</label>
                                        <div class="custom_one_line_cards_container LobsDrop border">
                                            @if ($client)
                                                @foreach($client as $key=>$value)
                                                    @if(isset($req['client_id']))
                                                    @else
                                                        <div class="lobs_cards custom_one_line_cards" id="{{ $key }}">{{ $value }}</div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="hidden" name="lobs" id="lobs" value="@if(isset($req['client_id'])){{$req['client_id']}}@endif">
                                        <label for="lobs" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Clients & sites the product is assigned to">Clients & Sites Assigned <span class="text-danger">*</span></label>
                                        <div class="custom_one_line_cards_container LobsDropAssigned border">
                                            @if ($client)
                                                @foreach($client as $key=>$value)
                                                    @if(isset($req['client_id']))
                                                        @if($req['client_id'] == $key)
                                                            <div class="lobs_cards custom_one_line_cards" id="{{ $key }}">{{ $value }}</div>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <input type="hidden" name="chanel_ids" id="chanel_ids" value="">
                                    <div class="row"  id="client_channel_container"></div>
                                </div>
                            </div>
                            

                            <!-- <div class="form-group col-md-6 ml-16">
                                <div class="row p-5">
                                    @foreach($masterCost as $key=>$val)
                                        @if($val->type == 'Cost')
                                        <div class="col-md-6">
                                            <div class="card mb-4" style="max-height: 376px;overflow-y: auto;">
                                                <div class="card-header {{str_replace(' ','_',$val->name)}}">
                                                    <h3 class="w-50 float-left card-title m-0">{{$val->name}}</h3>
                                                </div>
                                                <div class="card-body col-md-12"  ondrop="drop(event)" ondragover="allowDrop(event)" id="mstr{{$val->id}}">
                                                    @foreach($val->subCost as $subKey => $subVal )
                                                    @if($subVal->id ==4 || $subVal->id ==13 || $subVal->id ==14 || $subVal->id ==15 || $subVal->id ==18 || $subVal->id ==20 || $subVal->id ==21 || $subVal->id ==22 || $subVal->id ==23)
                                                        <div style="height: 50px"  class="border border-info mt-2 mb-4 {{str_replace(' ','_',$val->name)}} ml-16" data-id="{{$subVal->id}}"  data-type="add" id="sub{{$subVal->id}}">
                                                                {{$subVal->cost_name}}
                                                        </div>
                                                    @else
                                                        <div style="height: 50px" draggable="true" class="border border-info mt-2 mb-4 {{str_replace(' ','_',$val->name)}} ml-16" data-id="{{$subVal->id}}" ondragstart="drag(event)" data-type="add" id="sub{{$subVal->id}}">
                                                                {{$subVal->cost_name}}
                                                        </div>
                                                    @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                    @foreach($masterCost as $key=>$val)
                                        @if($val->type == 'Pricing')
                                        <div class="col-md-6 mt-4">
                                            <div class="card mb-4" style="max-height: 376px;overflow-y: auto;">
                                                <div class="card-header {{str_replace(' ','_',$val->name)}}">
                                                    <h3 class="w-50 float-left card-title m-0">{{$val->name}}</h3>
                                                </div>
                                                <div class="card-body col-md-12"  ondrop="drop(event)" ondragover="allowDrop(event)" id="mstr{{$val->id}}">
                                                    @foreach($val->subCost as $subKey => $subVal )
                                                    <div style="height: 50px"  class="border border-info mt-2 mb-4 {{str_replace(' ','_',$val->name)}} ml-16" data-id="{{$subVal->id}}" data-type="add" id="sub{{$subVal->id}}">
                                                            {{$subVal->cost_name}}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div> -->
                            <input type="hidden" readonly name="sub_cost_id" id="sub_cost_id" value="">
                            <!-- <div class="col-md-1"></div>
                            <div class="col-md-4 border border-success" ondrop="drop(event)" ondragover="allowDrop(event)" id="drag4">
                            </div> -->
                            <div class="form-group col-md-6">
                                <label for="lobs" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Clients & sites the product is assigned to">Cost & Price Block Not Assigned</label>
                                <div class="row p-5">
                                @foreach($masterCost as $key=>$val)
                                    <div class="col-md-6">
                                        <div class="card mb-4" style="max-height: 337px;overflow-y: auto;">
                                            <div class="card-header {{str_replace(' ','_',$val->name)}}">
                                                <h3 class="w-50 float-left card-title m-0">{{$val->name}}</h3>
                                            </div>
                                            <div class="drop mastercost{{$val->id}}">
                                                @foreach($val->subCost as $subKey => $subVal )
                                                    @if($subVal->cost_formula)
                                                    <div style="height: 50px" class="js-cost-click border border-info mt-2 mb-4 {{str_replace(' ','_',$val->name)}} ml-16 mr-16" data-msterId="{{$val->id}}" data-id="{{$subVal->id}}" id="sub{{$subVal->id}}">
                                                        {{$subVal->cost_name}}
                                                    </div>
                                                    @else
                                                    <div style="height: 50px" class="border border-info mt-2 mb-4 gray ml-16 mr-16" data-id="{{$subVal->id}}"  data-type="add" id="sub{{$subVal->id}}">
                                                        {{$subVal->cost_name}}
                                                    </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="lobs" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Clients & sites the product is assigned to">Cost & Price Block Assigned <span class="text-danger">*</span></label>
                                <div class="js-drop-assigned border">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="mc-footer">
                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary m-1">Submit</button>
                                    <a href="{{ route('pricegroup.index') }}" class="btn btn-outline-secondary m-1">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('page-js')
<script src="{{ asset('assets/js/validation/jquery.validate.js') }}"></script>
<script src="{{ asset('assets/js/validation/additional-methods.min.js') }}"></script>
<script type="text/javascript">
    $('.drop ').on('click','.js-cost-click',function(e){
        var dropped= $(this).attr('data-id');
        var assigned = $("#sub_cost_id").val();
        var array = [];
        if(assigned == ''){
            array.push(dropped);
            $("#sub_cost_id").val(array.join(','));
        }else{
            array = assigned.split(',');
            array.push(dropped);
            $("#sub_cost_id").val(array.join(','));
        }
        changeGrid(dropped,'add')
        $('.js-drop-assigned').append(this);
    });
     $(".js-drop-assigned").on('click','.js-cost-click',function(e){
        var dropped = $(this).attr('data-id');
        var masterId = $(this).attr('data-msterId');
        var assigned = $("#sub_cost_id").val();
        var array = [];
        if(assigned == ''){

        }else{
            array = assigned.split(',');
            array.splice($.inArray(dropped, array), 1);
            $("#sub_cost_id").val(array.join(','));
        }
        changeGrid(dropped,'sub')
        $('.mastercost'+masterId).append(this);
    });
    $('.LobsDrop ').on('click','.lobs_cards',function(e){
        var dropped_lobs = $(this).attr('id');
        var lobs_assigned = $("#lobs").val();
        var lobs_array = [];
        if(lobs_assigned == ''){
            lobs_array.push(dropped_lobs);
            $("#lobs").val(lobs_array.join(','));
        }else{
            lobs_array = lobs_assigned.split(',');
            lobs_array.push(dropped_lobs);
            $("#lobs").val(lobs_array.join(','));
        }

        $('.LobsDropAssigned').append(this);
        GetClientChanel();
    });

    $(".LobsDropAssigned").on('click','.lobs_cards',function(e){
        var dropped_lobs = $(this).attr('id');
        console.log(dropped_lobs);
        var lobs_assigned = $("#lobs").val();
        var lobs_array = [];
        if(lobs_assigned == ''){

        }else{
            lobs_array = lobs_assigned.split(',');
            lobs_array.splice($.inArray(dropped_lobs, lobs_array), 1);
            $("#lobs").val(lobs_array.join(','));
        }
        $('.LobsDrop').append(this);
        GetClientChanel();
    });
    function allowDrop(ev) {

     // ev.preventDefault();
    }

    function drag(ev) {
      // id = ev.target.id
      // selectId = $('#'+id).attr('data-id')
      // type = $('#'+id).attr('data-type')
      // ev.dataTransfer.setData("id", ev.target.id);
      // ev.dataTransfer.setData("selectId", selectId);
      // ev.dataTransfer.setData("type", type);
    }

    function drop(ev) {
     // ev.preventDefault();

      // var data = ev.dataTransfer.getData("id");
      // var type = ev.dataTransfer.getData("type");
      // var selectId = ev.dataTransfer.getData("selectId");
      // var selectId_array = $("#sub_cost_id").val();
      // console.log("screenX: "+ev.screenX)
      // console.log("screenY: "+ev.screenY)
      // var id_array = [];
      // if(type=='add')
      // {
      //   if(selectId_array == ''){
      //       id_array.push(selectId);
      //       $("#sub_cost_id").val(id_array.join(','));
      //   }else{
      //       id_array = selectId_array.split(',');
      //       id_array.push(selectId);
      //       $("#sub_cost_id").val(id_array.join(','));
      //   }
      //   $('#'+data).attr('data-type','sub')
      //   changeGrid(selectId,'sub');
      // }
      // if(type=='sub'){
      //    id_array = selectId_array.split(',');
      //    index = id_array.indexOf(selectId);
      //   if (index > -1) {
      //      id_array.splice(index, 1);
      //   }
      //   $("#sub_cost_id").val(id_array.join(','));
      //   $('#'+data).attr('data-type','add')
      //   // changeGrid(selectId,'add');
      // }
      //     // sum = $("#sum").val(tot);
      //     console.log(ev.target)

      // ev.target.appendChild(document.getElementById(data));
    }
    showMultiply = () => {
        if($('#group_formula_package_and_matirial').val() == 'Multiply'){
            $('.js-package_and_matirial').removeClass('col-md-6');
            $('.js-package_and_matirial').addClass('col-md-3');
            $('.js-parcentage_package_and_matirial').removeClass('d-none');
        }
        else{
            $('.js-package_and_matirial').removeClass('col-md-3');
            $('.js-package_and_matirial').addClass('col-md-6');
            $('.js-parcentage_package_and_matirial').addClass('d-none');
        }
        if($('#group_formula_coolant_cost').val() == 'Multiply'){
            $('.js-coolant_cost').removeClass('col-md-6');
            $('.js-coolant_cost').addClass('col-md-3');
            $('.js-parcentage_coolant_cost').removeClass('d-none');
        }
        else{
            $('.js-coolant_cost').removeClass('col-md-3');
            $('.js-coolant_cost').addClass('col-md-6');
            $('.js-parcentage_coolant_cost').addClass('d-none');
        }
        if($('#group_formula_residential_surcharge').val() == 'Multiply'){
            $('.js-residential_surcharge').removeClass('col-md-6');
            $('.js-residential_surcharge').addClass('col-md-3');
            $('.js-parcentage_residential_surcharge').removeClass('d-none');
        }
        else{
            $('.js-residential_surcharge').removeClass('col-md-3');
            $('.js-residential_surcharge').addClass('col-md-6');
            $('.js-parcentage_residential_surcharge').addClass('d-none');
        }
        if($('#group_formula_remote_area_surcharge').val() == 'Multiply'){
            $('.js-remote_area_surcharge').removeClass('col-md-6');
            $('.js-remote_area_surcharge').addClass('col-md-3');
            $('.js-parcentage_remote_area_surcharge').removeClass('d-none');
        }
        else{
            $('.js-remote_area_surcharge').removeClass('col-md-3');
            $('.js-remote_area_surcharge').addClass('col-md-6');
            $('.js-parcentage_remote_area_surcharge').addClass('d-none');
        }
         if($('#group_formula_delivary_area_surcharge').val() == 'Multiply'){
            $('.js-delivary_area_surcharge').removeClass('col-md-6');
            $('.js-delivary_area_surcharge').addClass('col-md-3');
            $('.js-parcentage_delivary_area_surcharge').removeClass('d-none');
        }
        else{
            $('.js-delivary_area_surcharge').removeClass('col-md-3');
            $('.js-delivary_area_surcharge').addClass('col-md-6');
            $('.js-parcentage_delivary_area_surcharge').addClass('d-none');
        }
         if($('#group_formula_extended_delivary_area_surcharge').val() == 'Multiply'){
            $('.js-extended_delivary_area_surcharge').removeClass('col-md-6');
            $('.js-extended_delivary_area_surcharge').addClass('col-md-3');
            $('.js-parcentage_extended_delivary_area_surcharge').removeClass('d-none');
        }
        else{
            $('.js-extended_delivary_area_surcharge').removeClass('col-md-3');
            $('.js-extended_delivary_area_surcharge').addClass('col-md-6');
            $('.js-parcentage_extended_delivary_area_surcharge').addClass('d-none');
        }
    }
    changeGrid = (selectId,type) =>{
        if(selectId == 1)
        {
            if(type == 'sub'){
                $('#sub2').addClass('js-cost-click');
                $('#sub3').addClass('js-cost-click');
                $('#sub2').removeClass('gray');
                $('#sub3').removeClass('gray');
                $('#sub2').addClass('Product_Cost');
                $('#sub3').addClass('Product_Cost');
            }
            else{
                $('#sub2').removeClass('js-cost-click');
                $('#sub3').removeClass('js-cost-click');
                $('#sub2').addClass('gray');
                $('#sub3').addClass('gray');
                $('#sub2').removeClass('Product_Cost');
                $('#sub3').removeClass('Product_Cost');
            }
        }
        if(selectId == 2 || selectId == 3 )
        {
            if(type == 'sub'){
                $('#sub1').addClass('Product_Cost');
                $('#sub1').removeClass('gray');
               // $('#sub4').addClass('Product_Cost');
               // $('#sub4').removeClass('gray');
                $('#sub1').addClass('js-cost-click');
               // $('#sub4').addClass('js-cost-click');
            }
            else{
                $('#sub1').removeClass('Product_Cost');
                $('#sub1').addClass('gray');
                //$('#sub4').removeClass('Product_Cost');
                //$('#sub4').addClass('gray');
                $('#sub1').removeClass('js-cost-click');
                //$('#sub4').removeClass('js-cost-click');
            }
        }
        if(selectId == 5){

            if(type == 'sub'){
                for (let i=6;i<=19;i++) {
                    if( i ==13 || i ==14 || i ==15 || i ==18 ){}
                    else{
                        $('#sub'+i).addClass('Shipping_Cost');
                        $('#sub'+i).removeClass('gray');
                        $('#sub'+i).addClass('js-cost-click');
                    }
                }
            }
            else{
                for (let i=6;i<=19;i++) {
                    $('#sub'+i).removeClass('Shipping_Cost');
                    $('#sub'+i).addClass('gray');
                    $('#sub'+i).removeClass('js-cost-click');
                }
            }
        }
        if(selectId == 27){

            if(type == 'sub'){
                $('#sub28').addClass('Mark_Of_Price_Group');
                $('#sub28').removeClass('gray');
                $('#sub28').addClass('js-cost-click');
                $('#sub29').addClass('Mark_Of_Price_Group');
                $('#sub29').removeClass('gray');
                $('#sub29').addClass('js-cost-click');
            }
            else{
                $('#sub28').removeClass('Mark_Of_Price_Group');
                $('#sub28').addClass('gray');
                $('#sub28').removeClass('js-cost-click');
                $('#sub29').removeClass('Mark_Of_Price_Group');
                $('#sub29').addClass('gray');
                $('#sub29').removeClass('js-cost-click');
            }
        }
        if(selectId == 28){

            if(type == 'sub'){
                $('#sub27').addClass('Mark_Of_Price_Group');
                $('#sub27').removeClass('gray');
                $('#sub27').addClass('js-cost-click');
                $('#sub29').addClass('Mark_Of_Price_Group');
                $('#sub29').removeClass('gray');
                $('#sub29').addClass('js-cost-click');
            }
            else{
                $('#sub27').removeClass('Mark_Of_Price_Group');
                $('#sub27').addClass('gray');
                $('#sub27').removeClass('js-cost-click');
                $('#sub29').removeClass('Mark_Of_Price_Group');
                $('#sub29').addClass('gray');
                $('#sub29').removeClass('js-cost-click');
            }
        }
        if(selectId == 29){

            if(type == 'sub'){
                $('#sub28').addClass('Mark_Of_Price_Group');
                $('#sub28').removeClass('gray');
                $('#sub28').addClass('js-cost-click');
                $('#sub27').addClass('Mark_Of_Price_Group');
                $('#sub27').removeClass('gray');
                $('#sub27').addClass('js-cost-click');
            }
            else{
                $('#sub28').removeClass('Mark_Of_Price_Group');
                $('#sub28').addClass('gray');
                $('#sub28').removeClass('js-cost-click');
                $('#sub27').removeClass('Mark_Of_Price_Group');
                $('#sub27').addClass('gray');
                $('#sub27').removeClass('js-cost-click');
            }
        }
    }
    $("#add_price_group").validate({
        submitHandler(form){
            $(".submit").attr("disabled", true);
            $('div#preloader').show();
            var form_cust = $('#add_price_group')[0];
            let form1 = new FormData(form_cust);
            $.ajax({
                type: "POST",
                url: '{{route('pricegroup.store')}}',
                data: form1,
                processData: false,
                contentType: false,
                success: function( response ) {
                    $('div#preloader').hide();
                    if(response.error == false){
                        toastr.success(response.msg);
                        setTimeout(function(){
                            location.href= response.url;
                        },2000);
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
            return false;
        }
    });

    function GetClientChanel(){
        $.ajax({
            method:'POST',
            url:'{{ route('getClientChanels') }}',
            data:{lobs:$("#lobs").val(),chanel_ids:$("#chanel_ids").val()},
            dataType:'html',
            success:function(res){
                $("#client_channel_container").html(res);
            }
        })
    }
</script>
@endsection
