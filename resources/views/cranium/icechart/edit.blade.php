@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
    @php $required_span = '<span class="text-danger">*</span>' @endphp
    <div class="breadcrumb">
        <h1>Ice Chart</h1>
        <ul>
            <li><a href="">Ice Chart</a></li>
            <li>Edit</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Edit Ice Chart Template</h6>
                </div>
                <form action="javascrpt:void(0)" id="new_ice_chart_template">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="template_name" class="ul-form__label">Template Name:<?php echo $required_span; ?></label>
                                <input type="text" required class="form-control" id="template_name" name="template_name" value="{{$iceChartTemplate->template_name}}">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="template__description" class="ul-form__label">Template Description:</label>
                                <textarea name="template__description" required id="template__description" cols="10" rows="3" class="form-control">{{$iceChartTemplate->template__description}}</textarea>
                            </div>
                            
                            <div class="card-body">
                                <table class="table-responsive">
                                    <thead>
                                        <tr>
                                            <th class="table_border">Transit</th>
                                            <th class="table_border" colspan="2" style="background-color:#92D050">1 Day</th>
                                            <th class="table_border" colspan="2" style="background-color:#00B0F0">2 Day</th>
                                            <th class="table_border" colspan="2" style="background-color:#FFC000">3 Day</th>
                                            <th class="table_border" colspan="2" style="background-color:#FFFF00">4 Day</th>                                          
                                        </tr>
                                        <tr>
                                            <td class="table_border">Box#</td>
                                            <td class="table_border">Block</td>
                                            <td class="table_border">Pellet</td>
                                            <td class="table_border">Block</td>
                                            <td class="table_border">Pellet</td>
                                            <td class="table_border">Block</td>
                                            <td class="table_border">Pellet</td>
                                            <td class="table_border">Block</td>
                                            <td class="table_border">Pellet</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($iceEditArray as $key=>$val)
                                            @if(isset($result[$val->packaging_materials_id]))
                                                <tr>
                                                    <td class="table_border">{{$result[$val->packaging_materials_id]}}</td>
                                                    <td class="table_border">
                                                        <input type="number" step="any" class="form-control standard" name="chartsval[{{$val->packaging_materials_id}}][0][block]" value="{{$val['1day_block']}}">
                                                    </td>
                                                    <td class="table_border">
                                                        <input type="number" step="any" class="form-control standard" name="chartsval[{{$val->packaging_materials_id}}][0][pellet]" value="{{$val['1day_pellet']}}">
                                                    </td>
                                                    <td class="table_border">
                                                        <input type="number" step="any" class="form-control standard" name="chartsval[{{$val->packaging_materials_id}}][1][block]" value="{{$val['2day_block']}}">
                                                    </td>
                                                    <td class="table_border">
                                                        <input type="number" step="any" class="form-control standard" name="chartsval[{{$val->packaging_materials_id}}][1][pellet]" value="{{$val['2day_pellet']}}">
                                                    </td>
                                                    <td class="table_border">
                                                        <input type="number" step="any" class="form-control standard" name="chartsval[{{$val->packaging_materials_id}}][2][block]" value="{{$val['3day_block']}}">
                                                    </td>
                                                    <td class="table_border">
                                                        <input type="number" step="any" class="form-control standard" name="chartsval[{{$val->packaging_materials_id}}][2][pellet]" value="{{$val['3day_pellet']}}">
                                                    </td>
                                                    <td class="table_border">
                                                        <input type="number" step="any" class="form-control standard" name="chartsval[{{$val->packaging_materials_id}}][3][block]" value="{{$val['4day_block']}}">
                                                    </td>
                                                    <td class="table_border">
                                                        <input type="number" step="any" class="form-control standard" name="chartsval[{{$val->packaging_materials_id}}][3][pellet]" value="{{$val['4day_pellet']}}">
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="mc-footer">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button type="submit" class="btn  btn-primary m-1">Submit</button>
                                            <a href="{{ route('icechart.index') }}" class="btn btn-outline-secondary m-1">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                    </div>
                </form>
                <div class="col-md-12 mt-4">
                </div>
            </div>
        </div>
    </div>
    <!-- end of col -->
@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script>
       $(document).ready(function () {
            $("#new_ice_chart_template").validate({
                submitHandler(form){
                    $(".submit").attr("disabled", true);
                    var form_cust = $('#new_ice_chart_template')[0];
                    let form1 = new FormData(form_cust);
                    $.ajax({
                        type: "POST",
                        url: '{{ route('icechart.update',$iceChartTemplate->id) }}',
                        data: form1,
                        processData: false,
                        contentType: false,
                        success: function( response ) {
                            $(".submit").attr("disabled", false);
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
                            var errors = data.responseJSON;
                            $.each( errors.errors, function( key, value ) {
                                var ele = "#"+key;
                                $(ele).addClass('border-danger');
                                $('<label class="text-danger">'+ value +'</label>').insertAfter(ele);
                            });
                        }
                    })
                }
            })
       });
   </script>
@endsection
