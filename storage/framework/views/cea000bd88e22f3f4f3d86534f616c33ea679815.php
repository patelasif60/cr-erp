
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
		<li>Create New Manual Order</li>
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
										<input type="text" value="New Manual" readonly class="form-control">
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="e-tailer Order Number">e-tailer Order Number</td>
									<td scope="row" data-placement="top"><input type="text" readonly class="form-control"></td>
								</tr>
								<!-- <tr>
									<td scope="row" data-placement="top" title="Channel Order Number">Channel Order Number</td>
									<td scope="row" data-placement="top"><input type="text" class="form-control" name="channel_order_number" id="channel_order_number"></td>
								</tr> -->
								<tr>
									<td scope="row" data-placement="top" title="Order Source">Order Source</td>
									<td scope="row" data-placement="top"><input type="text" name="order_source" id="order_source" value="Manual" readonly class="form-control"></td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Purchase Date">Purchase Date</td>
									<td scope="row" data-placement="top"><input type="date" name="purchase_date" id="purchase_date"  class="form-control" value="<?php echo e(date("Y-m-d")); ?>"></td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Channel Ship Date">Channel Ship Date</td>
									<td scope="row" data-placement="top"><input type="date" name="channel_estimated_ship_date" id="channel_estimated_ship_date"  class="form-control" disabled></td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Order Total Price">Order Total Price</td>
									<td scope="row" data-placement="top"><input type="number" name="order_total_price" id="order_total_price"  class="form-control" readonly></td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Must Ship Today">Must Ship Today</td>
									<td scope="row" data-placement="top">
										<select id="must_ship_today" name="must_ship_today" class="form-control select2">
											<option value='0'>No</option>											
											<option value='1'>Yes</option>
										</select>
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Client">Client<span class="text-danger">*</span></td>
									<td scope="row" data-placement="top" style="<?php if($client_id != ''): ?> pointer-events:none  <?php endif; ?>">
										<select id="client_id" name="client_id" class="form-control select2">
											<option value="">Select Client</option>
											<?php if($client): ?>
												<?php $__currentLoopData = $client; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row_client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value='<?php echo e($row_client->id); ?>' <?php if($client_id != '' && $client_id == $row_client->id): ?> selected='selected' <?php endif; ?>><?php echo e($row_client->company_name); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
										</select>
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Hold Release Date">Hold Release Date</td>
									<td scope="row" data-placement="top">
										<input type="date" id="release_date" name="release_date"  class="form-control"/>
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Order Type">Order Type<span class="text-danger">*</span></td>
									<td scope="row" data-placement="top">
										<select id="order_type_id" name="order_type_id" class="form-control select2" onchange="changeOrderType(this)">
											<?php if($ots): ?>
												<?php $__currentLoopData = $ots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value='<?php echo e($ot->id); ?>' <?php if (strtolower($ot->name) === 'manual') echo 'selected' ?>><?php echo e($ot->name); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
										</select>
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Hold Release Date">Gift Message</td>
									<td scope="row" data-placement="top">
										<textarea id="gift_message" name="gift_message" class="form-control" rows="3"></textarea>
									</td>
								</tr>
								<tr>
									<td scope="row">Saturday Eligible</td>
									<td scope="row" data-placement="top">
										<select id="sat_elli" class="form-control select2" name="sat_elli">
											<option value='1'>Yes</option>
											<option value='0' selected>No</option>
										</select>							
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
										<input type="text" id="customer_name" name="customer_name"  class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="E-Mail">E-Mail</td>
									<td scope="row" data-placement="top">
										<input type="text" id="customer_email" name="customer_email"  class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Phone">Phone</td>
									<td scope="row" data-placement="top">
										<input type="text" id="customer_number" name="customer_number"  class="form-control" />
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
									<td scope="row" data-placement="top">
										<select class="form-control select2 col-md-6" id="sum_carrier" name="sum_carrier" onchange="changeShipmentTypeInSummary(this)">
											<option value="">Select</option>
											<?php $__currentLoopData = $carr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($car->company_name); ?>"><?php echo e($car->company_name); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>										
										</select>
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Shipment Type">Shipment Type</td>
									<td scope="row" data-placement="top">
										<select class="form-control select2 col-md-6" id="sum_shipment_type" name="sum_shipment_type">
											<option value="">Select</option>											
										</select>
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To Name">Ship To Name<span class="text-danger">*</span></td>
									<td scope="row" data-placement="top">
										<input type="text" id="ship_to_name" name="ship_to_name"  class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To Address Type">Ship To Address Type</td>
									<td scope="row" data-placement="top">
										<select id="ship_to_address_type" name="ship_to_address_type" class="form-control">
											<option value="">Select</option>
											<option value="Residential">Residential</option>
											<option value="Business">Business</option>
										</select>
										
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To Address1">Ship To Address1<span class="text-danger">*</span></td>
									<td scope="row" data-placement="top">
										<input type="text" id="ship_to_address1" name="ship_to_address1" class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To Address2">Ship To Address2</td>
									<td scope="row" data-placement="top">
										<input type="text" id="ship_to_address2" name="ship_to_address2" class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To Address3">Ship To Address3</td>
									<td scope="row" data-placement="top">
										<input type="text" id="ship_to_address3" name="ship_to_address3" class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To City">Ship To City<span class="text-danger">*</span></td>
									<td scope="row" data-placement="top">
										<input type="text" id="ship_to_city" name="ship_to_city"  class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To State">Ship To State<span class="text-danger">*</span></td>
									<td scope="row" data-placement="top">
										<input type="text" id="ship_to_state" name="ship_to_state"  class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To Zip">Ship To Zip<span class="text-danger">*</span></td>
									<td scope="row" data-placement="top">
										<input type="text" id="ship_to_zip" name="ship_to_zip"  class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To Counter">Ship To Country<span class="text-danger">*</span></td>
									<td scope="row" data-placement="top">
										<select id="ship_to_country" name="ship_to_country" class="form-control">
											<option value="AF">Afghanistan</option>
											<option value="AX">Åland Islands</option>
											<option value="AL">Albania</option>
											<option value="DZ">Algeria</option>
											<option value="AS">American Samoa</option>
											<option value="AD">Andorra</option>
											<option value="AO">Angola</option>
											<option value="AI">Anguilla</option>
											<option value="AQ">Antarctica</option>
											<option value="AG">Antigua and Barbuda</option>
											<option value="AR">Argentina</option>
											<option value="AM">Armenia</option>
											<option value="AW">Aruba</option>
											<option value="AU">Australia</option>
											<option value="AT">Austria</option>
											<option value="AZ">Azerbaijan</option>
											<option value="BS">Bahamas</option>
											<option value="BH">Bahrain</option>
											<option value="BD">Bangladesh</option>
											<option value="BB">Barbados</option>
											<option value="BY">Belarus</option>
											<option value="BE">Belgium</option>
											<option value="BZ">Belize</option>
											<option value="BJ">Benin</option>
											<option value="BM">Bermuda</option>
											<option value="BT">Bhutan</option>
											<option value="BO">Bolivia, Plurinational State of</option>
											<option value="BQ">Bonaire, Sint Eustatius and Saba</option>
											<option value="BA">Bosnia and Herzegovina</option>
											<option value="BW">Botswana</option>
											<option value="BV">Bouvet Island</option>
											<option value="BR">Brazil</option>
											<option value="IO">British Indian Ocean Territory</option>
											<option value="BN">Brunei Darussalam</option>
											<option value="BG">Bulgaria</option>
											<option value="BF">Burkina Faso</option>
											<option value="BI">Burundi</option>
											<option value="KH">Cambodia</option>
											<option value="CM">Cameroon</option>
											<option value="CA">Canada</option>
											<option value="CV">Cape Verde</option>
											<option value="KY">Cayman Islands</option>
											<option value="CF">Central African Republic</option>
											<option value="TD">Chad</option>
											<option value="CL">Chile</option>
											<option value="CN">China</option>
											<option value="CX">Christmas Island</option>
											<option value="CC">Cocos (Keeling) Islands</option>
											<option value="CO">Colombia</option>
											<option value="KM">Comoros</option>
											<option value="CG">Congo</option>
											<option value="CD">Congo, the Democratic Republic of the</option>
											<option value="CK">Cook Islands</option>
											<option value="CR">Costa Rica</option>
											<option value="CI">Côte d'Ivoire</option>
											<option value="HR">Croatia</option>
											<option value="CU">Cuba</option>
											<option value="CW">Curaçao</option>
											<option value="CY">Cyprus</option>
											<option value="CZ">Czech Republic</option>
											<option value="DK">Denmark</option>
											<option value="DJ">Djibouti</option>
											<option value="DM">Dominica</option>
											<option value="DO">Dominican Republic</option>
											<option value="EC">Ecuador</option>
											<option value="EG">Egypt</option>
											<option value="SV">El Salvador</option>
											<option value="GQ">Equatorial Guinea</option>
											<option value="ER">Eritrea</option>
											<option value="EE">Estonia</option>
											<option value="ET">Ethiopia</option>
											<option value="FK">Falkland Islands (Malvinas)</option>
											<option value="FO">Faroe Islands</option>
											<option value="FJ">Fiji</option>
											<option value="FI">Finland</option>
											<option value="FR">France</option>
											<option value="GF">French Guiana</option>
											<option value="PF">French Polynesia</option>
											<option value="TF">French Southern Territories</option>
											<option value="GA">Gabon</option>
											<option value="GM">Gambia</option>
											<option value="GE">Georgia</option>
											<option value="DE">Germany</option>
											<option value="GH">Ghana</option>
											<option value="GI">Gibraltar</option>
											<option value="GR">Greece</option>
											<option value="GL">Greenland</option>
											<option value="GD">Grenada</option>
											<option value="GP">Guadeloupe</option>
											<option value="GU">Guam</option>
											<option value="GT">Guatemala</option>
											<option value="GG">Guernsey</option>
											<option value="GN">Guinea</option>
											<option value="GW">Guinea-Bissau</option>
											<option value="GY">Guyana</option>
											<option value="HT">Haiti</option>
											<option value="HM">Heard Island and McDonald Islands</option>
											<option value="VA">Holy See (Vatican City State)</option>
											<option value="HN">Honduras</option>
											<option value="HK">Hong Kong</option>
											<option value="HU">Hungary</option>
											<option value="IS">Iceland</option>
											<option value="IN">India</option>
											<option value="ID">Indonesia</option>
											<option value="IR">Iran, Islamic Republic of</option>
											<option value="IQ">Iraq</option>
											<option value="IE">Ireland</option>
											<option value="IM">Isle of Man</option>
											<option value="IL">Israel</option>
											<option value="IT">Italy</option>
											<option value="JM">Jamaica</option>
											<option value="JP">Japan</option>
											<option value="JE">Jersey</option>
											<option value="JO">Jordan</option>
											<option value="KZ">Kazakhstan</option>
											<option value="KE">Kenya</option>
											<option value="KI">Kiribati</option>
											<option value="KP">Korea, Democratic People's Republic of</option>
											<option value="KR">Korea, Republic of</option>
											<option value="KW">Kuwait</option>
											<option value="KG">Kyrgyzstan</option>
											<option value="LA">Lao People's Democratic Republic</option>
											<option value="LV">Latvia</option>
											<option value="LB">Lebanon</option>
											<option value="LS">Lesotho</option>
											<option value="LR">Liberia</option>
											<option value="LY">Libya</option>
											<option value="LI">Liechtenstein</option>
											<option value="LT">Lithuania</option>
											<option value="LU">Luxembourg</option>
											<option value="MO">Macao</option>
											<option value="MK">Macedonia, the former Yugoslav Republic of</option>
											<option value="MG">Madagascar</option>
											<option value="MW">Malawi</option>
											<option value="MY">Malaysia</option>
											<option value="MV">Maldives</option>
											<option value="ML">Mali</option>
											<option value="MT">Malta</option>
											<option value="MH">Marshall Islands</option>
											<option value="MQ">Martinique</option>
											<option value="MR">Mauritania</option>
											<option value="MU">Mauritius</option>
											<option value="YT">Mayotte</option>
											<option value="MX">Mexico</option>
											<option value="FM">Micronesia, Federated States of</option>
											<option value="MD">Moldova, Republic of</option>
											<option value="MC">Monaco</option>
											<option value="MN">Mongolia</option>
											<option value="ME">Montenegro</option>
											<option value="MS">Montserrat</option>
											<option value="MA">Morocco</option>
											<option value="MZ">Mozambique</option>
											<option value="MM">Myanmar</option>
											<option value="NA">Namibia</option>
											<option value="NR">Nauru</option>
											<option value="NP">Nepal</option>
											<option value="NL">Netherlands</option>
											<option value="NC">New Caledonia</option>
											<option value="NZ">New Zealand</option>
											<option value="NI">Nicaragua</option>
											<option value="NE">Niger</option>
											<option value="NG">Nigeria</option>
											<option value="NU">Niue</option>
											<option value="NF">Norfolk Island</option>
											<option value="MP">Northern Mariana Islands</option>
											<option value="NO">Norway</option>
											<option value="OM">Oman</option>
											<option value="PK">Pakistan</option>
											<option value="PW">Palau</option>
											<option value="PS">Palestinian Territory, Occupied</option>
											<option value="PA">Panama</option>
											<option value="PG">Papua New Guinea</option>
											<option value="PY">Paraguay</option>
											<option value="PE">Peru</option>
											<option value="PH">Philippines</option>
											<option value="PN">Pitcairn</option>
											<option value="PL">Poland</option>
											<option value="PT">Portugal</option>
											<option value="PR">Puerto Rico</option>
											<option value="QA">Qatar</option>
											<option value="RE">Réunion</option>
											<option value="RO">Romania</option>
											<option value="RU">Russian Federation</option>
											<option value="RW">Rwanda</option>
											<option value="BL">Saint Barthélemy</option>
											<option value="SH">Saint Helena, Ascension and Tristan da Cunha</option>
											<option value="KN">Saint Kitts and Nevis</option>
											<option value="LC">Saint Lucia</option>
											<option value="MF">Saint Martin (French part)</option>
											<option value="PM">Saint Pierre and Miquelon</option>
											<option value="VC">Saint Vincent and the Grenadines</option>
											<option value="WS">Samoa</option>
											<option value="SM">San Marino</option>
											<option value="ST">Sao Tome and Principe</option>
											<option value="SA">Saudi Arabia</option>
											<option value="SN">Senegal</option>
											<option value="RS">Serbia</option>
											<option value="SC">Seychelles</option>
											<option value="SL">Sierra Leone</option>
											<option value="SG">Singapore</option>
											<option value="SX">Sint Maarten (Dutch part)</option>
											<option value="SK">Slovakia</option>
											<option value="SI">Slovenia</option>
											<option value="SB">Solomon Islands</option>
											<option value="SO">Somalia</option>
											<option value="ZA">South Africa</option>
											<option value="GS">South Georgia and the South Sandwich Islands</option>
											<option value="SS">South Sudan</option>
											<option value="ES">Spain</option>
											<option value="LK">Sri Lanka</option>
											<option value="SD">Sudan</option>
											<option value="SR">Suriname</option>
											<option value="SJ">Svalbard and Jan Mayen</option>
											<option value="SZ">Swaziland</option>
											<option value="SE">Sweden</option>
											<option value="CH">Switzerland</option>
											<option value="SY">Syrian Arab Republic</option>
											<option value="TW">Taiwan, Province of China</option>
											<option value="TJ">Tajikistan</option>
											<option value="TZ">Tanzania, United Republic of</option>
											<option value="TH">Thailand</option>
											<option value="TL">Timor-Leste</option>
											<option value="TG">Togo</option>
											<option value="TK">Tokelau</option>
											<option value="TO">Tonga</option>
											<option value="TT">Trinidad and Tobago</option>
											<option value="TN">Tunisia</option>
											<option value="TR">Turkey</option>
											<option value="TM">Turkmenistan</option>
											<option value="TC">Turks and Caicos Islands</option>
											<option value="TV">Tuvalu</option>
											<option value="UG">Uganda</option>
											<option value="UA">Ukraine</option>
											<option value="AE">United Arab Emirates</option>
											<option value="GB">United Kingdom</option>
											<option value="US" selected>United States</option>
											<option value="UM">United States Minor Outlying Islands</option>
											<option value="UY">Uruguay</option>
											<option value="UZ">Uzbekistan</option>
											<option value="VU">Vanuatu</option>
											<option value="VE">Venezuela, Bolivarian Republic of</option>
											<option value="VN">Viet Nam</option>
											<option value="VG">Virgin Islands, British</option>
											<option value="VI">Virgin Islands, U.S.</option>
											<option value="WF">Wallis and Futuna</option>
											<option value="EH">Western Sahara</option>
											<option value="YE">Yemen</option>
											<option value="ZM">Zambia</option>
											<option value="ZW">Zimbabwe</option>
										</select>
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Ship To Phone">Ship To Phone<span class="text-danger">*</span></td>
									<td scope="row" data-placement="top">
										<input type="text" id="ship_to_phone" name="ship_to_phone"  class="form-control" />
									</td>
								</tr>
								<!-- <tr>
									<td scope="row" data-placement="top" title="Shiping Request Method">Shiping Request Method</td>
									<td scope="row" data-placement="top">
										<input type="text" id="shipping_method" name="shipping_method"  class="form-control" />
									</td>
								</tr> -->
								<tr>
									<td scope="row" data-placement="top" title="Delivery Notes">Delivery Notes</td>
									<td scope="row" data-placement="top">
										<input type="text" id="delivery_notes" name="delivery_notes"  class="form-control" />
									</td>
								</tr>
								<tr>
									<td scope="row" data-placement="top" title="Customer Paid Shipping">Customer Paid Shipping</td>
									<td scope="row" data-placement="top">
										<select class="select2 col-md-6" id="customer_shipping_price" name="customer_shipping_price">
											<option value="" >Select</option>
											<option value="Yes" >Yes</option>
											<option value="No" >No</option>
										</select>
									</td>
								</tr>							
							</tbody>
						</table>
					</div>								
				</div>	
				<div class="row md-2">
					<div class="col-12 p-5">
						<button type="submit" class="btn btn-primary">Save</button>
						<a class="btn btn-danger" href="<?php echo e(route('orders.index')); ?>">Cancel</a>
					</div>
				</div>
			</form>		
		</div>		
	</div>	
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
<script src="<?php echo e(asset('assets/js/validation/jquery.validate.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/validation/additional-methods.min.js')); ?>"></script>
<script>
	var carrier = '';
	var shipment_type = '';
	$("#save_manual_order").validate({
        submitHandler(form){
			
			var day = new Date().getDay();
			var satEl = $('#sat_elli').val();

			if (satEl == 1 && day != 4 && day != 5) {
				toastr.error("Saturday eligible orders can be created only on Thursday and Friday");
				return;
			}

            $(".submit").attr("disabled", true);
            $('div#preloader').show();
            var form_cust = $('#save_manual_order')[0];
            let form1 = new FormData(form_cust);
            $.ajax({
                type: "POST",
                url: '<?php echo e(route('orders.store',$client_id)); ?>',
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

	function changeOrderType(order_type){
		
		if(order_type.value == 3){
			carrier = $("#sum_carrier").html();
			shipment_type = $("#sum_shipment_type").html();

			var select_elem = document.getElementById('sum_carrier');
			var options = select_elem.getElementsByTagName('option');
			for (var i = options.length; i--;) {	
				select_elem.removeChild(options[i]);
			}
			
			let opt = document.createElement("option");
			opt.value = 'Non-person Pickup'; 
			opt.innerHTML = 'Non-person Pickup'; 
			select_elem.append(opt);

			var select_elem = document.getElementById('sum_shipment_type');
			var options = select_elem.getElementsByTagName('option');
			for (var i = options.length; i--;) {	
				select_elem.removeChild(options[i]);
			}
			
			let opt2 = document.createElement("option");
			opt2.value = 'Non-person Pickup'; 
			opt2.innerHTML = 'Non-person Pickup'; 
			select_elem.append(opt2);

		}else{
			$("#sum_shipment_type").html(shipment_type);
			$("#sum_carrier").html(carrier)
		}

	}

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\cranium_new\resources\views/orders/create.blade.php ENDPATH**/ ?>