@extends('layouts.master')


@section('main-content')
<style>
	.select2-container--open {
		z-index: 9999999
	}
</style>
<div class="breadcrumb">
	<h1>Cranium</h1>
	<ul>
		<li><a href="{{ route('orders.index') }}">Edit Order</a></li>
		<li>View</li>
	</ul>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="card o-hidden mb-4">
			<div class="card-header">
				<div class="row">
					<div class="col-md-2">
						<h3 class="w-100 float-left card-title m-0">Order Details</h3>
					</div>
				</div>
			</div>
			<form action="#" method="POST" id="save_manual_order">
				<div class="row md-12">
					<div class="col-md-4 table-responsive card_">
						<table class="table ">
							<thead>
								<tr>
									<td colspan="2"><h3 class="card_-title text-center">Order Details</h3></td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td scope="row" data-placement="top" title="Status">Status</td>
									<td scope="row" data-placement="top">
										<input type="text" value="{{$order->order_status_name}}" readonly class="form-control">
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="e-tailer Order Number">e-tailer Order Number</td>
									<td scope="row" data-placement="top"><input value="{{$order->etailer_order_number}}" type="text" readonly class="form-control"></td>
								</tr>
								<!-- <tr>
									<td scope="row" data-placement="top" title="Channel Order Number">Channel Order Number</td>
									<td scope="row" data-placement="top"><input type="text" value="{{$order->channel_order_number}}" class="form-control" name="channel_order_number" id="channel_order_number"></td>
								</tr> -->
								<tr>
									<td scope="row" data-placement="top" title="Order Source">Order Source</td>
									<td scope="row" data-placement="top"><input type="text" name="order_source" value="{{$order->order_source}}" id="order_source" value="Manual" readonly class="form-control"></td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Purchase Date">Purchase Date</td>
									<td scope="row" data-placement="top"><input type="date" name="purchase_date" value="{{$order->purchase_date}}" id="purchase_date"  class="form-control"></td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Channel Ship Date">Channel Ship Date</td>
									<td scope="row" data-placement="top"><input type="date" name="channel_estimated_ship_date" value="{{$order->channel_estimated_ship_date}}" id="channel_estimated_ship_date"  class="form-control"></td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Order Total Price">Order Total Price</td>
									<td scope="row" data-placement="top"><input type="number" name="order_total_price" id="order_total_price" value="{{$order->order_total_price}}"  class="form-control" readonly></td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Must Ship Today">Must Ship Today</td>
									<td scope="row" data-placement="top">
										<select id="must_ship_today" name="must_ship_today" class="form-control select2">
											<option value='0' <?php if($order->must_ship_today == '0'){ echo 'selected'; }?>>No</option>											
											<option value='1' <?php if($order->must_ship_today == '1'){ echo 'selected'; }?>>Yes</option>
										</select>
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Client">Client</td>
									<td scope="row" data-placement="top" style="@if($client_id != '') pointer-events:none  @endif">
										<select id="client_id" name="client_id" class="form-control select2">
											<option value="">Select Client</option>
											@if($client)
												@foreach($client as $row_client)
													<option value='{{$row_client->id}}' <?php if($order->client_id == $row_client->id){ echo 'selected'; }?>>{{$row_client->company_name}}</option>
												@endforeach
											@endif
										</select>
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Hold Release Date">Hold Release Date</td>
									<td scope="row" data-placement="top">
										<input type="date" id="release_date" name="release_date" value="{{$order->release_date}}"  class="form-control"/>
									</td>
								</tr>
								
							</tbody>
						</table>
					</div>
					<div class="col-md-4 table-responsive card_">
						<table class="table ">
							<thead>
								<tr>
									<td colspan="2"><h3 class="card_-title text-center">Customer Details</h3></td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td scope="row" data-placement="top" title="Name">Name</td>
									<td scope="row" data-placement="top">
										<input type="text" id="customer_name" name="customer_name" value="{{$order->customer_name}}" class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="E-Mail">E-Mail</td>
									<td scope="row" data-placement="top">
										<input type="text" id="customer_email" name="customer_email" value="{{$order->customer_email}}"  class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Phone">Phone</td>
									<td scope="row" data-placement="top">
										<input type="text" id="customer_number" name="customer_number"  value="{{$order->customer_number}}"  class="form-control" />
									</td>
								</tr>
								
							</tbody>
						</table>
					</div>
					<div class="col-md-4 table-responsive card_">
						<table class="table ">
							<thead>
								<tr>
									<td colspan="2"><h3 class="card_-title text-center">Shipping Details</h3></td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td scope="row" data-placement="top" title="Ship To Name">Ship To Name</td>
									<td scope="row" data-placement="top">
										<input type="text" id="ship_to_name" name="ship_to_name"   value="{{$order->ship_to_name}}"  class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To Address Type">Ship To Address Type</td>
									<td scope="row" data-placement="top">
										<select id="ship_to_address_type" name="ship_to_address_type" class="select2 form-control">
											<option value="">Select</option>
											<option value="Residential" @if($order->ship_to_address_type == 'Residential') selected='selected' @endif>Residential</option>
											<option value="Business" @if($order->ship_to_address_type == 'Business') selected='selected' @endif>Business</option>
										</select>
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To Address1">Ship To Address1</td>
									<td scope="row" data-placement="top">
										<input type="text" id="ship_to_address1" name="ship_to_address1" value="{{$order->ship_to_address1}}" class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To Address2">Ship To Address2</td>
									<td scope="row" data-placement="top">
										<input type="text" id="ship_to_address2" name="ship_to_address2" value="{{$order->ship_to_address2}}" class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To Address3">Ship To Address3</td>
									<td scope="row" data-placement="top">
										<input type="text" id="ship_to_address3" name="ship_to_address3" value="{{$order->ship_to_address3}}" class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To City">Ship To City</td>
									<td scope="row" data-placement="top">
										<input type="text" id="ship_to_city" name="ship_to_city" value="{{$order->ship_to_city}}"  class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To State">Ship To State</td>
									<td scope="row" data-placement="top">
										<input type="text" id="ship_to_state" name="ship_to_state" value="{{$order->ship_to_state}}" class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To Zip">Ship To Zip</td>
									<td scope="row" data-placement="top">
										<input type="text" id="ship_to_zip" name="ship_to_zip" value="{{$order->ship_to_zip}}" class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To Counter">Ship To Country</td>
									<td scope="row" data-placement="top">
										<input type="text" id="ship_to_country" name="ship_to_country" value="{{$order->ship_to_country}}" class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To Phone">Ship To Phone</td>
									<td scope="row" data-placement="top">
										<input type="text" id="ship_to_phone" name="ship_to_phone" value="{{$order->ship_to_phone}}" class="form-control" />
									</td>
								</tr>
								<!-- <tr>
									<td scope="row" data-placement="top" title="Shiping Request Method">Shiping Request Method</td>
									<td scope="row" data-placement="top">
										<input type="text" id="shipping_method" name="shipping_method" value="{{$order->shipping_method}}"  class="form-control" />
									</td>
								</tr> -->
								<tr>
									<td scope="row" data-placement="top" title="Delivery Notes">Delivery Notes</td>
									<td scope="row" data-placement="top">
										<input type="text" id="delivery_notes" name="delivery_notes" value="{{$order->delivery_notes}}" class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Customer Paid Shipping">Customer Paid Shipping</td>
									<td scope="row" data-placement="top">
										<select class="select2 col-md-6" id="customer_shipping_price" name="customer_shipping_price">
											<option value="" >Select</option>
											<option value="Yes" @if($order->customer_shipping_price == 'Yes') selected='selected' @endif>Yes</option>
											<option value="No" @if($order->customer_shipping_price == 'No') selected='selected' @endif>No</option>
										</select>
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Shipment Type">Shipment Type</td>
									<td scope="row" data-placement="top">
										<select class="select2 col-md-6" id="shipment_type" name="shipment_type">
											<option value="ground" <?php if($order->shipment_type == 'ground'){ echo 'selected'; }?>>Ground</option>
											<option value="air_os" <?php if($order->shipment_type == 'air_os'){ echo 'selected'; }?>>Air - Overnight Saver</option>
											<option value="air_so" <?php if($order->shipment_type == 'air_so'){ echo 'selected'; }?>>Air - Standard Overnight</option>
										</select>
									</td>
								</tr>
								
							</tbody>
						</table>
					</div>								
				</div>	
				<div class="row md-2">
					<div class="col-12 p-5">
						<button type="submit" class="btn btn-primary">Update</button>
					</div>
				</div>
			</form>		
		</div>		
	</div>	
</div>

<div class="row">
	<div class="col-md-12">
		<div class="card o-hidden mb-4">
			<div class="card-header">
				<div class="row">
					<div class="col-md-4">
						<h3 class="w-50 float-left card-title m-0">Products</h3>
					</div>
					<div class="col-md-2">

					</div>
					<div class="col-md-2">
					</div>
					<div class="col-md-2">						
					</div>
					<div class="col-md-2">
						<a href="{{route('orders.FilterOrderProducts',[$order->id])}}" class="btn btn-primary">Select Products</a>
					</div>
					
				</div>
			</div>

			<div class="card-body">
				@if($result)						
					@foreach($result as $key => $row)
						<div class="col-md-12 mt-4">
							<ul class="nav nav-tabs nav-justified">
								<li class="nav-item">
									<a class="nav-link active" href="#tab_sub_order_{{ str_replace(".", '_', $key) }}" id="sub_order_{{ str_replace(".", '_', $key) }}_tab" role="tab" aria-controls="sub_order_{{ str_replace(".", '_', $key) }}_tab" area-selected="true" data-toggle="tab">Sub Orders Details</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="#tab_ship_details_{{ str_replace(".", '_', $key) }}" id="ship_details_{{ str_replace(".", '_', $key) }}_tab" role="tab" aria-controls="ship_details_{{ str_replace(".", '_', $key) }}_tab" area-selected="false" data-toggle="tab">Shipment Details</a>
								</li>								
							</ul>
							<div class="tab-content">
								<div class="tab-pane fade show active" id="tab_sub_order_{{ str_replace(".", '_', $key) }}" role="tabpanel" area-labelledby="sub_order_{{ str_replace(".", '_', $key) }}_tab">
									<form method="post" action="{{ route('orders.update_qty') }}">
										@csrf
										<table class="table table-stripped">
											<thead>
												<tr>
													<th colspan="10" class="text-center" style="background:{{rand_color()}};">
														Sub order: #{{$key}}
													</th>
												</tr>
											</thead>
											@if($row)
												<thead>
													<tr>
														<th></th>
														<th>ETIN</th>
														<th>Product Listing Name </th>
														<th>Warehouse</th>
														<th>Quantity Ordered</th>
														<th>Quantity Fulfilled</th>
														<th>Carrier Type</th>
														<th>Shipment Type</th>														
														<th>Status</th>
														<th>Action</th>
													</tr>
												</thead>
												@foreach($row as $row_sub)
													<tr>
														<td>
															<input type="checkbox" name="cb_{{ $key }}" value="{{ $row_sub->id }}">
														</td>
														<td>{{ $row_sub->ETIN }}</td>
														<td>{{ $row_sub->product->product_listing_name }}</td>
														<td>
															<select id="wh[{{$row_sub['id']}}]" class="form-control select2" name="wh[{{$row_sub['id']}}]">											
																@foreach($whs as $wh)
																	<option value="{{ $wh->warehouses }}" <?php if($row_sub->warehouse === $wh->warehouses) echo 'selected'; ?>>{{ $wh->warehouses }}</option>
																@endforeach
															</select>
														</td>
														<td>
															<input id="order_item[{{$row_sub['id']}}]" type="number" name="order_item[{{$row_sub['id']}}]" value="{{ $row_sub['quantity_ordered'] }}">
														</td>
														<td>{{ $row_sub->quantity_fulfilled }}</td>
														<td>{{ $row_sub->carrier_name }}</td>
														<td>{{ $row_sub->service_name }}</td>
														<td>{{ $row_sub->status }}</td>
														@if (str_contains(strtolower($row_sub->product->item_form_description), 'kit'))
															<td><a class="btn btn-primary text-white" href="javascript:void(0)" onClick="showKitItems('{{ $row_sub->ETIN }}')" id="show_ki_btn">Show Kit Items</a></td>
														@else
															<td></td>
														@endif
													</tr>
														@if (str_contains(strtolower($row_sub->product->item_form_description), 'kit'))
															<tr name="kp_{{ $row_sub->ETIN }}" style="display: none;">
																<th colspan="10" class="text-center" style="background:{{rand_color()}};">
																	KIT Components for ETIN: #{{$row_sub->ETIN}}
																</th>
															</tr>
															<tr name="kp_{{ $row_sub->ETIN }}" style="display: none;">
																<th></th>
																<th colspan="2">Component ETIN</th>
																<th colspan="4">Description</th>
																<th colspan="3">Quantity</th>
															</tr>
															@foreach ($kit_items[$row_sub->ETIN] as $item)
																<tr name="kp_{{ $row_sub->ETIN }}" style="display: none;">
																	<td></td>
																	<td colspan="2">{{ $item->components_ETIN }}</td>
																	<td colspan="4">{{ $item->component_product_details->product_listing_name }}</td>
																	<td colspan="3">{{ $item->qty }}</td>
																</tr>
															@endforeach														
														@endif
												@endforeach
											@endif
										</table>
										<button type="submit" class="btn btn-primary float-right">Save</button>
										<!-- <button type="button" class="btn btn-primary float-right" style="margin-right: 10px;" onclick="splitOrders({{ $key }})">Split Orders</button> -->
										@if (isset($sub_order_ship_type[$key]['status']) && $sub_order_ship_type[$key]['status'] == '6')
											<button type="button" class="btn btn-primary float-right" style="margin-right: 10px;" onclick="showReshipOptions('{{ $key }}')">Re-Ship</button>
										@endif										
									</form>
								</div>
								<div class="tab-pane fade" id="tab_ship_details_{{ str_replace(".", '_', $key) }}" role="tabpanel" area-labelledby="ship_details_{{ str_replace(".", '_', $key) }}_tab">
									<form>
										@csrf
										<table class="table ">
											<thead>
												<tr>
													<th colspan="7" class="text-center" style="background:{{rand_color()}};">
														Shipping Details: #{{$key}}
													</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td scope="row" data-placement="top" title="Ship To Name">Ship To Name</td>
													<td scope="row" data-placement="top">
														@if(isset($sub_order_ship_type[$key]['ship_to_name']))
															<input type="text" id="ship_to_name[{{ $key }}]" value="{{ $sub_order_ship_type[$key]['ship_to_name'] }}" class="form-control" />
														@else
															<input type="text" id="ship_to_name[{{ $key }}]" value="{{ $summary->ship_to_name }}" class="form-control" />
														@endif
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To Address Type">Ship To Address Type</td>
													<td scope="row" data-placement="top">
														@if(isset($sub_order_ship_type[$key]['ship_to_address_type']))
															<input type="text" id="ship_to_address_type[{{ $key }}]" value="{{ $sub_order_ship_type[$key]['ship_to_address_type'] }}" class="form-control" />
														@else
															<input type="text" id="ship_to_address_type[{{ $key }}]" value="{{ $summary->ship_to_address_type }}" class="form-control" />
														@endif
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To Address1">Ship To Address1</td>
													<td scope="row" data-placement="top">
														@if(isset($sub_order_ship_type[$key]['ship_to_address1']))
															<input type="text" id="ship_to_address1[{{ $key }}]" value="{{ $sub_order_ship_type[$key]['ship_to_address1'] }}" class="form-control" />
														@else
															<input type="text" id="ship_to_address1[{{ $key }}]" value="{{ $summary->ship_to_address1 }}" class="form-control" />
														@endif
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To Address2">Ship To Address2</td>
													<td scope="row" data-placement="top">
														@if(isset($sub_order_ship_type[$key]['ship_to_address2']))
															<input type="text" id="ship_to_address2[{{ $key }}]" value="{{ $sub_order_ship_type[$key]['ship_to_address2'] }}" class="form-control" />
														@else
															<input type="text" id="ship_to_address2[{{ $key }}]" value="{{ $summary->ship_to_address2 }}" class="form-control" />
														@endif
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To Address3">Ship To Address3</td>
													<td scope="row" data-placement="top">
														@if(isset($sub_order_ship_type[$key]['ship_to_address3']))
															<input type="text" id="ship_to_address3[{{ $key }}]" value="{{ $sub_order_ship_type[$key]['ship_to_address3'] }}" class="form-control" />
														@else
															<input type="text" id="ship_to_address3[{{ $key }}]" value="{{ $summary->ship_to_address3 }}" class="form-control" />
														@endif
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To City">Ship To City</td>
													<td scope="row" data-placement="top">
														@if(isset($sub_order_ship_type[$key]['ship_to_city']))
															<input type="text" id="ship_to_city[{{ $key }}]" value="{{ $sub_order_ship_type[$key]['ship_to_city'] }}" class="form-control" />
														@else
															<input type="text" id="ship_to_city[{{ $key }}]" value="{{ $summary->ship_to_city }}" class="form-control" />
														@endif
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To State">Ship To State</td>
													<td scope="row" data-placement="top">
														@if(isset($sub_order_ship_type[$key]['ship_to_state']))
															<input type="text" id="ship_to_state[{{ $key }}]" value="{{ $sub_order_ship_type[$key]['ship_to_state'] }}" class="form-control" />
														@else
															<input type="text" id="ship_to_state[{{ $key }}]" value="{{ $summary->ship_to_state }}" class="form-control" />
														@endif
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To Zip">Ship To Zip</td>
													<td scope="row" data-placement="top">
														@if(isset($sub_order_ship_type[$key]['ship_to_zip']))
															<input type="text" id="ship_to_zip[{{ $key }}]" value="{{ $sub_order_ship_type[$key]['ship_to_zip'] }}" class="form-control" />
														@else
															<input type="text" id="ship_to_zip[{{ $key }}]" value="{{ $summary->ship_to_zip}}" class="form-control" />
														@endif
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To Counter">Ship To Country</td>
													<td scope="row" data-placement="top">
														@if(isset($sub_order_ship_type[$key]['ship_to_country']))
															<input type="text" id="ship_to_country[{{ $key }}]" value="{{ $sub_order_ship_country[$key]['ship_to_phone'] }}" class="form-control" />
														@else
															<input type="text" id="ship_to_country[{{ $key }}]" value="{{ $summary->ship_to_country }}" class="form-control" />
														@endif
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To Phone">Ship To Phone</td>
													<td scope="row" data-placement="top">
														@if(isset($sub_order_ship_type[$key]['ship_to_phone']))
															<input type="text" id="ship_to_phone[{{ $key }}]" value="{{ $sub_order_ship_type[$key]['ship_to_phone'] }}" class="form-control" />
														@else
															<input type="text" id="ship_to_phone[{{ $key }}]" value="{{ $summary->ship_to_phone }}" class="form-control" />
														@endif
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Shiping Request Method">Shiping Request Method</td>
													<td scope="row" data-placement="top">
														@if(isset($sub_order_ship_type[$key]['shipping_method']))
															<input type="text" id="shipping_method[{{ $key }}]" value="{{ $sub_order_ship_type[$key]['shipping_method'] }}" class="form-control" />
														@else
															<input type="text" id="shipping_method[{{ $key }}]" value="{{ $summary->shipping_method }}" class="form-control" />
														@endif
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Delivery Notes">Delivery Notes</td>
													<td scope="row" data-placement="top">
														@if(isset($sub_order_ship_type[$key]['delivery_notes']))
															<input type="text" id="delivery_notes[{{ $key }}]" value="{{ $sub_order_ship_type[$key]['delivery_notes'] }}" class="form-control" />
														@else
															<input type="text" id="delivery_notes[{{ $key }}]" value="{{ $summary->delivery_notes }}" class="form-control" />
														@endif
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Customer Paid Shipping">Customer Paid Shipping</td>
													<td scope="row" data-placement="top">
														@if(isset($sub_order_ship_type[$key]['customer_shipping_price']))
															<input type="text" id="customer_shipping_price[{{ $key }}]" value="{{ $sub_order_ship_type[$key]['customer_shipping_price'] }}" class="form-control" />
														@else
															<input type="text" id="customer_shipping_price[{{ $key }}]" value="{{ $summary->customer_shipping_price }}" class="form-control" />
														@endif
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Carrier Type">Carrier Type</td>
													<td scope="row" data-placement="top">
														<select class="select2 col-md-6" id="carrier_type[{{ $key }}]" onchange="changeShipmentTypeInSubOrder(this, {{ $key }})">
															<option value="-1">Select</option>
															@foreach ($carr as $car)
																<option value="{{ $car->company_name }}" <?php if(isset($sub_order_ship_type[$key]['carrier_id']) && $sub_order_ship_type[$key]['carrier_id'] === $car->id) echo 'selected'; ?> >{{ $car->company_name }}</option>
															@endforeach										
														</select>
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Shipment Type">Shipment Type</td>
													<td scope="row" data-placement="top">
														<select class="select2 col-md-6" id="shipment_type[{{ $key }}]">
															<option value="">Select</option>
															@if (isset($sub_order_ship_type[$key]['carrier_name'])) && strtolower($sub_order_ship_type[$key]['carrier_name']) === 'ups')
																@foreach ($ups_st as $ups)
																	<option value="{{ $ups->id }}" <?php if (isset($sub_order_ship_type[$key]['service_type_id']) && $sub_order_ship_type[$key]['service_type_id'] == $ups->id) echo 'selected'; ?>>{{ $ups->service_name }}</option>
																@endforeach											
															@elseif (isset($sub_order_ship_type[$key]['carrier_name']) && strtolower($sub_order_ship_type[$key]['carrier_name']) === 'fedex')
																@foreach ($fedex_st as $fedex)
																	<option value="{{ $fedex->id }}" <?php if (isset($sub_order_ship_type[$key]['service_type_id']) && $sub_order_ship_type[$key]['service_type_id'] == $fedex->id) echo 'selected'; ?>>{{ $fedex->service_name }}</option>
																@endforeach
															@endif
														</select>
													</td>
												</tr>
											</tbody>
										</table>
										<button type="button" class="btn btn-primary float-right" onclick="updateSubOrderShipDetails({{ $key }})">Submit</button>
									</form>
								</div>
							</div>						
						</div>						
					@endforeach
				@endif
			</div>	
		</div>
	</div>
	<!-- end of col-->
</div>
<div class="modal fade" id="MyModalOrderItm" data-backdrop="static">
</div>


@endsection

@section('page-js')
<script src="{{ asset('assets/js/validation/jquery.validate.js') }}"></script>
<script src="{{ asset('assets/js/validation/additional-methods.min.js') }}"></script>
<script>
	

	$("#save_manual_order").validate({
        submitHandler(form){
            $(".submit").attr("disabled", true);
            $('div#preloader').show();
            var form_cust = $('#save_manual_order')[0];
            let form1 = new FormData(form_cust);
            $.ajax({
                type: "POST",
                url: '{{route('orders.update',[$order->id,$client_id])}}',
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


	function splitOrders(subOrder) {
		var markedCheckbox = document.getElementsByName('cb_' + subOrder);
		var ids = [];
		for (var checkbox of markedCheckbox) {  
			if (checkbox.checked) { 
				ids.push(checkbox.value);
			}
		}

		if (ids.length == 0) {
			return;
		}

		if (markedCheckbox.length == ids.length) {
			toastr.error("All values cannot be selected to split");
			return;
		}
		
		var form = new FormData();
		form.append('order_number', '{{ $summary->etailer_order_number }}');
		form.append('ids', ids);

		$.ajax({
			url: '{{route('orders.split_order')}}',
			method: 'POST',
			data: form,
			processData: false,
			contentType: false,
			success: function(res) {
				if(res.error === false){
					window.location.reload();
				} else {
					toastr.error(res.msg);
				}
			}			
		});
	}

	function mergeSubOrders() {

		var markedCheckbox = document.getElementsByName('mcb');
		var ids = [];
		for (var checkbox of markedCheckbox) {  
			if (checkbox.checked) { 
				ids.push(checkbox.value);
			}
		}

		if (ids.length == 0) {
			return;
		}

		var form = new FormData();
		form.append('order_number', '{{ $summary->etailer_order_number }}');
		form.append('ids', ids);

		$.ajax({
			url: '{{route('orders.merge_order')}}',
			method: 'POST',
			data: form,
			processData: false,
			contentType: false,
			success: function(res) {
				if(res.error === false) {
					$('#MyModalOrderItm').modal('hide');
					window.location.reload();
				} else {
					toastr.error(res.msg);
				}
			}			
		});
	}

	function changeShipmentTypeInSummary(type) {		
		var toAppend = 'Hello'
		if (type.value.toLowerCase() === 'fedex') {
			toAppend = @json($fedex_st);
		} else if (type.value.toLowerCase() === 'ups') {
			toAppend = @json($ups_st);
		} else {
			toAppend = [];
		}
		
		var select_elem = document.getElementById('sum_shipment_type');
		var options = select_elem.getElementsByTagName('option');
		for (var i = options.length; i--;) {	
			select_elem.removeChild(options[i]);
		}
		
		let opt = document.createElement("option");
		opt.value = ''; 
		opt.innerHTML = 'Select'; 
		select_elem.append(opt);
		
		for (var key in toAppend) {
			let opt = document.createElement("option");
			opt.value = toAppend[key].id; 
			opt.innerHTML = toAppend[key].service_name; 
			select_elem.append(opt); 
		}
	}

	function changeShipmentTypeInSubOrder(type, key) {
		var toAppend = 'Hello'
		if (type.value.toLowerCase() === 'fedex') {
			toAppend = @json($fedex_st);
		} else if (type.value.toLowerCase() === 'ups') {
			toAppend = @json($ups_st);
		} else {
			toAppend = [];
		}
		
		var select_elem = document.getElementById('shipment_type['+key+']');
		var options = select_elem.getElementsByTagName('option');
		for (var i = options.length; i--;) {	
			select_elem.removeChild(options[i]);
		}
		
		let opt = document.createElement("option");
		opt.value = ''; 
		opt.innerHTML = 'Select'; 
		select_elem.append(opt);
		
		for (var key in toAppend) {
			let opt = document.createElement("option");
			opt.value = toAppend[key].id; 
			opt.innerHTML = toAppend[key].service_name; 
			select_elem.append(opt); 
		}
	}

	function showKitItems(etin) {
		var trs = document.getElementsByName("kp_" + etin);
		if (trs) {
			for (var i = 0; i < trs.length; i++) {
				var tr = trs[i];
				if (tr.style.display === 'none') {
					tr.style.display = '';
					document.getElementById('show_ki_btn').innerHTML = "Hide Kit Items"
				} else if (tr.style.display === '') {
					tr.style.display = 'none';
					document.getElementById('show_ki_btn').innerHTML = "Show Kit Items"
				}
			}
		}
		console.log();
	}

	function showReshipOptions(subOrder) {

		var markedCheckbox = document.getElementsByName('cb_' + subOrder);
		var ids = [];
		for (var checkbox of markedCheckbox) {  
			var quant = document.getElementById('order_item[' + checkbox.value + ']').value;
			ids.push(checkbox.value + '#' + quant);
		}

		if (ids.length == 0) {
			toastr.error('Atleast one order must be selected to Re-Ship.');
			return;
		}

		var form = new FormData();
		form.append('sub_order', subOrder);
		form.append('ids', ids);
		form.append('order_number', '{{ $summary->etailer_order_number }}');

		$.ajax({
			url: '{{route('orders.reship_order_page')}}',
			method: 'POST',
			data: form,
			processData: false,
			contentType: false,
			success: function(res){
				$("#MyModalOrderItm").html(res);
				$("#MyModalOrderItm").modal();
			}
		});
	}

</script>
@endsection
