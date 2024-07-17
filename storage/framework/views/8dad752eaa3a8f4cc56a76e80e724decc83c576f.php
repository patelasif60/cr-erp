
<?php $__env->startSection('page-css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/datatables.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/sweetalert2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/inputTags.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main-content'); ?>
<style>
	.select2-container--open {
		z-index: 9999999
	}
</style>
<div class="breadcrumb">
	<h1>Cranium</h1>
	<ul>
		<li><a href="<?php echo e(route('orders.index')); ?>">Order Listing</a></li>
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
					<div class="col-md-10 text-right">
						<?php if(in_array($row->order_status,[17,18,20,21,22])): ?>
							<button type="button" class="btn btn-primary" style="margin-right: 10px;" onClick="GetModelOrderItem('<?php echo e(route('orders.ViewTrackingDetails',$row->etailer_order_number)); ?>')">View Tracking Details</button>
						<?php endif; ?>
						<button type="button" class="btn btn-primary" style="margin-right: 10px;" onClick="GetModelOrderItem('<?php echo e(route('orders.OrderHistory',$row->etailer_order_number)); ?>')">View Order History</button>
					</div>
				</div>
			</div>
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
									<select id="order_status" class="form-control select2" 
										<?php if ( in_array($summary->order_status, [17,18,20,21,22,24]) ) echo 'disabled' ?>>
										<option value='-2'> -- Select a value  -- </option>
										<?php if($status_present): ?>
											<?php if($statuses): ?>
												<?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($status->id); ?>" <?php if ($status->order_status_name == $summary->order_status_name) echo 'selected' ?>><?php echo e($status->order_status_name); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
										<?php else: ?>
											<option value='-1' selected><?php echo e($summary->order_status_name); ?></option>
											<?php if($statuses): ?>
												<?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($status->id); ?>"><?php echo e($status->order_status_name); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
										<?php endif; ?>
									</select>
								</td>
							</tr>
							<tr>
								<?php if(count($set_whs) > 1): ?>
									<td scope="row" data-placement="top" title="Warehouse">Warehouse - (<?php echo e(implode(',', $set_whs)); ?>)</td>
									<td>
										<select id="wh_assigned" class="form-control select2" name="wh_assigned" <?php if(!in_array($summary->order_status, [1, 2, 19, 23])) {echo 'disabled';} ?>>
											<?php if(!isset($wh_assigned) && count($wh_assigned) <= 0): ?>
												<option value=''>No Warehouse Found</option>
											<?php else: ?>
												<option value=''>Select</option>
												<?php if($wh_assigned): ?>												
													<?php $__currentLoopData = $wh_assigned; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>														
														<option value="<?php echo e($wh); ?>"><?php echo e($wh); ?> - <?php echo e(isset($t_day[$wh]) ? $t_day[$wh] : 'NA'); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												<?php endif; ?>
											<?php endif; ?>
										</select>
									</td>
								<?php else: ?>									
									<td scope="row" data-placement="top" title="Warehouse">Warehouse</td>
									<td>
										<select id="wh_assigned" class="form-control select2" name="wh_assigned" <?php if(!in_array($summary->order_status, [1, 2, 19, 23])) {echo 'disabled';} ?>>
											<?php if(!isset($wh_assigned) && count($wh_assigned) <= 0): ?>
												<option value=''>No Warehouse Found</option>
											<?php else: ?>
												<option value=''>Select</option>		
												<?php if($wh_assigned): ?>										
													<?php $__currentLoopData = $wh_assigned; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>														
														<option value="<?php echo e($wh); ?>" <?php if (in_array($wh, $set_whs)) echo 'selected'; ?> ><?php echo e($wh); ?> - <?php echo e(isset($t_day[$wh]) ? $t_day[$wh] : 'NA'); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												<?php endif; ?>
											<?php endif; ?>
										</select>
									</td>
								<?php endif; ?>
								<td>									
								</td>
							</tr>						
							<tr>
								<td scope="row" data-placement="top" title="e-tailer Order Number">e-tailer Order Number</td>
								<td scope="row" data-placement="top"><?php echo e($summary->etailer_order_number); ?></td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Channel Order Number">Channel / Manual Order Number</td>
								<td scope="row" data-placement="top"><?php echo e($summary->channel_order_number ? $summary->channel_order_number : 'NA'); ?></td>
							</tr>
							<?php if($summary->sa_order_number): ?>
							<tr>
								<td scope="row" data-placement="top" title="Channel Order Number">SA Order Number</td>
								<td scope="row" data-placement="top"><?php echo e($summary->sa_order_number); ?></td>
							</tr>
							<?php endif; ?>
							<tr>
								<td scope="row" data-placement="top" title="Order Source">Order Source</td>
								<td scope="row" data-placement="top"><?php echo e($summary->order_source); ?></td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Client">Client</td>
								<td scope="row" data-placement="top"><?php echo e($summary->client_name); ?></td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Purchase Date">Purchase Date</td>
								<td scope="row" data-placement="top"><?php echo e(date("m/d/Y g:i:s A", strtotime($summary->purchase_date))); ?></td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Channel Ship Date">Ship Date</td>
									<?php
									if($row->OrderPackage){
									$dates = $row->OrderPackage->pluck('ship_date');
									$formattedDates = $dates->map(function ($date) {
										return \Carbon\Carbon::parse($date)->format('m/d/Y');
									})->toArray();}
									?>
								<td scope="row" data-placement="top"><?php echo e(isset($row->OrderPackage) ? implode(', ',array_unique($formattedDates)) : ''); ?></td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Order Total Price">Order Total Price</td>
								<td scope="row" data-placement="top"><?php echo e(isset($summary->order_total_price) ? $summary->order_total_price : ''); ?></td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Must Ship Today">Must Ship Today</td>
								<td scope="row" data-placement="top">
									<select id="must_ship" class="form-control select2" <?php if ($summary->order_status_name !== 'Ready to Pick') echo 'disabled' ?>>
										<option value='1' <?php if (isset($summary->must_ship_today)) echo 'selected' ?>>Yes</option>
										<option value='0' <?php if (!isset($summary->must_ship_today) || $summary->must_ship_today === '') echo 'selected' ?>>No</option>										
									</select>
								</td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Hold Release Date">Hold Release Date</td>
								<td scope="row" data-placement="top">
									<input type="date" id="release_date" value="<?php echo e($summary->release_date); ?>" class="form-control" />
								</td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Order Type">Order Type</td>
								<td scope="row" data-placement="top">
									<?php if($summary->order_type_id == 1): ?>
										Auto
									<?php else: ?>
									<select id="order_type_id" name="order_type_id" class="form-control select2" onchange="toggleNonPickupWarehouse(this)" <?php if (!in_array($summary->order_status_name, array('New', 'New Manual'))) echo 'disabled' ?>>
										<?php if($ots): ?>
											<?php $__currentLoopData = $ots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value='<?php echo e($ot->id); ?>' <?php if ($summary->order_type_id == $ot->id) echo 'selected' ?>><?php echo e($ot->name); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
									</select>
									<?php endif; ?>
								</td>

							</tr>
							<tr class="otype_tr" style="display: none;">
								<td scope="row" data-placement="top" title="Warehouse">Warehouse</td>
								<td>
									<select id="wh_np" class="form-control select2" name="wh_np">
										<?php if($whs): ?>
											<?php $__currentLoopData = $whs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($wh->warehouses); ?>" <?php if ($np_wh == $wh->warehouses) echo 'selected' ?>><?php echo e($wh->warehouses); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
									</select>
								</td>
							</tr>
							<tr class="otype_tr" style="display: none;">
								<td scope="row" data-placement="top" title="PO #">PO #</td>
								<td>
									<input type="text" name="po_number" id="po_number" value="<?php echo e($summary->po_number); ?>" class="form-control">
								</td>
							</tr>
							<tr class="otype_tr" style="display: none;">
								<td scope="row" data-placement="top" title="BOL #">BOL #</td>
								<td>
									<input type="text" name="bol_number" id="bol_number" value="<?php echo e($summary->bol_number); ?>" class="form-control">
								</td>
							</tr>
							<tr>
								<td scope="row">Receive Notification</td>
								<td>
									<select id="receive_notification" class="form-control" name="receive_notification">
										<option value="0" <?php if ($summary->receive_notification == '0') echo 'selected' ?>>No</option>
										<option value="1" <?php if ($summary->receive_notification == '1') echo 'selected' ?>>Yes</option>
									</select>
								</td>
							</tr>
							<tr>
								<td scope="row">Gift Message</td>
								<td scope="row" data-placement="top">
									<textarea id="gift_message" 
										name="gift_message" 
										class="form-control" 
										rows="3"><?php echo e(isset($summary->gift_message) ? trim($summary->gift_message) : ''); ?></textarea>									
								</td>
							</tr>
							<tr>
								<td scope="row">Saturday Eligible</td>
								<td scope="row" data-placement="top">
									<select id="sat_elli" class="form-control select2">
										<option value='1' <?php if ($summary->saturday_eligible == 1) echo 'selected' ?>>Yes</option>
										<option value='0' <?php if ($summary->saturday_eligible == 0) echo 'selected' ?>>No</option>
									</select>							
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<?php if(!in_array($summary->order_status,[17,18,20,21,22])): ?>									
										<a class="btn btn-primary text-white float-right" href="javascript:void(0)" onClick="updateStatus()">Update Status</a>
										<?php if(in_array($summary->order_status,[1,2,19,23])): ?>
											<?php if(isset($sub_order_ship_type) && count($sub_order_ship_type) > 0): ?>
												<a style="margin-right: 10px;" class="btn btn-primary text-white float-right" href="javascript:void(0)" onClick="GetModelOrderItem('<?php echo e(route('orders.sub_orders',$row->etailer_order_number)); ?>')">Merge Sub-Orders</a>
											<?php endif; ?>
										<?php endif; ?>
									<?php endif; ?>
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
									<input type="text" id="customer_name" value="<?php echo e($summary->customer_name); ?>" class="form-control" />
								</td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="E-Mail">E-Mail</td>
								<td scope="row" data-placement="top">
									<input type="text" id="customer_email" value="<?php echo e($summary->customer_email); ?>" class="form-control" />
								</td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Phone">Phone</td>
								<td scope="row" data-placement="top">
									<input type="text" id="customer_number" value="<?php echo e($summary->customer_number); ?>" class="form-control" />
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<?php if(!in_array($summary->order_status,[17,18,20,21,22])): ?>
										<a class="btn btn-primary text-white float-right" href="javascript:void(0)" onClick="updateShippingAndCustomerDetails(1)">Update Customer Details</a>
									<?php endif; ?>
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
								<td scope="row" data-placement="top" title="Shipment Type">Carrier Type</td>
								<?php if($summary->order_type_id === 3): ?>
									<td scope="row" data-placement="top">Non-Person Pickup</td>
								<?php else: ?>
									<td scope="row" data-placement="top">
										<select class="select2 col-md-6" id="sum_carrier" onchange="changeShipmentTypeInSummary(this)" <?php if (!in_array($summary->order_status_name, array('New', 'New Manual', 'Ready to Pick'))) echo 'disabled' ?>>
											<option value="-1">Select</option>
											<?php if($carr): ?>
												<?php $__currentLoopData = $carr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($car->company_name); ?>" <?php if (isset($summary->carrier_id) && $summary->carrier_id == $car->id) echo 'selected'; ?>><?php echo e($car->company_name); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>										
											<?php endif; ?>
										</select>
									</td>
								<?php endif; ?>								
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Shipment Type">Shipment Type</td>
								<?php if($summary->order_type_id === 3): ?>
									<td scope="row" data-placement="top">Non-Person Pickup</td>
								<?php else: ?>
									<td scope="row" data-placement="top">
										<select class="select2 col-md-6" id="sum_shipment_type" <?php if (!in_array($summary->order_status_name, array('New', 'New Manual', 'Ready to Pick'))) echo 'disabled' ?>>
											<option value="">Select</option>
											<?php if(isset($summary->carrier_name) && strtolower($summary->carrier_name) === 'ups'): ?>
												<?php if($etailer_services): ?>
														<?php $__currentLoopData = $etailer_services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ups): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<?php if(isset($ups->upsShippingServiceType->service_name)): ?>
																<option value="<?php echo e($ups->id); ?>" <?php if (isset($ups->upsShippingServiceType->id) && $summary->shipment_type == $ups->upsShippingServiceType->id) echo 'selected'; ?>><?php if (isset($ups->upsShippingServiceType->service_name)) echo $ups->upsShippingServiceType->service_name; ?></option>
															<?php endif; ?>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>			
												<?php endif; ?>								
											<?php elseif(isset($summary->carrier_name) && strtolower($summary->carrier_name) === 'fedex'): ?>
												<?php if($etailer_services): ?>
													<?php $__currentLoopData = $etailer_services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fedex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<?php if(isset($fedex->fdxShippingServiceType->service_name)): ?>
															<option value="<?php echo e($fedex->id); ?>" <?php if (isset($fedex->fdxShippingServiceType->id) && $summary->shipment_type == $fedex->fdxShippingServiceType->id) echo 'selected'; ?>><?php echo e($fedex->fdxShippingServiceType->service_name); ?></option>
														<?php endif; ?>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												<?php endif; ?>
											<?php endif; ?>


										</select>
									</td>
								<?php endif; ?>								
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Ship To Name">Ship To Name</td>
								<td scope="row" data-placement="top">
									<input type="text" id="ship_to_name" value="<?php echo e($summary->ship_to_name); ?>" class="form-control" />
								</td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Ship To Address Type">Ship To Address Type</td>
								<td scope="row" data-placement="top">
									<input type="text" id="ship_to_address_type" value="<?php echo e($summary->ship_to_address_type); ?>" class="form-control" />
								</td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Ship To Address1">Ship To Address1</td>
								<td scope="row" data-placement="top">
									<input type="text" id="ship_to_address1" value="<?php echo e($summary->ship_to_address1); ?>" class="form-control" />
								</td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Ship To Address2">Ship To Address2</td>
								<td scope="row" data-placement="top">
									<input type="text" id="ship_to_address2" value="<?php echo e($summary->ship_to_address2); ?>" class="form-control" />
								</td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Ship To Address3">Ship To Address3</td>
								<td scope="row" data-placement="top">
									<input type="text" id="ship_to_address3" value="<?php echo e($summary->ship_to_address3); ?>" class="form-control" />
								</td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Ship To City">Ship To City</td>
								<td scope="row" data-placement="top">
									<input type="text" id="ship_to_city" value="<?php echo e($summary->ship_to_city); ?>" class="form-control" />
								</td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Ship To State">Ship To State</td>
								<td scope="row" data-placement="top">
									<input type="text" id="ship_to_state" value="<?php echo e($summary->ship_to_state); ?>" class="form-control" />
								</td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Ship To Zip">Ship To Zip</td>
								<td scope="row" data-placement="top">
									<input type="text" id="ship_to_zip" value="<?php echo e($summary->ship_to_zip); ?>" class="form-control" />
								</td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Ship To Counter">Ship To Country</td>
								<td scope="row" data-placement="top">
									<select id="ship_to_country" name="ship_to_country" class="form-control">
										<option value="AF" <?php if($summary->ship_to_country == 'AF') echo "selected"; ?> >Afghanistan</option>
										<option value="AX" <?php if($summary->ship_to_country == 'AX') echo "selected"; ?> >Åland Islands</option>
										<option value="AL" <?php if($summary->ship_to_country == 'AL') echo "selected"; ?> >Albania</option>
										<option value="DZ" <?php if($summary->ship_to_country == 'DZ') echo "selected"; ?>>Algeria</option>
										<option value="AS" <?php if($summary->ship_to_country == 'AS') echo "selected"; ?>>American Samoa</option>
										<option value="AD" <?php if($summary->ship_to_country == 'AD') echo "selected"; ?>>Andorra</option>
										<option value="AO" <?php if($summary->ship_to_country == 'AO') echo "selected"; ?>>Angola</option>
										<option value="AI" <?php if($summary->ship_to_country == 'AI') echo "selected"; ?>>Anguilla</option>
										<option value="AQ" <?php if($summary->ship_to_country == 'AQ') echo "selected"; ?>>Antarctica</option>
										<option value="AG" <?php if($summary->ship_to_country == 'AG') echo "selected"; ?>>Antigua and Barbuda</option>
										<option value="AR" <?php if($summary->ship_to_country == 'AR') echo "selected"; ?>>Argentina</option>
										<option value="AM" <?php if($summary->ship_to_country == 'AM') echo "selected"; ?>>Armenia</option>
										<option value="AW" <?php if($summary->ship_to_country == 'AW') echo "selected"; ?>>Aruba</option>
										<option value="AU" <?php if($summary->ship_to_country == 'AU') echo "selected"; ?>>Australia</option>
										<option value="AT" <?php if($summary->ship_to_country == 'AT') echo "selected"; ?>>Austria</option>
										<option value="AZ" <?php if($summary->ship_to_country == 'AZ') echo "selected"; ?>>Azerbaijan</option>
										<option value="BS" <?php if($summary->ship_to_country == 'BS') echo "selected"; ?>>Bahamas</option>
										<option value="BH" <?php if($summary->ship_to_country == 'BH') echo "selected"; ?>>Bahrain</option>
										<option value="BD" <?php if($summary->ship_to_country == 'BD') echo "selected"; ?>>Bangladesh</option>
										<option value="BB" <?php if($summary->ship_to_country == 'BB') echo "selected"; ?>>Barbados</option>
										<option value="BY" <?php if($summary->ship_to_country == 'BY') echo "selected"; ?>>Belarus</option>
										<option value="BE" <?php if($summary->ship_to_country == 'BE') echo "selected"; ?>>Belgium</option>
										<option value="BZ" <?php if($summary->ship_to_country == 'BZ') echo "selected"; ?>>Belize</option>
										<option value="BJ" <?php if($summary->ship_to_country == 'BJ') echo "selected"; ?>>Benin</option>
										<option value="BM" <?php if($summary->ship_to_country == 'BM') echo "selected"; ?>>Bermuda</option>
										<option value="BT" <?php if($summary->ship_to_country == 'BT') echo "selected"; ?>>Bhutan</option>
										<option value="BO" <?php if($summary->ship_to_country == 'BO') echo "selected"; ?>>Bolivia, Plurinational State of</option>
										<option value="BQ" <?php if($summary->ship_to_country == 'BQ') echo "selected"; ?>>Bonaire, Sint Eustatius and Saba</option>
										<option value="BA" <?php if($summary->ship_to_country == 'BA') echo "selected"; ?>>Bosnia and Herzegovina</option>
										<option value="BW" <?php if($summary->ship_to_country == 'BW') echo "selected"; ?>>Botswana</option>
										<option value="BV" <?php if($summary->ship_to_country == 'BV') echo "selected"; ?>>Bouvet Island</option>
										<option value="BR" <?php if($summary->ship_to_country == 'BR') echo "selected"; ?>>Brazil</option>
										<option value="IO" <?php if($summary->ship_to_country == 'IO') echo "selected"; ?>>British Indian Ocean Territory</option>
										<option value="BN" <?php if($summary->ship_to_country == 'BN') echo "selected"; ?>>Brunei Darussalam</option>
										<option value="BG" <?php if($summary->ship_to_country == 'BG') echo "selected"; ?>>Bulgaria</option>
										<option value="BF" <?php if($summary->ship_to_country == 'BF') echo "selected"; ?>>Burkina Faso</option>
										<option value="BI" <?php if($summary->ship_to_country == 'BI') echo "selected"; ?>>Burundi</option>
										<option value="KH" <?php if($summary->ship_to_country == 'KH') echo "selected"; ?>>Cambodia</option>
										<option value="CM" <?php if($summary->ship_to_country == 'CM') echo "selected"; ?>>Cameroon</option>
										<option value="CA" <?php if($summary->ship_to_country == 'CA') echo "selected"; ?>>Canada</option>
										<option value="CV" <?php if($summary->ship_to_country == 'CV') echo "selected"; ?>>Cape Verde</option>
										<option value="KY" <?php if($summary->ship_to_country == 'KY') echo "selected"; ?>>Cayman Islands</option>
										<option value="CF" <?php if($summary->ship_to_country == 'CF') echo "selected"; ?>>Central African Republic</option>
										<option value="TD" <?php if($summary->ship_to_country == 'TD') echo "selected"; ?>>Chad</option>
										<option value="CL" <?php if($summary->ship_to_country == 'CL') echo "selected"; ?>>Chile</option>
										<option value="CN" <?php if($summary->ship_to_country == 'CN') echo "selected"; ?>>China</option>
										<option value="CX" <?php if($summary->ship_to_country == 'CX') echo "selected"; ?>>Christmas Island</option>
										<option value="CC" <?php if($summary->ship_to_country == 'CC') echo "selected"; ?>>Cocos (Keeling) Islands</option>
										<option value="CO" <?php if($summary->ship_to_country == 'CO') echo "selected"; ?>>Colombia</option>
										<option value="KM" <?php if($summary->ship_to_country == 'KM') echo "selected"; ?>>Comoros</option>
										<option value="CG" <?php if($summary->ship_to_country == 'CG') echo "selected"; ?>>Congo</option>
										<option value="CD" <?php if($summary->ship_to_country == 'CD') echo "selected"; ?>>Congo, the Democratic Republic of the</option>
										<option value="CK" <?php if($summary->ship_to_country == 'CK') echo "selected"; ?>>Cook Islands</option>
										<option value="CR" <?php if($summary->ship_to_country == 'CR') echo "selected"; ?>>Costa Rica</option>
										<option value="CI" <?php if($summary->ship_to_country == 'CI') echo "selected"; ?>>Côte d'Ivoire</option>
										<option value="HR" <?php if($summary->ship_to_country == 'HR') echo "selected"; ?>>Croatia</option>
										<option value="CU" <?php if($summary->ship_to_country == 'CU') echo "selected"; ?>>Cuba</option>
										<option value="CW" <?php if($summary->ship_to_country == 'CW') echo "selected"; ?>>Curaçao</option>
										<option value="CY" <?php if($summary->ship_to_country == 'CY') echo "selected"; ?>>Cyprus</option>
										<option value="CZ" <?php if($summary->ship_to_country == 'CZ') echo "selected"; ?>>Czech Republic</option>
										<option value="DK" <?php if($summary->ship_to_country == 'DK') echo "selected"; ?>>Denmark</option>
										<option value="DJ" <?php if($summary->ship_to_country == 'DJ') echo "selected"; ?>>Dji <?php if($summary->ship_to_country == 'AF') echo "selected"; ?>bouti</option>
										<option value="DM" <?php if($summary->ship_to_country == 'DM') echo "selected"; ?>>Dominica</option>
										<option value="DO" <?php if($summary->ship_to_country == 'DO') echo "selected"; ?>>Dominican Republic</option>
										<option value="EC" <?php if($summary->ship_to_country == 'EC') echo "selected"; ?>>Ecuador</option>
										<option value="EG" <?php if($summary->ship_to_country == 'EG') echo "selected"; ?>>Egypt</option>
										<option value="SV" <?php if($summary->ship_to_country == 'SV') echo "selected"; ?>>El Salvador</option>
										<option value="GQ" <?php if($summary->ship_to_country == 'GQ') echo "selected"; ?>>Equatorial Guinea</option>
										<option value="ER" <?php if($summary->ship_to_country == 'ER') echo "selected"; ?>>Eritrea</option>
										<option value="EE" <?php if($summary->ship_to_country == 'EE') echo "selected"; ?>>Estonia</option>
										<option value="ET" <?php if($summary->ship_to_country == 'ET') echo "selected"; ?>>Ethiopia</option>
										<option value="FK" <?php if($summary->ship_to_country == 'FK') echo "selected"; ?>>Falkland Islands (Malvinas)</option>
										<option value="FO" <?php if($summary->ship_to_country == 'FO') echo "selected"; ?>>Faroe Islands</option>
										<option value="FJ" <?php if($summary->ship_to_country == 'FJ') echo "selected"; ?>>Fiji</option>
										<option value="FI" <?php if($summary->ship_to_country == 'FI') echo "selected"; ?>>Finland</option>
										<option value="FR" <?php if($summary->ship_to_country == 'FR') echo "selected"; ?>>France</option>
										<option value="GF" <?php if($summary->ship_to_country == 'GF') echo "selected"; ?>>French Guiana</option>
										<option value="PF" <?php if($summary->ship_to_country == 'PF') echo "selected"; ?>>French Polynesia</option>
										<option value="TF" <?php if($summary->ship_to_country == 'TF') echo "selected"; ?>>French Southern Territories</option>
										<option value="GA" <?php if($summary->ship_to_country == 'GA') echo "selected"; ?>>Gabon</option>
										<option value="GM" <?php if($summary->ship_to_country == 'GM') echo "selected"; ?>>Gambia</option>
										<option value="GE" <?php if($summary->ship_to_country == 'GE') echo "selected"; ?>>Georgia</option>
										<option value="DE" <?php if($summary->ship_to_country == 'DE') echo "selected"; ?>>Germany</option>
										<option value="GH" <?php if($summary->ship_to_country == 'GH') echo "selected"; ?>>Ghana</option>
										<option value="GI" <?php if($summary->ship_to_country == 'GI') echo "selected"; ?>>Gibraltar</option>
										<option value="GR" <?php if($summary->ship_to_country == 'GR') echo "selected"; ?>>Greece</option>
										<option value="GL" <?php if($summary->ship_to_country == 'GL') echo "selected"; ?>>Greenland</option>
										<option value="GD" <?php if($summary->ship_to_country == 'GD') echo "selected"; ?>>Grenada</option>
										<option value="GP" <?php if($summary->ship_to_country == 'GP') echo "selected"; ?>>Guadeloupe</option>
										<option value="GU" <?php if($summary->ship_to_country == 'GU') echo "selected"; ?>>Guam</option>
										<option value="GT" <?php if($summary->ship_to_country == 'GT') echo "selected"; ?>>Guatemala</option>
										<option value="GG" <?php if($summary->ship_to_country == 'GG') echo "selected"; ?>>Guernsey</option>
										<option value="GN" <?php if($summary->ship_to_country == 'GN') echo "selected"; ?>>Guinea</option>
										<option value="GW" <?php if($summary->ship_to_country == 'GW') echo "selected"; ?>>Guinea-Bissau</option>
										<option value="GY" <?php if($summary->ship_to_country == 'GY') echo "selected"; ?>>Guyana</option>
										<option value="HT" <?php if($summary->ship_to_country == 'HT') echo "selected"; ?>>Haiti</option>
										<option value="HM" <?php if($summary->ship_to_country == 'HM') echo "selected"; ?>>Heard Island and McDonald Islands</option>
										<option value="VA" <?php if($summary->ship_to_country == 'VA') echo "selected"; ?>>Holy See (Vatican City State)</option>
										<option value="HN" <?php if($summary->ship_to_country == 'HN') echo "selected"; ?>>Honduras</option>
										<option value="HK" <?php if($summary->ship_to_country == 'HK') echo "selected"; ?>>Hong Kong</option>
										<option value="HU" <?php if($summary->ship_to_country == 'HU') echo "selected"; ?>>Hungary</option>
										<option value="IS" <?php if($summary->ship_to_country == 'IS') echo "selected"; ?>>Iceland</option>
										<option value="IN" <?php if($summary->ship_to_country == 'IN') echo "selected"; ?>>India</option>
										<option value="ID" <?php if($summary->ship_to_country == 'ID') echo "selected"; ?>>Indonesia</option>
										<option value="IR" <?php if($summary->ship_to_country == 'IR') echo "selected"; ?>>Iran, Islamic Republic of</option>
										<option value="IQ" <?php if($summary->ship_to_country == 'IQ') echo "selected"; ?>>Iraq</option>
										<option value="IE" <?php if($summary->ship_to_country == 'IE') echo "selected"; ?>>Ireland</option>
										<option value="IM" <?php if($summary->ship_to_country == 'IM') echo "selected"; ?>>Isle of Man</option>
										<option value="IL" <?php if($summary->ship_to_country == 'IL') echo "selected"; ?>>Israel</option>
										<option value="IT" <?php if($summary->ship_to_country == 'IT') echo "selected"; ?>>Italy</option>
										<option value="JM" <?php if($summary->ship_to_country == 'JM') echo "selected"; ?>>Jamaica</option>
										<option value="JP" <?php if($summary->ship_to_country == 'JP') echo "selected"; ?>>Japan</option>
										<option value="JE" <?php if($summary->ship_to_country == 'JE') echo "selected"; ?>>Jersey</option>
										<option value="JO" <?php if($summary->ship_to_country == 'JO') echo "selected"; ?>>Jordan</option>
										<option value="KZ" <?php if($summary->ship_to_country == 'KZ') echo "selected"; ?>>Kazakhstan</option>
										<option value="KE" <?php if($summary->ship_to_country == 'KE') echo "selected"; ?>>Kenya</option>
										<option value="KI" <?php if($summary->ship_to_country == 'KI') echo "selected"; ?>>Kiribati</option>
										<option value="KP" <?php if($summary->ship_to_country == 'KP') echo "selected"; ?>>Korea, Democratic People's Republic of</option>
										<option value="KR" <?php if($summary->ship_to_country == 'KR') echo "selected"; ?>>Korea, Republic of</option>
										<option value="KW" <?php if($summary->ship_to_country == 'KW') echo "selected"; ?>>Kuwait</option>
										<option value="KG" <?php if($summary->ship_to_country == 'KG') echo "selected"; ?>>Kyrgyzstan</option>
										<option value="LA" <?php if($summary->ship_to_country == 'LA') echo "selected"; ?>>Lao People's Democratic Republic</option>
										<option value="LV" <?php if($summary->ship_to_country == 'LV') echo "selected"; ?>>Latvia</option>
										<option value="LB" <?php if($summary->ship_to_country == 'LB') echo "selected"; ?>>Lebanon</option>
										<option value="LS" <?php if($summary->ship_to_country == 'LS') echo "selected"; ?>>Lesotho</option>
										<option value="LR" <?php if($summary->ship_to_country == 'LR') echo "selected"; ?>>Liberia</option>
										<option value="LY" <?php if($summary->ship_to_country == 'LY') echo "selected"; ?>>Libya</option>
										<option value="LI" <?php if($summary->ship_to_country == 'LI') echo "selected"; ?>>Liechtenstein</option>
										<option value="LT" <?php if($summary->ship_to_country == 'LT') echo "selected"; ?>>Lithuania</option>
										<option value="LU" <?php if($summary->ship_to_country == 'LU') echo "selected"; ?>>Luxembourg</option>
										<option value="MO" <?php if($summary->ship_to_country == 'MO') echo "selected"; ?>>Macao</option>
										<option value="MK" <?php if($summary->ship_to_country == 'MK') echo "selected"; ?>>Macedonia, the former Yugoslav Republic of</option>
										<option value="MG" <?php if($summary->ship_to_country == 'MG') echo "selected"; ?>>Madagascar</option>
										<option value="MW" <?php if($summary->ship_to_country == 'MW') echo "selected"; ?>>Malawi</option>
										<option value="MY" <?php if($summary->ship_to_country == 'MY') echo "selected"; ?>>Malaysia</option>
										<option value="MV" <?php if($summary->ship_to_country == 'MV') echo "selected"; ?>>Maldives</option>
										<option value="ML" <?php if($summary->ship_to_country == 'ML') echo "selected"; ?>>Mali</option>
										<option value="MT" <?php if($summary->ship_to_country == 'MT') echo "selected"; ?>>Malta</option>
										<option value="MH" <?php if($summary->ship_to_country == 'MH') echo "selected"; ?>>Marshall Islands</option>
										<option value="MQ" <?php if($summary->ship_to_country == 'MQ') echo "selected"; ?>>Martinique</option>
										<option value="MR" <?php if($summary->ship_to_country == 'MR') echo "selected"; ?>>Mauritania</option>
										<option value="MU" <?php if($summary->ship_to_country == 'MU') echo "selected"; ?>>Mauritius</option>
										<option value="YT" <?php if($summary->ship_to_country == 'YT') echo "selected"; ?>>Mayotte</option>
										<option value="MX" <?php if($summary->ship_to_country == 'MX') echo "selected"; ?>>Mexico</option>
										<option value="FM" <?php if($summary->ship_to_country == 'FM') echo "selected"; ?>>Micronesia, Federated States of</option>
										<option value="MD" <?php if($summary->ship_to_country == 'MD') echo "selected"; ?>>Moldova, Republic of</option>
										<option value="MC" <?php if($summary->ship_to_country == 'MC') echo "selected"; ?>>Monaco</option>
										<option value="MN" <?php if($summary->ship_to_country == 'MN') echo "selected"; ?>>Mongolia</option>
										<option value="ME" <?php if($summary->ship_to_country == 'ME') echo "selected"; ?>>Montenegro</option>
										<option value="MS" <?php if($summary->ship_to_country == 'MS') echo "selected"; ?>>Montserrat</option>
										<option value="MA" <?php if($summary->ship_to_country == 'MA') echo "selected"; ?>>Morocco</option>
										<option value="MZ" <?php if($summary->ship_to_country == 'MZ') echo "selected"; ?>>Mozambique</option>
										<option value="MM" <?php if($summary->ship_to_country == 'MM') echo "selected"; ?>>Myanmar</option>
										<option value="NA" <?php if($summary->ship_to_country == 'NA') echo "selected"; ?>>Namibia</option>
										<option value="NR" <?php if($summary->ship_to_country == 'NR') echo "selected"; ?>>Nauru</option>
										<option value="NP" <?php if($summary->ship_to_country == 'NP') echo "selected"; ?>>Nepal</option>
										<option value="NL" <?php if($summary->ship_to_country == 'NL') echo "selected"; ?>>Netherlands</option>
										<option value="NC" <?php if($summary->ship_to_country == 'NC') echo "selected"; ?>>New Caledonia</option>
										<option value="NZ" <?php if($summary->ship_to_country == 'NZ') echo "selected"; ?>>New Zealand</option>
										<option value="NI" <?php if($summary->ship_to_country == 'NI') echo "selected"; ?>>Nicaragua</option>
										<option value="NE" <?php if($summary->ship_to_country == 'NE') echo "selected"; ?>>Niger</option>
										<option value="NG" <?php if($summary->ship_to_country == 'NG') echo "selected"; ?>>Nigeria</option>
										<option value="NU" <?php if($summary->ship_to_country == 'NU') echo "selected"; ?>>Niue</option>
										<option value="NF" <?php if($summary->ship_to_country == 'NF') echo "selected"; ?>>Norfolk Island</option>
										<option value="MP" <?php if($summary->ship_to_country == 'MP') echo "selected"; ?>>Northern Mariana Islands</option>
										<option value="NO" <?php if($summary->ship_to_country == 'NO') echo "selected"; ?>>Norway</option>
										<option value="OM" <?php if($summary->ship_to_country == 'OM') echo "selected"; ?>>Oman</option>
										<option value="PK" <?php if($summary->ship_to_country == 'PK') echo "selected"; ?>>Pakistan</option>
										<option value="PW" <?php if($summary->ship_to_country == 'PW') echo "selected"; ?>>Palau</option>
										<option value="PS" <?php if($summary->ship_to_country == 'PS') echo "selected"; ?>>Palestinian Territory, Occupied</option>
										<option value="PA" <?php if($summary->ship_to_country == 'PA') echo "selected"; ?>>Panama</option>
										<option value="PG" <?php if($summary->ship_to_country == 'PG') echo "selected"; ?>>Papua New Guinea</option>
										<option value="PY" <?php if($summary->ship_to_country == 'PY') echo "selected"; ?>>Paraguay</option>
										<option value="PE" <?php if($summary->ship_to_country == 'PE') echo "selected"; ?>>Peru</option>
										<option value="PH" <?php if($summary->ship_to_country == 'PH') echo "selected"; ?>>Philippines</option>
										<option value="PN" <?php if($summary->ship_to_country == 'PN') echo "selected"; ?>>Pitcairn</option>
										<option value="PL" <?php if($summary->ship_to_country == 'PL') echo "selected"; ?>>Poland</option>
										<option value="PT" <?php if($summary->ship_to_country == 'PT') echo "selected"; ?>>Portugal</option>
										<option value="PR" <?php if($summary->ship_to_country == 'PR') echo "selected"; ?>>Puerto Rico</option>
										<option value="QA" <?php if($summary->ship_to_country == 'QA') echo "selected"; ?>>Qatar</option>
										<option value="RE" <?php if($summary->ship_to_country == 'RE') echo "selected"; ?>>Réunion</option>
										<option value="RO" <?php if($summary->ship_to_country == 'RO') echo "selected"; ?>>Romania</option>
										<option value="RU" <?php if($summary->ship_to_country == 'RU') echo "selected"; ?>>Russian Federation</option>
										<option value="RW" <?php if($summary->ship_to_country == 'RW') echo "selected"; ?>>Rwanda</option>
										<option value="BL" <?php if($summary->ship_to_country == 'BL') echo "selected"; ?>>Saint Barthélemy</option>
										<option value="SH" <?php if($summary->ship_to_country == 'SH') echo "selected"; ?>>Saint Helena, Ascension and Tristan da Cunha</option>
										<option value="KN" <?php if($summary->ship_to_country == 'KN') echo "selected"; ?>>Saint Kitts and Nevis</option>
										<option value="LC" <?php if($summary->ship_to_country == 'LC') echo "selected"; ?>>Saint Lucia</option>
										<option value="MF" <?php if($summary->ship_to_country == 'MF') echo "selected"; ?>>Saint Martin (French part)</option>
										<option value="PM" <?php if($summary->ship_to_country == 'PM') echo "selected"; ?>>Saint Pierre and Miquelon</option>
										<option value="VC" <?php if($summary->ship_to_country == 'VC') echo "selected"; ?>>Saint Vincent and the Grenadines</option>
										<option value="WS" <?php if($summary->ship_to_country == 'WS') echo "selected"; ?>>Samoa</option>
										<option value="SM" <?php if($summary->ship_to_country == 'SM') echo "selected"; ?>>San Marino</option>
										<option value="ST" <?php if($summary->ship_to_country == 'ST') echo "selected"; ?>>Sao Tome and Principe</option>
										<option value="SA" <?php if($summary->ship_to_country == 'SA') echo "selected"; ?>>Saudi Arabia</option>
										<option value="SN" <?php if($summary->ship_to_country == 'SN') echo "selected"; ?>>Senegal</option>
										<option value="RS" <?php if($summary->ship_to_country == 'RS') echo "selected"; ?>>Serbia</option>
										<option value="SC" <?php if($summary->ship_to_country == 'SC') echo "selected"; ?>>Seychelles</option>
										<option value="SL" <?php if($summary->ship_to_country == 'SL') echo "selected"; ?>>Sierra Leone</option>
										<option value="SG" <?php if($summary->ship_to_country == 'SG') echo "selected"; ?>>Singapore</option>
										<option value="SX" <?php if($summary->ship_to_country == 'SX') echo "selected"; ?>>Sint Maarten (Dutch part)</option>
										<option value="SK" <?php if($summary->ship_to_country == 'SK') echo "selected"; ?>>Slovakia</option>
										<option value="SI" <?php if($summary->ship_to_country == 'SI') echo "selected"; ?>>Slovenia</option>
										<option value="SB" <?php if($summary->ship_to_country == 'SB') echo "selected"; ?>>Solomon Islands</option>
										<option value="SO" <?php if($summary->ship_to_country == 'SO') echo "selected"; ?>>Somalia</option>
										<option value="ZA" <?php if($summary->ship_to_country == 'ZA') echo "selected"; ?>>South Africa</option>
										<option value="GS" <?php if($summary->ship_to_country == 'GS') echo "selected"; ?>>South Georgia and the South Sandwich Islands</option>
										<option value="SS" <?php if($summary->ship_to_country == 'SS') echo "selected"; ?>>South Sudan</option>
										<option value="ES" <?php if($summary->ship_to_country == 'ES') echo "selected"; ?>>Spain</option>
										<option value="LK" <?php if($summary->ship_to_country == 'LK') echo "selected"; ?>>Sri Lanka</option>
										<option value="SD" <?php if($summary->ship_to_country == 'SD') echo "selected"; ?>>Sudan</option>
										<option value="SR" <?php if($summary->ship_to_country == 'SR') echo "selected"; ?>>Suriname</option>
										<option value="SJ" <?php if($summary->ship_to_country == 'SJ') echo "selected"; ?>>Svalbard and Jan Mayen</option>
										<option value="SZ" <?php if($summary->ship_to_country == 'SZ') echo "selected"; ?>>Swaziland</option>
										<option value="SE" <?php if($summary->ship_to_country == 'SE') echo "selected"; ?>>Sweden</option>
										<option value="CH" <?php if($summary->ship_to_country == 'CH') echo "selected"; ?>>Switzerland</option>
										<option value="SY" <?php if($summary->ship_to_country == 'SY') echo "selected"; ?>>Syrian Arab Republic</option>
										<option value="TW" <?php if($summary->ship_to_country == 'TW') echo "selected"; ?>>Taiwan, Province of China</option>
										<option value="TJ" <?php if($summary->ship_to_country == 'TJ') echo "selected"; ?>>Tajikistan</option>
										<option value="TZ" <?php if($summary->ship_to_country == 'TZ') echo "selected"; ?>>Tanzania, United Republic of</option>
										<option value="TH" <?php if($summary->ship_to_country == 'TH') echo "selected"; ?>>Thailand</option>
										<option value="TL" <?php if($summary->ship_to_country == 'TL') echo "selected"; ?>>Timor-Leste</option>
										<option value="TG" <?php if($summary->ship_to_country == 'TG') echo "selected"; ?>>Togo</option>
										<option value="TK" <?php if($summary->ship_to_country == 'TK') echo "selected"; ?>>Tokelau</option>
										<option value="TO" <?php if($summary->ship_to_country == 'TO') echo "selected"; ?>>Tonga</option>
										<option value="TT" <?php if($summary->ship_to_country == 'TT') echo "selected"; ?>>Trinidad and Tobago</option>
										<option value="TN" <?php if($summary->ship_to_country == 'TN') echo "selected"; ?>>Tunisia</option>
										<option value="TR" <?php if($summary->ship_to_country == 'TR') echo "selected"; ?>>Turkey</option>
										<option value="TM" <?php if($summary->ship_to_country == 'TM') echo "selected"; ?>>Turkmenistan</option>
										<option value="TC" <?php if($summary->ship_to_country == 'TC') echo "selected"; ?>>Turks and Caicos Islands</option>
										<option value="TV" <?php if($summary->ship_to_country == 'TV') echo "selected"; ?>>Tuvalu</option>
										<option value="UG" <?php if($summary->ship_to_country == 'UG') echo "selected"; ?>>Uganda</option>
										<option value="UA" <?php if($summary->ship_to_country == 'UA') echo "selected"; ?>>Ukraine</option>
										<option value="AE" <?php if($summary->ship_to_country == 'AE') echo "selected"; ?>>United Arab Emirates</option>
										<option value="GB" <?php if($summary->ship_to_country == 'GB') echo "selected"; ?>>United Kingdom</option>
										<option value="US" <?php if($summary->ship_to_country == 'US') echo "selected"; ?>>United States</option>
										<option value="UM" <?php if($summary->ship_to_country == 'UM') echo "selected"; ?>>United States Minor Outlying Islands</option>
										<option value="UY" <?php if($summary->ship_to_country == 'UY') echo "selected"; ?>>Uruguay</option>
										<option value="UZ" <?php if($summary->ship_to_country == 'UZ') echo "selected"; ?>>Uzbekistan</option>
										<option value="VU" <?php if($summary->ship_to_country == 'VU') echo "selected"; ?>>Vanuatu</option>
										<option value="VE" <?php if($summary->ship_to_country == 'VE') echo "selected"; ?>>Venezuela, Bolivarian Republic of</option>
										<option value="VN" <?php if($summary->ship_to_country == 'VN') echo "selected"; ?>>Viet Nam</option>
										<option value="VG" <?php if($summary->ship_to_country == 'VG') echo "selected"; ?>>Virgin Islands, British</option>
										<option value="VI" <?php if($summary->ship_to_country == 'VI') echo "selected"; ?>>Virgin Islands, U.S.</option>
										<option value="WF" <?php if($summary->ship_to_country == 'WF') echo "selected"; ?>>Wallis and Futuna</option>
										<option value="EH" <?php if($summary->ship_to_country == 'EH') echo "selected"; ?>>Western Sahara</option>
										<option value="YE" <?php if($summary->ship_to_country == 'YE') echo "selected"; ?>>Yemen</option>
										<option value="ZM" <?php if($summary->ship_to_country == 'ZM') echo "selected"; ?>>Zambia</option>
										<option value="ZW" <?php if($summary->ship_to_country == 'ZW') echo "selected"; ?>>Zimbabwe</option>
									</select>
								</td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Ship To Phone">Ship To Phone<span class="text-danger">*</span></td>
								<td scope="row" data-placement="top">
									<input type="text" id="ship_to_phone" value="<?php echo e($summary->ship_to_phone); ?>" class="form-control" />
								</td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Shiping Request Method">Shiping Request Method</td>
								<td scope="row" data-placement="top">
									<input type="text" id="shipping_method" value="<?php echo e(isset($summary->shipping_method) ? $summary->shipping_method : ''); ?>" class="form-control" />
								</td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Delivery Notes">Delivery Notes</td>
								<td scope="row" data-placement="top">
									<input type="text" id="delivery_notes" value="<?php echo e($summary->delivery_notes); ?>" class="form-control" />
								</td>
							</tr>
							<tr>
								<td scope="row" data-placement="top" title="Customer Paid Shipping">Customer Paid Shipping</td>
								<td scope="row" data-placement="top">
									<input type="text" id="customer_shipping_price" value="<?php echo e($summary->customer_shipping_price); ?>" class="form-control" />
								</td>
							</tr>													
							<tr>
								<td>
									<?php if(!in_array($summary->order_status,[17,18,20,21,22])): ?>
										<a class="btn btn-primary text-white float-right" href="javascript:void(0)" onClick="updateShippingAndCustomerDetails(2)">Update Shipping Details</a>
									<?php endif; ?>
								</td>
								<td>
								<?php if(in_array($summary->order_status,[1,2,19,23]) && in_array(auth()->user()->role, [2,3,6]) ): ?>
									<a class="btn btn-danger text-white float-right" href="javascript:void(0)" onClick="cancelOrder(<?php echo e($summary->id); ?>)">Cancel Order</a>
								<?php elseif(auth()->user()->role == 1 &&  !in_array($summary->order_status,[17,20])): ?>
									<a class="btn btn-danger text-white float-right" href="javascript:void(0)" onClick="cancelOrder(<?php echo e($summary->id); ?>)">Cancel Order</a>
								<?php endif; ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>								
			</div>			
		</div>		
	</div>	
</div>

<div class="row">
	<div class="col-md-12">
		<div class="card o-hidden mb-4">
			<div class="card-header">
				<div class="row">
					<div class="col-md-4">
						<h3 class="w-50 float-left card-title m-0"><?php echo e($row->etailer_order_number); ?></h3>
					</div>
					<div class="col-md-2">

					</div>
					<div class="col-md-2">
					</div>
					<div class="col-md-2">						
					</div>					
					<div class="col-md-2">	
						<?php if(in_array($summary->order_status,[1,2,19,23,10,11,12])): ?>					
							<a class="btn btn-primary text-white float-right" href="javascript:void(0)" onClick="GetModelOrderItem('<?php echo e(route('orders.add_product',$row->etailer_order_number)); ?>')">Add Product</a>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="card-body">
				<?php if($result): ?>						
					<?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<div class="col-md-12 mt-4">
							<ul class="nav nav-tabs nav-justified">
								<li class="nav-item">
									<a class="nav-link active" href="#tab_sub_order_<?php echo e(str_replace(".", '_', $key)); ?>" id="sub_order_<?php echo e(str_replace(".", '_', $key)); ?>_tab" role="tab" aria-controls="sub_order_<?php echo e(str_replace(".", '_', $key)); ?>_tab" area-selected="true" data-toggle="tab">Sub Orders Details</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="#tab_ship_details_<?php echo e(str_replace(".", '_', $key)); ?>" id="ship_details_<?php echo e(str_replace(".", '_', $key)); ?>_tab" role="tab" aria-controls="ship_details_<?php echo e(str_replace(".", '_', $key)); ?>_tab" area-selected="false" data-toggle="tab">Shipment Details</a>
								</li>								
								<li class="nav-item">
									<a class="nav-link" href="#tab_comments_<?php echo e(str_replace(".", '_', $key)); ?>" id="comments_<?php echo e(str_replace(".", '_', $key)); ?>_tab" role="tab" aria-controls="comments_<?php echo e(str_replace(".", '_', $key)); ?>_tab" area-selected="false" data-toggle="tab">Comments</a>
								</li>								
							</ul>
							<div class="tab-content">
								<div class="tab-pane fade show active" id="tab_sub_order_<?php echo e(str_replace(".", '_', $key)); ?>" role="tabpanel" area-labelledby="sub_order_<?php echo e(str_replace(".", '_', $key)); ?>_tab">
									<form id="sub_order_<?php echo e(str_replace(".", '_', $key)); ?>" method="post" action="<?php echo e(route('orders.update_qty')); ?>" onsubmit="return validateForm('sub_order_<?php echo e(str_replace('.', '_', $key)); ?>');">
										<?php echo csrf_field(); ?>
										<table class="table table-stripped">
											<thead>
												<tr>
													<th colspan="12" class="text-center" style="background:<?php echo e(rand_color()); ?>;">
														Sub order: #<?php echo e(isset($key) && $key != '' ? $key : 'NA'); ?>

													</th>
												</tr>
											</thead>
											<?php if($row): ?>
												<thead>
													<tr>
														<?php if(count($row) > 0): ?>
															<th>
																
															</th>
														<?php endif; ?>
														<th>ETIN</th>
														<th>Product Listing Name </th>
														<th>UPC </th>
														<th>GTIN </th>
														<th>Warehouse</th>
														<th>Quantity Ordered</th>
														<th>Quantity Fulfilled</th>
														<th>Carrier Type</th>
														<th>Shipment Type</th>														
														<th>Status</th>
														<th>Action</th>
													</tr>
												</thead>
												<?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row_sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<tr>												
														<?php if(count($row) > 0 && $row_sub->status != 'Client Fulfilled'): ?>
															<td>
																<input type="checkbox" name="cb_<?php echo e($key); ?>" value="<?php echo e($row_sub->id); ?>">
															</td>
														<?php else: ?>
															<td></td>								
														<?php endif; ?>
														<td><?php echo e($row_sub->ETIN); ?></td>
														<td><?php echo e(isset($row_sub->product) ? $row_sub->product->product_listing_name : ''); ?></td>
														<td><?php echo e(isset($row_sub->product) ? $row_sub->product->upc : ''); ?></td>
														<td><?php echo e(isset($row_sub->product) ? $row_sub->product->gtin: ''); ?></td>
														<?php if($row_sub->status == 'Client Fulfilled'): ?>
															<td><?php echo e($row_sub->warehouse); ?></td>
														<?php else: ?>
															<td>
																<select id="wh[<?php echo e($row_sub['id']); ?>]" class="form-control select2" name="wh[<?php echo e($row_sub['id']); ?>]">
																	<option value="">Select</option>
																	<?php if($whs): ?>
																		<?php $__currentLoopData = $whs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																			<option value="<?php echo e($wh->warehouses); ?>" <?php if($row_sub->warehouse === $wh->warehouses) echo 'selected'; ?>><?php echo e($wh->warehouses); ?></option>
																		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																	<?php endif; ?>
																</select>
															</td>
														<?php endif; ?>														
														<td>
															<input id="order_item[<?php echo e($row_sub['id']); ?>]" type="number" name="order_item[<?php echo e($row_sub['id']); ?>]" value="<?php echo e($row_sub['quantity_ordered']); ?>" <?php if(in_array($summary->order_status,[1,19])): ?> readonly <?php endif; ?>>
														</td>
														<td><?php echo e($row_sub->quantity_fulfilled); ?></td>
														<td><?php echo e($row_sub->carrier_name); ?></td>
														<td><?php echo e($row_sub->service_name); ?></td>
														<td><?php echo e($row_sub->status); ?></td>
														<td>
															<?php if($row_sub->status != 'Client Fulfilled'): ?>
																<?php if(isset($row_sub->product) && str_contains(strtolower($row_sub->product->item_form_description), 'kit')): ?>
																	<a class="btn btn-primary text-white" href="javascript:void(0)" onClick="showKitItems('<?php echo e($row_sub->ETIN); ?>')" id="show_ki_btn">Show Kit Items</a>
																<?php endif; ?>

																<?php if(!in_array($row_sub->status, ['Picked', 'Packed', 'Shipped', 'Reship Picked', 'Reship Packed', 'Reship Shipped'])): ?>
																	<a href="<?php echo e(route('orders.delete_sub_order_items',$row_sub['id'])); ?>"	 class="btn btn-danger" onClick="return confirm('are you sure you want to delete this item?')">Delete</a>
																<?php endif; ?>
															<?php endif; ?>															
														</td>
													</tr>
														<?php if(isset($row_sub->product) && str_contains(strtolower($row_sub->product->item_form_description), 'kit')): ?>
															<tr name="kp_<?php echo e($row_sub->ETIN); ?>" style="display: none;">
																<th colspan="10" class="text-center" style="background:<?php echo e(rand_color()); ?>;">
																	KIT Components for ETIN: #<?php echo e($row_sub->ETIN); ?>

																</th>
															</tr>
															<tr name="kp_<?php echo e($row_sub->ETIN); ?>" style="display: none;">
																<th></th>
																<th colspan="2">Component ETIN</th>
																<th colspan="4">Description</th>
																<th colspan="3">Quantity</th>
															</tr>
															<?php if(isset($kit_items[$row_sub->ETIN])): ?>
																<?php $__currentLoopData = $kit_items[$row_sub->ETIN]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<tr name="kp_<?php echo e($row_sub->ETIN); ?>" style="display: none;">
																		<td></td>
																		<td colspan="2"><?php echo e($item->components_ETIN); ?></td>
																		<td colspan="4"><?php echo e($item->component_product_details->product_listing_name); ?></td>
																		<td colspan="3"><?php echo e($item->qty); ?></td>
																	</tr>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
															<?php endif; ?>													
														<?php endif; ?>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
										</table>
										<?php if(isset($key) && $key != '' && $row_sub->status != 'Client Fulfilled'): ?>
											<?php if(isset($sub_order_ship_type[$key]['status']) && isset($sub_order_ship_type[$key]['id']) && !in_array($sub_order_ship_type[$key]['status'],[6,13])): ?>
												<?php if(in_array($summary->order_status,[1,2,19,23])): ?>
													<button type="button" class="btn btn-primary" style="margin-right: 10px;" onClick="GetModelOrderItem('<?php echo e(route('orders.update_sub_order_status',[$sub_order_ship_type[$key]['id'],$sub_order_ship_type[$key]['status']])); ?>')">Change Status</button>						
													<button type="button" class="btn btn-primary" style="margin-right: 10px;" onClick="GetModelOrderItem('<?php echo e(route('orders.update_sub_order_wh',[$key])); ?>')">Change Warehouse</button>
												<?php endif; ?>
												<button type="button" class="btn btn-primary"  style="margin-right: 10px;" onClick="GetModelOrderItem('<?php echo e(route('orders.add_product',$row_sub->order_number)); ?>','reship','<?php echo e($key); ?>')">Add Product</button>						
											<?php endif; ?>
												<?php if(in_array($summary->order_status,[1,2,19,23])): ?>
													<button type="submit" class="btn btn-primary float-right">Submit</button>
												<?php endif; ?>
											<?php if(count($row) > 1): ?>
												<?php if(in_array($summary->order_status,[1,2,19,23])): ?>
													<button type="button" class="btn btn-primary float-right" style="margin-right: 10px;" onclick="splitOrders(<?php echo e($key); ?>)">Split Orders</button>						
												<?php endif; ?>
											<?php endif; ?>											
											<?php if($sub_order_ship_type[$key]['status'] == 7 || ($summary->order_status == 23 && $sub_order_ship_type[$key]['status'] != 6)): ?>
												<button type="button" class="btn btn-primary float-right" style="margin-right: 10px;" onclick="shipOrders(<?php echo e($key); ?>)">Pickup Shipment</button>
											<?php endif; ?>
											<?php if(isset($sub_order_ship_type[$key]['status']) && $sub_order_ship_type[$key]['status'] == '6'): ?>
												<button type="button" class="btn btn-primary float-right" style="margin-right: 10px;" onclick="showReshipOptions('<?php echo e($key); ?>')">Re-Ship</button>
											<?php endif; ?>
										<?php endif; ?>
										<?php if(in_array($summary->order_status,[10,11,12,])): ?>
													<button type="submit" class="btn btn-primary float-right">Submit</button>
										<?php endif; ?>										
									</form>
								</div>
								<div class="tab-pane fade" id="tab_ship_details_<?php echo e(str_replace(".", '_', $key)); ?>" role="tabpanel" area-labelledby="ship_details_<?php echo e(str_replace(".", '_', $key)); ?>_tab">
									<form>
										<?php echo csrf_field(); ?>
										<table class="table ">
											<thead>
												<tr>
													<th colspan="7" class="text-center" style="background:<?php echo e(rand_color()); ?>;">
														Shipping Details: #<?php echo e(isset($key) && $key != '' ? $key : 'NA'); ?>

													</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td scope="row" data-placement="top" title="Ship To Name">Status</td>
													<?php if(isset($sub_order_ship_type[$key]['status_name'])): ?>
														<td scope="row" data-placement="top"><?php echo e($sub_order_ship_type[$key]['status_name']); ?></td>
													<?php else: ?>
														<td scope="row" data-placement="top">NA</td>
													<?php endif; ?>													
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Carrier Type">Carrier Type</td>
													<td scope="row" data-placement="top">
														<select class="select2 col-md-6" id="carrier_type[<?php echo e($key); ?>]" onchange="changeShipmentTypeInSubOrder(this, <?php echo e($key); ?>)">
															<option value="-1">Select</option>
															<?php if(isset($sub_order_ship_type[$key]['carrier_id'])): ?>
																<?php if($carr): ?>
																	<?php $__currentLoopData = $carr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																		<option value="<?php echo e($car->company_name); ?>" <?php if($sub_order_ship_type[$key]['carrier_id'] === $car->id) echo 'selected'; ?> ><?php echo e($car->company_name); ?></option>
																	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																<?php endif; ?>
															<?php else: ?>
																<?php if($carr): ?>
																	<?php $__currentLoopData = $carr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																		<option value="<?php echo e($car->company_name); ?>" <?php if($summary->carrier_id === $car->id) echo 'selected'; ?> ><?php echo e($car->company_name); ?></option>
																	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																<?php endif; ?>
															<?php endif; ?>																							
														</select>
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Shipment Type">Shipment Type</td>
													<td scope="row" data-placement="top">
														<select class="select2 col-md-6" id="shipment_type[<?php echo e($key); ?>]">
															<option value="">Select</option>	
															<?php if(isset($sub_order_ship_type[$key]['carrier_name'])): ?>
																<?php if(strtolower($sub_order_ship_type[$key]['carrier_name']) === 'ups'): ?>
																	<?php if($ups_st): ?>
																		<?php $__currentLoopData = $ups_st; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ups): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																			<option value="<?php echo e($ups->id); ?>" <?php if ($sub_order_ship_type[$key]['service_type_id'] == $ups->id) echo 'selected'; ?>><?php echo e($ups->service_name); ?></option>
																		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
																	<?php endif; ?>										
																<?php elseif(strtolower($sub_order_ship_type[$key]['carrier_name']) === 'fedex'): ?>
																	<?php if($fedex_st): ?>
																		<?php $__currentLoopData = $fedex_st; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fedex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																			<option value="<?php echo e($fedex->id); ?>" <?php if ($sub_order_ship_type[$key]['service_type_id'] == $fedex->id) echo 'selected'; ?>><?php echo e($fedex->service_name); ?></option>
																		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
																	<?php endif; ?>
																<?php endif; ?>
															<?php else: ?>														
																<?php if(strtolower($summary->carrier_name) === 'ups'): ?>
																	<?php if($ups_st): ?>
																		<?php $__currentLoopData = $ups_st; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ups): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																			<option value="<?php echo e($ups->id); ?>" <?php if ($summary->shipment_type == $ups->id) echo 'selected'; ?>><?php echo e($ups->service_name); ?></option>
																		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																	<?php endif; ?>											
																<?php elseif(strtolower($summary->carrier_name) === 'fedex'): ?>
																	<?php if($fedex_st): ?>
																		<?php $__currentLoopData = $fedex_st; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fedex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																			<option value="<?php echo e($fedex->id); ?>" <?php if ($summary->shipment_type == $fedex->id) echo 'selected'; ?>><?php echo e($fedex->service_name); ?></option>
																		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																	<?php endif; ?>
																<?php endif; ?>															
															<?php endif; ?>
														</select>
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To Name">Ship To Name</td>
													<td scope="row" data-placement="top">
														<?php if(isset($sub_order_ship_type[$key]['ship_to_name'])): ?>
															<input type="text" id="ship_to_name[<?php echo e($key); ?>]" value="<?php echo e($sub_order_ship_type[$key]['ship_to_name']); ?>" class="form-control" />
														<?php else: ?>
															<input type="text" id="ship_to_name[<?php echo e($key); ?>]" value="<?php echo e($summary->ship_to_name); ?>" class="form-control" />
														<?php endif; ?>
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To Address Type">Ship To Address Type</td>
													<td scope="row" data-placement="top">
														<?php if(isset($sub_order_ship_type[$key]['ship_to_address_type'])): ?>
															<input type="text" id="ship_to_address_type[<?php echo e($key); ?>]" value="<?php echo e($sub_order_ship_type[$key]['ship_to_address_type']); ?>" class="form-control" />
														<?php else: ?>
															<input type="text" id="ship_to_address_type[<?php echo e($key); ?>]" value="<?php echo e($summary->ship_to_address_type); ?>" class="form-control" />
														<?php endif; ?>
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To Address1">Ship To Address1</td>
													<td scope="row" data-placement="top">
														<?php if(isset($sub_order_ship_type[$key]['ship_to_address1'])): ?>
															<input type="text" id="ship_to_address1[<?php echo e($key); ?>]" value="<?php echo e($sub_order_ship_type[$key]['ship_to_address1']); ?>" class="form-control" />
														<?php else: ?>
															<input type="text" id="ship_to_address1[<?php echo e($key); ?>]" value="<?php echo e($summary->ship_to_address1); ?>" class="form-control" />
														<?php endif; ?>
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To Address2">Ship To Address2</td>
													<td scope="row" data-placement="top">
														<?php if(isset($sub_order_ship_type[$key]['ship_to_address2'])): ?>
															<input type="text" id="ship_to_address2[<?php echo e($key); ?>]" value="<?php echo e($sub_order_ship_type[$key]['ship_to_address2']); ?>" class="form-control" />
														<?php else: ?>
															<input type="text" id="ship_to_address2[<?php echo e($key); ?>]" value="<?php echo e($summary->ship_to_address2); ?>" class="form-control" />
														<?php endif; ?>
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To Address3">Ship To Address3</td>
													<td scope="row" data-placement="top">
														<?php if(isset($sub_order_ship_type[$key]['ship_to_address3'])): ?>
															<input type="text" id="ship_to_address3[<?php echo e($key); ?>]" value="<?php echo e($sub_order_ship_type[$key]['ship_to_address3']); ?>" class="form-control" />
														<?php else: ?>
															<input type="text" id="ship_to_address3[<?php echo e($key); ?>]" value="<?php echo e($summary->ship_to_address3); ?>" class="form-control" />
														<?php endif; ?>
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To City">Ship To City</td>
													<td scope="row" data-placement="top">
														<?php if(isset($sub_order_ship_type[$key]['ship_to_city'])): ?>
															<input type="text" id="ship_to_city[<?php echo e($key); ?>]" value="<?php echo e($sub_order_ship_type[$key]['ship_to_city']); ?>" class="form-control" />
														<?php else: ?>
															<input type="text" id="ship_to_city[<?php echo e($key); ?>]" value="<?php echo e($summary->ship_to_city); ?>" class="form-control" />
														<?php endif; ?>
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To State">Ship To State</td>
													<td scope="row" data-placement="top">
														<?php if(isset($sub_order_ship_type[$key]['ship_to_state'])): ?>
															<input type="text" id="ship_to_state[<?php echo e($key); ?>]" value="<?php echo e($sub_order_ship_type[$key]['ship_to_state']); ?>" class="form-control" />
														<?php else: ?>
															<input type="text" id="ship_to_state[<?php echo e($key); ?>]" value="<?php echo e($summary->ship_to_state); ?>" class="form-control" />
														<?php endif; ?>
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To Zip">Ship To Zip</td>
													<td scope="row" data-placement="top">
														<?php if(isset($sub_order_ship_type[$key]['ship_to_zip'])): ?>
															<input type="text" id="ship_to_zip[<?php echo e($key); ?>]" value="<?php echo e($sub_order_ship_type[$key]['ship_to_zip']); ?>" class="form-control" />
														<?php else: ?>
															<input type="text" id="ship_to_zip[<?php echo e($key); ?>]" value="<?php echo e($summary->ship_to_zip); ?>" class="form-control" />
														<?php endif; ?>
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To Counter">Ship To Country</td>
													<td scope="row" data-placement="top">
														<?php if(isset($sub_order_ship_type[$key]['ship_to_country'])): ?>
															<input type="text" id="ship_to_country[<?php echo e($key); ?>]" value="<?php echo e($sub_order_ship_country[$key]['ship_to_phone']); ?>" class="form-control" />
														<?php else: ?>
															<input type="text" id="ship_to_country[<?php echo e($key); ?>]" value="<?php echo e($summary->ship_to_country); ?>" class="form-control" />
														<?php endif; ?>
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Ship To Phone">Ship To Phone</td>
													<td scope="row" data-placement="top">
														<?php if(isset($sub_order_ship_type[$key]['ship_to_phone'])): ?>
															<input type="text" id="ship_to_phone[<?php echo e($key); ?>]" value="<?php echo e($sub_order_ship_type[$key]['ship_to_phone']); ?>" class="form-control" />
														<?php else: ?>
															<input type="text" id="ship_to_phone[<?php echo e($key); ?>]" value="<?php echo e($summary->ship_to_phone); ?>" class="form-control" />
														<?php endif; ?>
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Shiping Request Method">Shiping Request Method</td>
													<td scope="row" data-placement="top">
														<?php if(isset($sub_order_ship_type[$key]['shipping_method'])): ?>
															<input type="text" id="shipping_method[<?php echo e($key); ?>]" value="<?php echo e($sub_order_ship_type[$key]['shipping_method']); ?>" class="form-control" />
														<?php else: ?>
															<input type="text" id="shipping_method[<?php echo e($key); ?>]" value="<?php echo e($summary->shipping_method); ?>" class="form-control" />
														<?php endif; ?>
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Delivery Notes">Delivery Notes</td>
													<td scope="row" data-placement="top">
														<?php if(isset($sub_order_ship_type[$key]['delivery_notes'])): ?>
															<input type="text" id="delivery_notes[<?php echo e($key); ?>]" value="<?php echo e($sub_order_ship_type[$key]['delivery_notes']); ?>" class="form-control" />
														<?php else: ?>
															<input type="text" id="delivery_notes[<?php echo e($key); ?>]" value="<?php echo e($summary->delivery_notes); ?>" class="form-control" />
														<?php endif; ?>
													</td>
												</tr>
												<tr>
													<td scope="row" data-placement="top" title="Customer Paid Shipping">Customer Paid Shipping</td>
													<td scope="row" data-placement="top">
														<?php if(isset($sub_order_ship_type[$key]['customer_shipping_price'])): ?>
															<input type="text" id="customer_shipping_price[<?php echo e($key); ?>]" value="<?php echo e($sub_order_ship_type[$key]['customer_shipping_price']); ?>" class="form-control" />
														<?php else: ?>
															<input type="text" id="customer_shipping_price[<?php echo e($key); ?>]" value="<?php echo e($summary->customer_shipping_price); ?>" class="form-control" />
														<?php endif; ?>
													</td>
												</tr>																						
											</tbody>
										</table>
										<?php if(isset($key) && $key != ''): ?>
											<button type="button" class="btn btn-primary float-right" onclick="updateSubOrderShipDetails(<?php echo e($key); ?>)">Submit</button>
										<?php endif; ?>										
									</form>
								</div>
								<div class="tab-pane fade" id="tab_comments_<?php echo e(str_replace(".", '_', $key)); ?>" role="tabpanel" area-labelledby="comments_tab_<?php echo e(str_replace(".", '_', $key)); ?>">									
									<?php echo $__env->make('cranium.chat.chat',['type_id' => $summary->etailer_order_number, 'type' => 'order' ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>			
								</div>
							</div>						
						</div>						
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<?php endif; ?>					
			</div>	
		</div>
	</div>
	<!-- end of col-->
</div>
<div class="modal fade" id="MyModalOrderItm" data-backdrop="static">
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
<script>

	$(document).ready(function () {
		toggleNonPickupWarehouse();
		checkSaturdayEligibility();
	});

	function checkSaturdayEligibility() {
		var day = new Date().getDay();
		if (day != 4 && day != 5) {
			$('#sat_elli').attr("disabled", 'disabled');
		}
	}

	function validateForm(formId) {
		let formSelect = document.querySelectorAll('#' + formId + ' select');
		for (var fS of formSelect) {
			if(fS.options[fS.selectedIndex].value === '') {				
				toastr.error("Please select a valid warehouse.")
				return false;
			}
		}
		return true;
	}

	function GetModelOrderItem(url,type=null,$re_sub_order=null){
		$.ajax({
			url:url,
			method:'GET',
			data:{'type':type,'re_sub_order':$re_sub_order},
			success:function(res){
				$("#MyModalOrderItm").html(res);
				$("#MyModalOrderItm").modal();
			}
		});
	}

	function updateShippingAndCustomerDetails($type) {

		var customerName = document.getElementById('customer_name').value
		var customerEmail = document.getElementById('customer_email').value
		var customerNumber = document.getElementById('customer_number').value

		var shipName = document.getElementById('ship_to_name').value
		var shipAddressType = document.getElementById('ship_to_address_type').value
		var shipAddress1 = document.getElementById('ship_to_address1').value
		var shipAddress2 = document.getElementById('ship_to_address2').value
		var shipAddress3 = document.getElementById('ship_to_address3').value
		var shipCity = document.getElementById('ship_to_city').value
		var shipState = document.getElementById('ship_to_state').value
		var shipZip = document.getElementById('ship_to_zip').value
		var shipCountry = document.getElementById('ship_to_country').value
		var shipPhone = document.getElementById('ship_to_phone').value
		var shipMethod = document.getElementById('shipping_method').value
		var deliveryNotes = document.getElementById('delivery_notes').value
		var customerPrice = document.getElementById('customer_shipping_price').value

		var selectElem = document.getElementById('sum_shipment_type');
		var shipmentType = selectElem.options[selectElem.selectedIndex].value;

		selectElem = document.getElementById('sum_carrier');
		var carrierType = selectElem.options[selectElem.selectedIndex].value;


		var form = new FormData();
		form.append('sum_id', "<?php echo e($summary->id); ?>")
		form.append('type', $type)

		if ($type === 1) {
			form.append('customer_name', customerName);
			form.append('customer_email', customerEmail);
			form.append('customer_number', customerNumber);
		} else {		
			form.append('ship_to_name', shipName);
			form.append('ship_to_address_type', shipAddressType);
			form.append('ship_to_address1', shipAddress1);
			form.append('ship_to_address2', shipAddress2);
			form.append('ship_to_address3', shipAddress3);
			form.append('ship_to_city', shipCity);
			form.append('ship_to_state', shipState);
			form.append('ship_to_zip', shipZip);
			form.append('ship_to_phone', shipPhone);
			form.append('shipping_method', shipMethod);
			form.append('delivery_notes', deliveryNotes);
			form.append('customer_shipping_price', customerPrice);
			form.append('shipment_type', shipmentType);
			form.append('carrier_type', carrierType);
			form.append('ship_to_country', shipCountry);
		}

		$.ajax({
			url: '<?php echo e(route('orders.change_shipping_customer_details')); ?>',
			method: 'POST',
			data: form,
			processData: false,
			contentType: false,
			success: function(res) {
				if(res.error === false){
                    toastr.success(res.msg);
					setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    toastr.error(res.msg);
                }
			}			
		});
	}

	function updateStatus() {

		var oldStatus = "<?php echo e($summary->order_status); ?>";

		var selectElem = document.getElementById('order_status');
		var newStatus = selectElem.options[selectElem.selectedIndex].value;

		if (oldStatus !== newStatus && newStatus !== '-1' && newStatus !== '') {
			swal({
				title: 'Are you sure you want to change the Order Status?',
				text: ['1', '19'].includes(newStatus) 
					? "Warehouse, Sub Order, Carrier, Transit Days will be reset!" 
					: '',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Confirm'
			}).then((result) => {
                if(result) {
                    performUpdateStatus(oldStatus, newStatus)
                } else {
					return; 
				}
            });
		} else {
			performUpdateStatus(oldStatus, newStatus)
		}
	}

	function performUpdateStatus(oldStatus, newStatus) {

		var invalidShipPreviousStatus = ['3', '4', '5', '6'];					

		var mustShipToday = document.getElementById('must_ship').selectedIndex === 0 ? 1 : 0;
		var holdReleaseDate = document.getElementById('release_date').value;
		var giftMessage = document.getElementById('gift_message').value;

		var selectElem = document.getElementById('order_type_id');
		var typeId = selectElem ? selectElem.options[selectElem.selectedIndex].value : 1;
		
		// if (newStatus !== '-1' && newStatus !== '-2' && holdReleaseDate === '') {
		// 	toastr.error("Please select a release date");
		// 	return;		
		// }		

		if (newStatus === '25' && !invalidShipPreviousStatus.includes(oldStatus)) {
			toastr.error("'Invalid Shipment Type' can only be changed from 'Partially Picked', 'Picked', 'Partially Packed', 'Packed'");
			return;		
		}

		if (oldStatus === '25' && newStatus !== '2') {
			toastr.error("'Invalid Shipment Type' can be only changed to 'Ready To Pick'");
			return;
		}

		selectElem = document.getElementById('wh_np');
		var whNp = selectElem.options[selectElem.selectedIndex].value;

		if (typeId == 3 && !whNp) {
			toastr.error("Please select a Warehouse");
			return;		
		}

		selectElem = document.getElementById('wh_assigned');
		var whAssigned = selectElem ? selectElem.options[selectElem.selectedIndex].value : null;

		selectElem = document.getElementById('sat_elli');
		var satElli = selectElem ? selectElem.options[selectElem.selectedIndex].value : 0;

		var form = new FormData();
		form.append('new_status', newStatus);
		form.append('old_status', oldStatus);
		form.append('must_ship', mustShipToday);
		form.append('hold_release_date', holdReleaseDate);
		form.append('sum_id', "<?php echo e($summary->id); ?>")
		form.append('type_id', typeId)
		form.append('wh_np', whNp)
		form.append('receive_notification', $("#receive_notification").val())
		form.append('po_number', $("#po_number").val())
		form.append('bol_number', $("#bol_number").val())
		if (whAssigned) form.append('wh_assigned', whAssigned)
		form.append('sat_elli', satElli);
		form.append('gift_message', giftMessage);

		$.ajax({
			url: '<?php echo e(route('orders.change_status')); ?>',
			method: 'POST',
			data: form,
			processData: false,
			contentType: false,
			success: function(res) {
				if(res.error === false){
                    toastr.success(res.msg);
					setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    toastr.error(res.msg);
                }
			}			
		});
	}

	function updateSubOrderShipDetails(subOrderNumber) {

		var shipName = document.getElementById('ship_to_name['+subOrderNumber+']').value
		var shipAddressType = document.getElementById('ship_to_address_type['+subOrderNumber+']').value
		var shipAddress1 = document.getElementById('ship_to_address1['+subOrderNumber+']').value
		var shipAddress2 = document.getElementById('ship_to_address2['+subOrderNumber+']').value
		var shipAddress3 = document.getElementById('ship_to_address3['+subOrderNumber+']').value
		var shipCity = document.getElementById('ship_to_city['+subOrderNumber+']').value
		var shipState = document.getElementById('ship_to_state['+subOrderNumber+']').value
		var shipZip = document.getElementById('ship_to_zip['+subOrderNumber+']').value
		var shipCountry = document.getElementById('ship_to_country['+subOrderNumber+']').value
		var shipPhone = document.getElementById('ship_to_phone['+subOrderNumber+']').value
		var shipMethod = document.getElementById('shipping_method['+subOrderNumber+']').value
		var deliveryNotes = document.getElementById('delivery_notes['+subOrderNumber+']').value
		var customerPrice = document.getElementById('customer_shipping_price['+subOrderNumber+']').value

		var selectElem = document.getElementById('shipment_type['+subOrderNumber+']');
		var shipmentType = selectElem.options[selectElem.selectedIndex].value;

		selectElem = document.getElementById('carrier_type['+subOrderNumber+']');
		var carrierType = selectElem.options[selectElem.selectedIndex].value;

		var form = new FormData();
		form.append('sub_order_id', subOrderNumber)	
		form.append('ship_to_name', shipName);
		form.append('ship_to_address_type', shipAddressType);
		form.append('ship_to_address1', shipAddress1);
		form.append('ship_to_address2', shipAddress2);
		form.append('ship_to_address3', shipAddress3);
		form.append('ship_to_city', shipCity);
		form.append('ship_to_state', shipState);
		form.append('ship_to_zip', shipZip);
		form.append('ship_to_phone', shipPhone);
		form.append('shipping_method', shipMethod);
		form.append('delivery_notes', deliveryNotes);
		form.append('customer_shipping_price', customerPrice);
		form.append('shipment_type', shipmentType);
		form.append('carrier_type', carrierType);

		$.ajax({
			url: '<?php echo e(route('orders.update_sub_order_ship_details')); ?>',
			method: 'POST',
			data: form,
			processData: false,
			contentType: false,
			success: function(res) {
				if(res.error === false){
					toastr.success(res.msg);
					setTimeout(() => {
                        location.reload();
                    }, 2000);
				} else {
					toastr.error(res.msg);
				}
			}			
		});
	}

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
		form.append('order_number', '<?php echo e($summary->etailer_order_number); ?>');
		form.append('ids', ids);

		$.ajax({
			url: '<?php echo e(route('orders.split_order')); ?>',
			method: 'POST',
			data: form,
			processData: false,
			contentType: false,
			success: function(res) {
				if(res.error === false){
					toastr.success(res.msg);
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
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
		form.append('order_number', '<?php echo e($summary->etailer_order_number); ?>');
		form.append('ids', ids);

		$.ajax({
			url: '<?php echo e(route('orders.merge_order')); ?>',
			method: 'POST',
			data: form,
			processData: false,
			contentType: false,
			success: function(res) {
				if(res.error === false) {
					$('#MyModalOrderItm').modal('hide');
					toastr.success(res.msg);
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
				} else {
					toastr.error(res.msg);
				}
			}			
		});
	}

	function changeShipmentTypeInSummary(type) {			
		var toAppend = <?php echo json_encode($etailer_services, 15, 512) ?>;

		if (type.value.toLowerCase() === 'non-person pickup') {
			toAppend = <?php echo json_encode($non_pickup_st, 15, 512) ?>;
		} else if (type.value.toLowerCase() !== 'ups' && type.value.toLowerCase() !== 'fedex') {			
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
			if (type.value.toLowerCase() === 'non-person pickup') {
				opt.value = toAppend[key].id; 
				opt.innerHTML = toAppend[key].service_name; 
			} else {
				opt.value = type.value.toLowerCase() === 'ups' 
					? toAppend[key].ups_shipping_service_type.id 
					: toAppend[key].fdx_shipping_service_type.id;  
				opt.innerHTML = type.value.toLowerCase() === 'ups' 
					? toAppend[key].ups_shipping_service_type.service_name 
					: toAppend[key].fdx_shipping_service_type.service_name; 
			}
			select_elem.append(opt); 
		}
	}

	function changeShipmentTypeInSubOrder(type, key) {
		var toAppend = 'Hello'
		if (type.value.toLowerCase() === 'fedex') {
			toAppend = <?php echo json_encode($fedex_st, 15, 512) ?>;
		} else if (type.value.toLowerCase() === 'ups') {
			toAppend = <?php echo json_encode($ups_st, 15, 512) ?>;
		} else if (type.value.toLowerCase() === 'non-person pickup') {
			toAppend = <?php echo json_encode($non_pickup_st, 15, 512) ?>;
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
		form.append('order_number', '<?php echo e($summary->etailer_order_number); ?>');
		form.append('prev_carrier_id', 
			'<?php echo e(isset($key) && isset($sub_order_ship_type[$key]['carrier_id']) ? $sub_order_ship_type[$key]['carrier_id'] : 0); ?>');
		form.append('prev_carrier_name', 
			'<?php echo e(isset($key) && isset($sub_order_ship_type[$key]['carrier_name']) ? $sub_order_ship_type[$key]['carrier_name'] : 0); ?>');
		form.append('prev_ship_type', 
			'<?php echo e(isset($key) && isset($sub_order_ship_type[$key]['service_type_id']) ? $sub_order_ship_type[$key]['service_type_id'] : 0); ?>');

		$.ajax({
			url: '<?php echo e(route('orders.reship_order_page')); ?>',
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

	function shipOrders(subOrder) {
		var form = new FormData();
		form.append('sub_order', subOrder);
		form.append('order', '<?php echo e($summary->etailer_order_number); ?>');
		$.ajax({
			url: '<?php echo e(route('orders.ship_manual')); ?>',
			method: 'POST',
			data: form,
			processData: false,
			contentType: false,
			success: function(res) {
				if(res.error === 0) {
					toastr.success(res.msg);
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
				} else {
					toastr.error(res.msg);
				}
			}			
		});
	}

	function toggleNonPickupWarehouse(orderType) {
		if (orderType) {
			if (orderType.value == 3) {
				$('.otype_tr').removeAttr("style");
			} else {
				$('.otype_tr').attr('style', 'display:none');
			}
		} else {
			var selectElem = document.getElementById('order_type_id');
			var value = selectElem ? selectElem.options[selectElem.selectedIndex].value : -1;
			if (value == 3) {
				$('.otype_tr').removeAttr("style");
			}
		}
	}

	function cancelOrder(order_id){
		var form = new FormData();
		form.append('order_id', order_id);

		$.ajax({
			url: '<?php echo e(route('orders.cancel_order')); ?>',
			method: 'POST',
			data: form,
			processData: false,
			contentType: false,
			success: function(res) {
				if(res.error === 0) {
					toastr.success(res.msg);
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
				} else {
					toastr.error(res.msg);
				}
			}			
		});
	}

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\cranium_new\resources\views/orders/view.blade.php ENDPATH**/ ?>