@extends('layouts.master')

@section('main-content')
    
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <div class="breadcrumb">
        <h1>Pipeline & Productivity Metrics</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>
    <div class="row mb-4">
        <div class="col-md-12 mb-4">           
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Orders</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            @include('report_section.parts.container_order_chart_total_orders')
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="mc-footer">
                        <div class="row">
                            <div class="col-lg-12">
                                <!-- <button type="submit" class="btn btn-primary m-1">Submit</button> -->
                            </div>
                        </div>
                    </div>
                </div>             
            </div>
            <div class="card text-left mt-5">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Orders By User</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            @include('report_section.parts.container_order_chart_total_orders_by_users')
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="mc-footer">
                        <div class="row">
                            <div class="col-lg-12">
                                <!-- <button type="submit" class="btn btn-primary m-1">Submit</button> -->
                            </div>
                        </div>
                    </div>
                </div>             
            </div>
            <div class="card text-left mt-5">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Orders By Warehouse</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            @include('report_section.parts.container_order_chart_total_orders_by_warehouse')
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="mc-footer">
                        <div class="row">
                            <div class="col-lg-12">
                                <!-- <button type="submit" class="btn btn-primary m-1">Submit</button> -->
                            </div>
                        </div>
                    </div>
                </div>             
            </div>
            <div class="card text-left mt-5">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Orders By Transit Day</h6>
                </div>
                <div class="col-md-12 mt-4">
                    <ul class="nav nav-tabs nav-justified">
                        <li class="nav-item">
                            <a class="nav-link active" href="#tab_td_graph" id="td_graph_tab" role="tab" aria-controls="td_graph_tab" area-selected="true" data-toggle="tab">Graphs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tab_td_table" id="td_table_tab" role="tab" aria-controls="td_table_tab" area-selected="false" data-toggle="tab">Table</a>
                        </li>								
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab_td_graph" role="tabpanel" area-labelledby="td_graph_tab">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        @include('report_section.parts.container_order_chart_transit')
                                    </div>
                                </div>
                            </div> 
                        </div>
                        <div class="tab-pane fade" id="tab_td_table" role="tabpanel" area-labelledby="td_table_tab">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        @include('report_section.parts.container_tb_total_td')
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>						
                </div>                                           
            </div>
            <div class="card text-left mt-5">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Orders By Order Status</h6>
                </div>
                <div class="col-md-12 mt-4">
                    <ul class="nav nav-tabs nav-justified">
                        <li class="nav-item">
                            <a class="nav-link active" href="#tab_os_graph" id="os_graph_tab" role="tab" aria-controls="os_graph_tab" area-selected="true" data-toggle="tab">Graphs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tab_os_table" id="os_table_tab" role="tab" aria-controls="os_table_tab" area-selected="false" data-toggle="tab">Table</a>
                        </li>								
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab_os_graph" role="tabpanel" area-labelledby="os_graph_tab">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        @include('report_section.parts.container_order_chart_order_status')
                                    </div>
                                </div>
                            </div> 
                        </div>
                        <div class="tab-pane fade" id="tab_os_table" role="tabpanel" area-labelledby="os_table_tab">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        @include('report_section.parts.container_tb_total_os')
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>						
                </div>                
            </div>
            <div class="card text-left mt-5">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Orders By Ship Day</h6>
                </div>
                <div class="col-md-12 mt-4">
                    <ul class="nav nav-tabs nav-justified">
                        <li class="nav-item">
                            <a class="nav-link active" href="#tab_sd_graph" id="sd_graph_tab" role="tab" aria-controls="sd_graph_tab" area-selected="true" data-toggle="tab">Graphs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tab_sd_table" id="sd_table_tab" role="tab" aria-controls="sd_table_tab" area-selected="false" data-toggle="tab">Table</a>
                        </li>								
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab_sd_graph" role="tabpanel" area-labelledby="sd_graph_tab">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        @include('report_section.parts.container_order_chart_ship_day')
                                    </div>
                                </div>
                            </div> 
                        </div>
                        <div class="tab-pane fade" id="tab_sd_table" role="tabpanel" area-labelledby="sd_table_tab">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        @include('report_section.parts.container_tb_total_sd')
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>						
                </div>                
            </div>
        </div>
    </div>
    <div class="modal fade" id="MyColumnsModal" data-backdrop="static">
    </div>
    @include('download');
    
@endsection

@section('page-js')
<script type="text/javascript">
    

</script>
@endsection