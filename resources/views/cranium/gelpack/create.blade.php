@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
    @php $required_span = '<span class="text-danger">*</span>' @endphp
    <div class="breadcrumb">
        <h1>Gel Pack Chart</h1>
        <ul>
            <li><a href="">Gel Pack Chart</a></li>
            <li>New</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">New Gel Pack Chart Template</h6>
                </div>
                <form action="javascrpt:void(0)" id="new_gel_pack_chart_template">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="template_name" class="ul-form__label">Template Name:<?php echo $required_span; ?></label>
                                <input type="text" required class="form-control" id="template_name" name="template_name" placeholder="Enter Template Name">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="template__description" class="ul-form__label">Template Description:</label>
                                <textarea required name="template__description" id="template__description" cols="10" rows="3" class="form-control" placeholder="Enter Template Description"></textarea>
                            </div>
                            
                            <div class="card-body">
                                <table class="table-responsive">
                                    <thead>
                                        <tr>
                                            <th class="table_border">Transit</th>
                                            <th class="table_border" colspan="4" style="background-color:#92D050">1 Day</th>
                                            <th class="table_border" colspan="4" style="background-color:#00B0F0">2 Day</th>
                                            <th class="table_border" colspan="4" style="background-color:#FFC000">3 Day</th>
                                            <th class="table_border" colspan="4" style="background-color:#FFFF00">4 Day</th>                                          
                                        </tr>
                                        <tr>
                                            <td class="table_border">Box#</td>
                                            <td class="table_border">Block</td>
                                            <td class="table_border">Pellet</td>
                                            <td class="table_border">1.Lb</td>
                                            <td class="table_border">2.Lb</td>
                                            <td class="table_border">Block</td>
                                            <td class="table_border">Pellet</td>
                                            <td class="table_border">1.Lb</td>
                                            <td class="table_border">2.Lb</td>
                                            <td class="table_border">Block</td>
                                            <td class="table_border">Pellet</td>
                                            <td class="table_border">1.Lb</td>
                                            <td class="table_border">2.Lb</td>
                                            <td class="table_border">Block</td>
                                            <td class="table_border">Pellet</td>
                                            <td class="table_border">1.Lb</td>
                                            <td class="table_border">2.Lb</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($result as $key=>$val)
                                        <tr>
                                            <td class="table_border">{{$key}}</td>
                                            <td class="table_border">
                                                <input type="number" step="any" class="form-control standard" name="chartsval[{{$val}}][0][block]" value="">
                                            </td>
                                            <td class="table_border">
                                                <input type="number" step="any" class="form-control standard" name="chartsval[{{$val}}][0][pellet]" value="">
                                            </td>
                                            <td class="table_border">
                                                <input type="number" step="any" class="form-control standard" name="chartsval[{{$val}}][0][1lbpack]" value="">
                                            </td>
                                            <td class="table_border">
                                                <input type="number" step="any" class="form-control standard" name="chartsval[{{$val}}][0][2lbpack]" value="">
                                            </td>
                                            <td class="table_border">
                                                <input type="number" step="any" class="form-control standard" name="chartsval[{{$val}}][1][block]" value="">
                                            </td>
                                            <td class="table_border">
                                                <input type="number" step="any" class="form-control standard" name="chartsval[{{$val}}][1][pellet]" value="">
                                            </td>
                                            <td class="table_border">
                                                <input type="number" step="any" class="form-control standard" name="chartsval[{{$val}}][1][1lbpack]" value="">
                                            </td>
                                            <td class="table_border">
                                                <input type="number" step="any" class="form-control standard" name="chartsval[{{$val}}][1][2lbpack]" value="">
                                            </td>
                                            <td class="table_border">
                                                <input type="number" step="any" class="form-control standard" name="chartsval[{{$val}}][2][block]" value="">
                                            </td>
                                            <td class="table_border">
                                                <input type="number" step="any" class="form-control standard" name="chartsval[{{$val}}][2][pellet]" value="">
                                            </td>
                                            <td class="table_border">
                                                <input type="number" step="any" class="form-control standard" name="chartsval[{{$val}}][2][1lbpack]" value="">
                                            </td>
                                            <td class="table_border">
                                                <input type="number" step="any" class="form-control standard" name="chartsval[{{$val}}][2][2lbpack]" value="">
                                            </td>
                                            <td class="table_border">
                                                <input type="number" step="any" class="form-control standard" name="chartsval[{{$val}}][3][block]" value="">
                                            </td>
                                            <td class="table_border">
                                                <input type="number" step="any" class="form-control standard" name="chartsval[{{$val}}][3][pellet]" value="">
                                            </td>
                                            <td class="table_border">
                                                <input type="number" step="any" class="form-control standard" name="chartsval[{{$val}}][3][1lbpack]" value="">
                                            </td>
                                            <td class="table_border">
                                                <input type="number" step="any" class="form-control standard" name="chartsval[{{$val}}][3][2lbpack]" value="">
                                            </td>
                                        </tr>
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
    <script src="{{ asset('assets/js/validation/jquery.validate.js') }}"></script>
    <script src="{{ asset('assets/js/validation/additional-methods.min.js') }}"></script>
    <script>
       $(document).ready(function () {
               // $("#new_gel_pack_chart_template").on('submit',function(e){
                 //   e.preventDefault();
            $("#new_gel_pack_chart_template").validate({
                submitHandler(form){
                    $(".submit").attr("disabled", true);
                    var form_cust = $('#new_gel_pack_chart_template')[0];
                    let form1 = new FormData(form_cust);
                    $.ajax({
                        type: "POST",
                        url: '{{ route('gelpack.store') }}',
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
