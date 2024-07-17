<style>
ul {
  list-style-type: none;
  padding-left:0px;
}
</style>
<div class="breadcrumb">
    <h1>Reports Builder</h1>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row">
	<div class="col-md-12">
		<div class="card o-hidden mb-4">
			<div class="card-header">
				<div class="row">
					<div class="col-md-2">
						<h3 class="w-50 float-left card-title m-0">Filters</h3>
					</div>
				</div>
			</div>
			<div class="card-body">
                <form method="post" id="build_report" data-parsley-validate>
				<div class="mb-3 row" style="">
					<div class="col-md-3 mb-4">
                        <label for="report_type" class="ul-form__label">Report Type:</label>
                        <select required data-parsley-errors-container="#errorReport" class="form-control select2" onchange="reportType(this)" id="report" name="report">
                            <option value="">--Select--</option>
                            <option value="product_report">Product Report</option>
                            <option value ="inventory_report">Inventory Report</option>
                            <option value="order_report">Order Report</option>
                            <option value="user_report">User Report</option>
                            <option value="material_report">Materials Report</option>
                        </select>
                        <div id="errorReport" class="text-danger"></div>
                    </div>
                    <div class="col-md-2 mb-4">
                        <label for="report_type" class="ul-form__label">Sub Report Type:</label>
                        <select required data-parsley-errors-container="#errorReportSub" class="form-control select2" onchange="changeSubReport()" id="report_type" name="report_type">
                            <option ref_val="" value="">--Select--</option>
                            <option ref_val="product_report" value="all">All Product Report</option>
                            <option ref_val="product_report" value="own">Own Product Report</option>
                            <option ref_val="inventory_report" value="receive">Receiving Report</option>
                            <option ref_val="inventory_report" value="putaway">Put Away Report</option>
                            <option ref_val="inventory_report" value="restoke">Restock Report</option>
                            <option ref_val="inventory_report" value="transfer">Transfer Report</option>
                            <option ref_val="inventory_report" value="inventory_adjustment">Inventory Adjustments Report</option>
                            <option ref_val="inventory_report" value="perpetual">Perpetual Inventory Report</option>
                            <option ref_val="inventory_report" value="inventory">Inventory Report</option>
                            <option ref_val="inventory_report" value="mark_report">mark out Report</option>
                            <option ref_val="inventory_report" value="own_inventory">Own Inventory Report</option>
                            <option ref_val="order_report" value="open_order">Open Order Report</option>
                            <option ref_val="order_report" value="shipped_order">Shipped Orders Report</option>
                            <option ref_val="order_report" value="shipped_line_order">Shipped Line Items Report</option>
                            <option ref_val="order_report" value="unfulfill_order">Unfulfillable orders report</option>
                            <option ref_val="order_report" value="all_order">All Order Report</option>
                            <option ref_val="order_report" value="own_order">Own Order Report</option>
                            <option ref_val="user_report" value="own">WMS User Report</option>
                            <option ref_val="user_report" value="own">CPM User Report </option>
                            <option ref_val="user_report" value="own">Own User Report </option>
                            <option ref_val="material_report" value="own3">Box Report</option>
                            <option ref_val="material_report" value="own4">Dry Ice Report</option>
                        </select>
                        <div id="errorReportSub" class="text-danger"></div>
                    </div>
                    <div class="col-md-2 mb-4 js-fromdate d-none">
                        <label class="ul-form__label">From Date</label>
                        <input type="date" value="{{date('Y-m-d')}}" class="form-control" id="from_date" name="from_date">
                    </div>
                    <div class="col-md-2 mb-4 js-todate d-none">
                        <label class="ul-form__label">To Date</label>
                        <input type="date" value="{{date('Y-m-d')}}" class="form-control" id="to_date" name="to_date">
                    </div>
                    <div class="col-md-2 mb-4 d-none js-own-inventory">
                        <label for="report_type" class="ul-form__label">Header For inventory Report:</label>
                        <select class="form-control select2"  multiple id="own_inventory_report_type" name="own_inventory_report_type[]">
                            <option  value="">--Select--</option>
                            <option value="nv_qty">NV Total Quantity</option>
                            <option value="nv_orderable_qty">NV Orderable Quantity</option>
                            <option value="nv_fulfilled_qty">NV Fulfillable Quantity</option>
                            <option value="nv_open_order_qty">NV On Order Quantity</option>
                            <option value="nv_each_qty">NV Inbound Quantity</option>
                            <option value="okc_qty">OK Total Quantity</option>
                            <option value="okc_orderable_qty">OK Orderable Quantity</option>
                            <option value="okc_fulfilled_qty">OK Fulfillable Quantity</option>
                            <option value="okc_open_order_qty">OK On Order Quantity</option>
                            <option value="okc_each_qty">OK Inbound Quantity</option>
                            <option value="wi_qty">WI Total Quantity</option>
                            <option value="wi_orderable_qty">WI Orderable Quantity</option>
                            <option value="wi_fulfilled_qty">WI Fulfillable Quantity</option>
                            <option value="wi_open_order_qty">WI On Order Quantity</option>
                            <option value="wi_each_qty">WI Inbound Quantity</option>
                            <option value="pa_qty">PA Total Quantity</option>
                            <option value="pa_orderable_qty">PA Orderable Quantity</option>
                            <option value="pa_fulfilled_qty">PA Fulfillable Quantity</option>
                            <option value="pa_open_order_qty">PA On Order Quantity</option>
                            <option value="pa_each_qty">PA Inbound Quantity</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-4 d-none js-own-order">
                        <label for="report_type" class="ul-form__label">Header For Own Order Report:</label>
                        <select class="form-control select2"  multiple id="own_order_report_type" name="own_order_report_type[]">
                            <option  value="">--Select--</option>
                            <option value="warehouses">Warehouse</option>
                            <option value="client">Client</option>
                            <option value="channel_order_number">Channel / Manual Order Number</option>
                            <option value="order_status_name">Order Status</option>
                            <option value="purchase_date">Order Date</option>
                            <option value="ship_to_name">Ship to Name</option>
                            <option value="ship_to_address1">Ship to Line 1</option>
                            <option value="ship_to_address2">Ship to Line 2</option>
                            <option value="ship_to_address3">Ship to Line 3</option>
                            <option value="ship_to_city">City</option>
                            <option value="ship_to_state">State</option>
                            <option value="ship_to_zip">Zip</option>
                            <option value="ship_to_country">Country</option>
                            <option value="must_ship_today">Must Ship Today</option>
                            <option value="wi_orderable_qty">Transit Time</option>
                            <option value="totalorder">Number of Items on the Order</option>
                            {{-- <option value="package_num">Number of Packages</option>
                            <option value="tracking_number">Tracking Number</option>
                            <option value="ship_date">Shipped Date</option>
                            <option value="shipping_label_creation_time">Shipped Time</option> --}}
                        </select>
                    </div>
                    <input type="hidden" name="client_id" value="{{$row->id}}"/>
                    <div class="col-md-2 mb-4">
                        <label for="warehouseId" class="ul-form__label">Select Warehouse</label>
                        <select class="form-control select2" id="warehouseId" name="warehouseId">
                            <option value=''>Select Warehouse</option>
                            @foreach($warehouse as $warehouseKey=>$warehouseVal)
                                <option value="{{$warehouseVal->warehouses}}">{{$warehouseVal->warehouses}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-4" style="margin-top: 2.5rem!important;">
                        <button type="button" class="btn btn-primary d-none js-product-own" data-toggle="modal" data-target="#modelColumns" data-backdrop="static" data-keyboard="false">
							Show / Hide Columns
						</button>
                        <button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#modelSmartFilters" data-backdrop="static" data-keyboard="false">
							Smart Filters
						</button>
					</div>
                </div>
                <div class="row">
                    <div class="col-md-3" style="margin-top: 2.5rem!important;">
                        <button type="button" onClick="buildReport()" class="btn btn-primary submit">Export Report</button>
                    </div>
                </div>
                </form>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modelColumns">
    <div class="modal-dialog modal-xl" style="width: 25%;">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#f7f7f7 !important">
                <h4 class="modal-title">Show / Hide Columns</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
		        <form action="javascript:void(0);" method='POST' id="column_visibility_form">
		            @csrf
                    <div class="row">
                        <ul class="" id="#column_visibility"  style="list-style-type:none;">
                            @if($product_listing_filter)
                                @foreach($product_listing_filter as  $key => $row_product_listing_filter)
                                    <li class="m-2">
                                        <label for="hide_show_column_{{ $row_product_listing_filter->id }}">
                                            <input  id="hide_show_column_{{ $row_product_listing_filter->id }}" class="listing-filter-columns" type="checkbox" name='columns[]' value="{{ $row_product_listing_filter->column_name }}" {{ !empty($visible_filters) && in_array($row_product_listing_filter->sorting_order,$visible_columns) || ($row_product_listing_filter->is_default == 1) ? 'checked' : ''}} <?php if(!empty($visible_filters) && in_array($row_product_listing_filter->sorting_order,$visible_columns) || ($row_product_listing_filter->is_default == 1)) { ?> onclick="return false"<?php } ?>>
                                            <span class="font-weight-bold ml-2">{{ $row_product_listing_filter->label_name }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" onClick="GetActiveProducts()">Close</button>
                    </div>
		        </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modelSmartFilters">
    <div class="modal-dialog modal-lg" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#f7f7f7 !important">
                <h4 class="modal-title">Smart Filters</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered text-center">
                    @if($smart_filters)
                        <tr>
                            <td>Fillter name</td>
                            <td>Apply Fillter</td>
                            <td>Visible Column</td>
                        </tr>
                        @foreach($smart_filters as $smart_fil)
                            <tr>
                                <td><a href="{{url('/masterparoducts_approved')}}/{{$smart_fil->id}}" class="font-weight-bold ml-2" style="<?php if($id != NULL && $smart_fil->id == $id) echo 'color: #19bef4';?>">{{ $smart_fil->filter_name}}</a></td>
                                <td>{{$smart_fil->productListingFilterList($smart_fil->visible_filters)}}</td>
                                <td>{{$smart_fil->productListingFilterList($smart_fil->visible_columns)}}</td>
                            </tr>
                        @endforeach
                    @endif
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>