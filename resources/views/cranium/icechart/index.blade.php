@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Ice Chart Template</h1>
        <!-- <ul>
            <li><a href="">UI Kits</a></li>
            <li>Datatables</li>
        </ul> -->
    </div>
    <div class="separator-breadcrumb border-top"></div>
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <label for="warehouseId" class="ul-form__label">Select Warehouse</label>
            <select class="form-control select2" onchange="warehouseTemplate()" id="warehouseId" name="warehouseId">
                <option value="">--Select--</option>
                @foreach($warehouse as $warehouseKey=>$warehouseVal)
                    <option value="{{$warehouseVal->id}}">{{$warehouseVal->warehouses}}</option>
                @endforeach
            </select>
        </div> 
        @foreach($warehouse as $warehouseKey=>$warehouseVal)
            <div class="col-md-12">
                <div class="card text-left">
                    <div class="table-responsive d-none tabdaync" id="tab{{$warehouseVal->id}}">
                        <table class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Warehouse</th>  
                                    <th>Template</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{$warehouseVal->warehouses}}</td>
                                    <td>{{isset($warehouseVal->iceChartTemplate->first()->template_name) ? $warehouseVal->iceChartTemplate->first()->template_name : ' '}}</td>
                                    <td id="row{{$warehouseVal->id}}">
                                        @if(count($result) > 0)
                                            <a href="javascript:void(0);"  onclick="editTemplate({{$warehouseVal->id}})" class="btn btn-primary btn-sm m-1">Edit
                                            </a>
                                            @if(isset($warehouseVal->iceChartTemplate->first()->id))
                                                <a href="{{ route('icechart.exportWarehouseTemplate',[$warehouseVal->iceChartTemplate->first()->id,'pdf'])}}" class="btn btn-primary btn-sm" id="btn_export_pdf" >Pdf</a>
                                                <a href="{{ route('icechart.exportWarehouseTemplate',[$warehouseVal->iceChartTemplate->first()->id,'excel'])}}" class="btn btn-primary btn-sm" id="btn_export_excel" >Excel</a>
                                            @endif
                                        @else
                                            <a href="{{ route('icechart.create') }}" class="btn btn-primary btn-sm m-1"><i class="i-Add-User text-white mr-2"></i> Add </a>
                                        @endif
                                    </td>
                                    <td id="selectTemplate{{$warehouseVal->id}}" class="d-none">
                                        <select class="form-control select2" id="templateId{{$warehouseVal->id}}" name="selectTemplate" onchange="changeTemplate({{$warehouseVal->id}})">
                                            <option value="">--Select--</option>
                                            @if(count($result) > 0)
                                                @foreach($result as $row)
                                                    <option value="{{ $row->id }}">{{ $row->template_name }}</option>
                                                @endforeach
                                            @endif                        
                                        </select>
                                    </td>
                                    <td id='cancelBtn{{$warehouseVal->id}}' class="d-none">
                                        <a href="javascript:void(0);" class="btn btn-outline-secondary btn-sm m-1" onclick="cancelTemplate({{$warehouseVal->id}})">Cancel</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                         @if($warehouseVal->iceChartTemplate->first())
                        <table  class="display table table-striped table-bordered" style="width:100%">
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
                               @foreach($warehouseVal->iceChartTemplate->first()->iceSubChart as $key=>$val)
                                    @if(isset($resultArray[$val['packaging_materials_id']])
                                        && ( $val['1day_block']!= NULL
                                        || $val['1day_pellet']!= NULL
                                        || $val['2day_block']!= NULL
                                        || $val['2day_pellet']!= NULL
                                        || $val['3day_block']!= NULL
                                        || $val['3day_pellet']!= NULL
                                        || $val['4day_block']!= NULL
                                        || $val['4day_pellet']!= NULL
                                        ))
                                        <tr>
                                            <td class="table_border">{{$resultArray[$val['packaging_materials_id']]}}</td>
                                            <td class="table_border">
                                                {{$val['1day_block']}}
                                            </td>
                                            <td class="table_border">
                                                {{$val['1day_pellet']}}
                                            </td>
                                            <td class="table_border">
                                                {{$val['2day_block']}}
                                            </td>
                                            <td class="table_border">
                                                {{$val['2day_pellet']}}
                                            </td>
                                            <td class="table_border">
                                                {{$val['3day_block']}}
                                            </td>
                                            <td class="table_border">
                                                {{$val['3day_pellet']}}
                                            </td>
                                            <td class="table_border">
                                                {{$val['4day_block']}}
                                            </td>
                                            <td class="table_border">
                                                {{$val['4day_pellet']}}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                
                            </tbody>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        @endForeach
    </div>
    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                 <!-- @if(ReadWriteAccess('AddNewSupplier')) -->
                <div class="card-header text-right bg-transparent">
                    <a href="{{ route('icechart.create') }}" class="btn btn-primary btn-md m-1"><i class="i-Add-User text-white mr-2"></i> Add New</a>
                </div>
                <!-- @endif -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="all_ice_chart_template" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Template Name</th>
                                    <th>Template Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($result)
                                    @foreach($result as $row)
                                        <tr>
                                            <td>{{ $row->template_name }}</td>
                                            <td>{{ $row->template__description }}</td>
                                            <td>
                                                <a href="{{ route('icechart.edit',$row->id) }}" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Edit">
                                                    <i class="nav-icon i-Pen-2 "></i>
                                                </a>
                                                <a href="{{ route('icechart.exportWarehouseTemplate',[$row->id,'pdf'])}}" class="btn btn-primary" id="btn_export_pdf" _onclick="ExportPDF('{{$row->id}}','pdf','{{$row->template_name}}')">Pdf</a>
                                                <a href="{{ route('icechart.exportWarehouseTemplate',[$row->id,'excel'])}}" class="btn btn-primary" id="btn_export_excel" _onclick="ExportExcel('{{$row->id}}','excel')">Excel</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
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
            $('#all_ice_chart_template').DataTable();
       });
       editTemplate = (id) =>{
        $("#selectTemplate"+id).removeClass('d-none')
        $("#cancelBtn"+id).removeClass('d-none')
        $("#row"+id).addClass('d-none')
       }

       cancelTemplate = (id) =>{
        $("#selectTemplate"+id).addClass('d-none')
        $("#cancelBtn"+id).addClass('d-none')
        $("#row"+id).removeClass('d-none')
       }
       
       changeTemplate = (id) =>{
            templateId =  $('#templateId'+id).val();
            $.ajax({
                type: "POST",
                url: '{{ route('icechart.updatewarehousetemplate') }}',
                data: {'id':id,'templateId':templateId},
                success: function( response ){
                    $("#preloader").hide();
                    location.reload()
                }
            })
       }
       warehouseTemplate = () =>{
        $('.tabdaync').addClass('d-none');
        templateId =  $('#warehouseId').val();
        $('#tab'+templateId).removeClass('d-none')
       }

       ExportPDF = (id,type,name) =>{
        $("#preloader").css("display","block")
        $("#btn_export_pdf").off('click');
        $.ajax({
            type: "POST",
            url: '',
            data: {'id': id,'type': 'pdf'},
            xhrFields: {
                responseType: 'blob'
            },
            success: function( response ) {
                const d = new Date();
                var blob = new Blob([response]);
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "Cranium_"+"{{date('Ymd')}}"+".pdf";
                link.click();
                DeletePdf();
                $("#preloader").css("display","none")
            },
        });
        return false;
       }
       DeletePdf = () =>{
            $.ajax({
                type: "GET",
                url: '{{ route('deletepdf')}}',
                success: function( response ) {},
            });
            return true;
        }
        ExportExcel = (id,type) => {
            
        }
   </script>
@endsection
