<link rel="stylesheet" href="{{asset('assets/styles/vendor/dropzone.min.css')}}">
{{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> --}}
<link rel="stylesheet" href="{{asset('assets/styles/css/select2/new/select2.min.css')}}">
<style>
  .tab_W {
    display: none;
  }  
  form .form-group {
      margin-right: -5px;
      display: inline-block;
  }
  h2.header_for_wizard {
      padding: 10px;
      text-align: center;
  }
  .card-title {
      text-align: center;
  }
  button#nextBtn, button#saveBtn {
      margin-right: 20px;
  }
  .ui-helper-hidden-accessible {
      display: none;
  }
  .step {
    height: 15px;
    width: 15px;
    margin: 0 2px;
    background-color: #bbbbbb;
    border: none;
    border-radius: 50%;
    display: inline-block;
    opacity: 0.5;
  }
  
  /* Mark the active step: */
  .step.active {
    opacity: 1;
  }
  
  /* Mark the steps that are finished and valid: */
  .step.finish {
    background-color: #04AA6D;
  }
  </style>
<!-- Modal -->
<div class="modal-dialog" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="h3" id="exampleModalLabel">Parent Product Wizard</h5>
      <button type="button" class="close reset-text" data-dismiss="modal" style="color:white;">&times;</button>
    </div>
    <div class="modal-body">
        <div class="box-body">
          <!-- model -->
          <form id="regForm" class="regFormClass"  method="POST" action="#" >
            @csrf
            @method('put')
            <input type="hidden" name="queue_status" id="queue_status" value="d">
          
            <div class="tab_W">
              <div class="card-header bg-transparent">		
                <h3 class="card-title"> Step 1: Core Product Information</h3>		
              </div>
              <div class="row">
                <div class="col-lg-12 col-lg-offset-4">
                    <div class="form-group col-lg-12">
                      <label for="ETIN" class="" data-toggle="tooltip" data-placement="top" title="e-tailer internal SKU">ETIN</label>
                      <input type="text" class="form-control" id="ETIN" placeholder="ETIN" name ="ETIN" value='{{$newetin}}' readonly>
                    </div>
                    <div class="form-group col-lg-12">
                      <label for="inputEmail4" class="" data-toggle="tooltip" data-placement="top" title="The product name displayed on stores and marketplaces">
                        Product Listing Name<small>(Auto Generated)</small> <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" id="product_listing_name" name="product_listing_name" placeholder="Product Listing Name" readonly />
                    </div>
                    <div class="form-group col-lg-12">
                      <label for="brand" class="" data-toggle="tooltip" data-placement="top" title="Brand name">Brand <span class="text-danger">*</span></label>
                      <select  id="brand" name="brand" class="form-control select2"  >
                        <option value='' selected> -- Select a value  --</option>
                        @if ($brand)
                          @foreach($brand as $row_brand)
                            <option value="{{ $row_brand }}">{{ $row_brand }}</option>
                          @endforeach
                        @endif
                      </select>
                      <!-- <a href="#" data-toggle="modal" data-target="#new_brand_request">New Request</a> -->
                    </div>
                    <div class="form-group col-lg-12">
                      <label for="inputEmail4" class="" data-toggle="tooltip" data-placement="top" title="The product's flavor or variety description">Flavor Variation</label>
                      <input type="text" class="form-control" id="flavor" name="flavor" placeholder="Flavor Variation">
                    </div>
                    <div class="form-group  col-lg-12">
                      <label for="inputEmail4" class="" data-toggle="tooltip" data-placement="top" title="Base product name, i.e. Ice Cream, Energy Drink, Potato Chips, etc.">Product Type <span class="text-danger">*</span></label>
                      <select id="product_type" name="product_type" class="form-control select2" >
                        <option value='' selected> -- Select a value  -- </option>
                        @if ($producttype)
                          @foreach($producttype as $producttypelist)
                            <option value="{{ $producttypelist }}">{{ $producttypelist }}</option>
                          @endforeach
                        @endif
                      </select>
                      <!-- <a href="#" data-toggle="modal" data-target="#new_product_type_request">New Request</a> -->
                    </div>
                    <div class="form-group col-lg-12">
                      <label for="inputEmail4" class="" data-toggle="tooltip" data-placement="top" title="Count of packs per case">Pack Form Count <span class="text-danger">*</span></label>
                      <input type="number" class="form-control" id="pack_form_count" name="pack_form_count" placeholder="Pack Form Count" min="1" >
                    </div>
                    <div class="form-group col-lg-12">
                      <label for="unit_in_pack" class="" data-toggle="tooltip" data-placement="top" title="Count of units per pack">Units in Pack <span class="text-danger">*</span></label>
                      <input type="number" class="form-control" id="unit_in_pack" name="unit_in_pack" placeholder="Units in Pack" min="1">
                    </div>
                    <div class="form-group col-lg-12">
                      <label for="unit_description" class="" data-toggle="tooltip" data-placement="top" title="Unit type, i.e. Cup, Can, Bottle, Box">Unit Description <span class="text-danger">*</span></label>
                      <select id="unit_description" name="unit_description" class="form-control select2" id="unit_description" >
                          <option value='' selected> -- Select a value  -- </option>
                          @if ($unitdesc)
                            @foreach($unitdesc as $unitdesclist)
                              <option value="{{ $unitdesclist }}">{{ $unitdesclist }}</option>
                            @endforeach
                          @endif
                        </select>
                      <!-- <a href="#" data-toggle="modal" data-target="#new_unit_description_request">New Request</a> -->
                    </div>
                    <div class="form-group col-md-12">
                      <label for="unit_list" class="" data-toggle="tooltip" data-placement="top" title="Individual unit size, i.e. lb. oz.">Unit Size <span class="text-danger">*</span></label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="number" id="unit_num" name="unit_num" class="form-control" placeholder="Select Unit Count"  min="0.0001" step="0.0001">
                        </div>
                        <div class="col-md-6">
                          <select id="unit_list" name="unit_list" class="form-control" id="unit_list" >
                            <option value='' selected> -- Select unit -- </option>
                            @if ($unitsize)
                              @foreach($unitsize as $unitabbr => $unitname)
                                <option value="{{ $unitabbr }}">{{ $unitname }}</option>
                              @endforeach
                            @endif
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="form-group col-md-12">
                      <label for="inputEmail4" class="" data-toggle="tooltip" data-placement="top" title="i.e. Case, Pack, Each, Kit">Item Form Description <span class="text-danger">*</span></label>
                      <select id="item_form_description" name="item_form_description" class="form-control select2" id="item_form_description" >
                          <option value=''> -- Select a value  -- </option>
                        @if ($itemsdesc)
                          @foreach($itemsdesc as $key=>$value)
                            @if($value != 'Kit')
                              <option value="{{ $value }}">{{ $value }}</option>
                            @endif
                          @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="form-group col-lg-12">
                      <label for="product_temperature" class="" data-toggle="tooltip" data-placement="top" title="Product temperature category, i.e. Dry-Fragile, Frozen, Refrigerated, Dry-Strong">Product Temperature <span class="text-danger">*</span></label>
                      <select id="product_temperature" name="product_temperature" class="form-control select2" id="product_temperature" >
                          <option value='' selected> -- Select a value  -- </option>
                        @if ($producttemp)
                          @foreach($producttemp as $producttemplist)
                            <option value="{{ $producttemplist }}">{{ $producttemplist }}</option>
                          @endforeach
                        @endif
                      </select>
                    </div>
                  </div>
                </div>
            </div>

            <div class="tab_W">
              <div class="card-header bg-transparent">		
                <h3 class="card-title"> Step 2: Supplier & Manufacturer Details</h3>		
              </div>
              <div class="row">	
                <div class="col-lg-12 col-lg-offset-4">
                  <div class="table-responsive  col-md-12">
                    <table class="table ">
                      <tbody>
                        <tr>
                          <th scope="row" data-toggle="tooltip" data-placement="top" title="Supplier Type">
                            Supplier Type <span class="text-danger">*</span>
                          </th>
                          <td>
                            <input type="radio" id="type_supplier" name="sup_type" value="type_supplier" onchange="changeSuppliers('supplier')" checked> Supplier
                            <input type="radio" id="type_client" name="sup_type" value="type_client" onchange="changeSuppliers('client')"> Client
                          </td>
                        </tr>
                        <tr>
                          <th scope="row" data-toggle="tooltip" data-placement="top" title="Current Supplier Name">
                            Current Supplier <span class="text-danger">*</span>
                          </th>
                          <td>
                            <select id="current_supplier" name="current_supplier" class="form-control select2" >
                                <option value=''> -- Select a value  -- </option>
                              @if ($supplier)
                                @foreach($supplier as $key=>$value)
                                  <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                              @endif
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <th scope="row" data-toggle="tooltip" data-placement="top" title="Current Supplier's Status/Availability, i.e. Backorder, Special Order">
                            Supplier Status
                          </th>
                          <td>
                            @if(count($supplier_status) == 0)
                            <input type="text" class="form-control" id="supplier_status" name="supplier_status" placeholder="Supplier Status">
                            @else
                            <select class="form-control select2" id="supplier_status" name="supplier_status" placeholder="Supplier Status">
                              <option value=''> -- Select a value  -- </option>
                              @if ($supplier_status)
                                @foreach($supplier_status as $row)
                                <option value="{{ $row->id }}">{{ $row->supplier_status }}</option>
                                @endforeach
                              @endif
                            </select>
                            @endif
                          </td>
                        </tr>
                        {{-- <tr>
                          <th scope="row">Alternate Supplier(s)</th>
                          <td>N.A.</td>
                        </tr> --}}
                        <tr>
                          <th scope="row" data-toggle="tooltip" data-placement="top" title="Notates the supplier's availability for dropship">
                            Dropship Available
                          </th>
                          <td>
                            <select class="form-control" id="dropship_available" name="dropship_available">
                              <option value="">--Select a value--</option>
                              <option value="Yes">Yes</option>
                              <option value="No">No</option>											
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <th scope="row" data-toggle="tooltip" data-placement="top" title="Product cost from supplier: (Case Cost / Case Count) x Pack Form">
                            Cost <span class="text-danger">*</span>
                          </th>
                          <td><input type="number" class="form-control" id="cost" name="cost" placeholder="Cost" min="0.01" step="0.01" pattern="^\d+(?:\.\d{1,2})?$" ></td>
                        </tr>
                        <tr>
                          <th scope="row" data-toggle="tooltip" data-placement="top" title="Weeks Worth Qantity">
                            Weeks Worth Qantity
                          </th>
                          <td><input type="text" class="form-control" id="week_worth_qty" name="week_worth_qty" placeholder="Weeks Worth Qantity"></td>
                        </tr>
                        <tr>
                          <th scope="row" data-toggle="tooltip" data-placement="top" title="Minimum Order Qantity">
                            Minimum Order Qantity
                          </th>
                          <td><input type="text" class="form-control" id="min_order_qty" name="min_order_qty" placeholder="Minimum Order Qantity"></td>
                        </tr>
                        <tr>
                          <td scope="row" data-toggle="tooltip" data-placement="top" title="Lead Time">
                            Lead Time
                          </td>
                          <td><input type="text" class="form-control" id="lead_time" name="lead_time" placeholder="Lead Time"></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="form-group col-md-12">
                    <div class="row">
                      <div class="col-lg-6">
                        <table class="table ">
                          <thead>
                            <tr>
                              <td colspan="2"><h3 class="card-title text-center">Product Codes: Case</h3></td>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <th scope="row" data-toggle="tooltip" data-placement="top" title="12 Digit UPC (Universal Product Code) of the item/unit being sold">
                                UPC 
                              </th>
                              <td>
                                <input type="checkbox" id="upc_present" name="upc_present" onchange="disableEnableTextBox(this, 'upc')" checked=true>
                                <input type="text" class="form-control text-center" id="upc" name="upc" placeholder="UPC" minlength="12" maxlength="12" >
                              </td>
                            </tr>
                            <tr>
                              <th scope="row" data-toggle="tooltip" data-placement="top" title="14 Digit case GTIN (Global Trade Identification Number) of the item/unit being sold">
                                GTIN 
                              </th>
                              <td>
                                <input type="checkbox" id="gtin_present" name="gtin_present" onchange="disableEnableTextBox(this, 'gtin')" checked=true>
                                <input type="text" class="form-control text-center" id="gtin" name="gtin" placeholder="GTIN" minlength="14" maxlength="14" >
                              </td>
                            </tr>
                            <tr>
                              <th scope="row" data-toggle="tooltip" data-placement="top" title="Amazon Standard Identification Number">
                                ASIN
                              </th>
                              <td>
                                <input type="text" class="form-control text-center" id="asin" name="asin" placeholder="ASIN">
                              </td>
                            </tr>
                            <tr>
                              <th scope="row" data-toggle="tooltip" data-placement="top" title="Global Product Classification Code for international shipping etc.">
                                GPC Code
                              </th>
                              <td>
                                <input type="text" class="form-control text-center" id="GPC_code" name="GPC_code" placeholder="GPC Code"> 
                              </td>
                            </tr>
                            <tr>
                              <th scope="row" data-toggle="tooltip" data-placement="top" title="Global Product Classification Category for international shipping etc.">
                                GPC Class
                              </th>
                              <td>
                                <input type="text" class="form-control text-center" id="GPC_class" name="GPC_class" placeholder="GPC Class"> 
                              </td>
                            </tr>
                            <tr>
                              <th scope="row" data-toggle="tooltip" data-placement="top" title="Harmonized System for International shipping">
                                HS Code
                              </th>
                              <td>
                                <input type="text" class="form-control text-center" id="HS_code" name="HS_code" placeholder="HS Code"> 
                              </td>
                            </tr>			
                          </tbody>
                        </table>
                      </div>
                      <div class="col-lg-6">
                        <table class="table ">
                          <thead>
                            <tr>
                              <td colspan="2"><h3 class="card-title text-center">Product Codes: Unit</h3></td>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <th scope="row" data-toggle="tooltip" data-placement="top" title="12 Digit UPC (Universal Product Code) of the item/unit being sold">
                                UPC 
                              </th>
                              <td>
                                <input type="checkbox" id="unit_upc_present" name="unit_upc_present" onchange="disableEnableTextBox(this, 'unit_upc')" checked=true>
                                <input type="text" class="form-control text-center" id="unit_upc" name="unit_upc" placeholder="UPC" minlength="12" maxlength="12" >
                              </td>
                            </tr>
                            <tr>
                              <th scope="row" data-toggle="tooltip" data-placement="top" title="14 Digit case GTIN (Global Trade Identification Number) of the item/unit being sold">
                                GTIN 
                              </th>
                              <td>
                                <input type="checkbox" id="unit_gtin_present" name="unit_gtin_present" onchange="disableEnableTextBox(this, 'unit_gtin')" checked=true>
                                <input type="text" class="form-control text-center" id="unit_gtin" name="unit_gtin" placeholder="GTIN" minlength="14" maxlength="14" >
                              </td>	
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="form-group col-md-12">
                    <div class="row">
                      <div class="col-lg-6">
                        <table class="table ">
                          <thead>
                            <tr>
                              <td colspan="2"><h3 class="card-title text-center">Product Dimensions: Case</h3></td>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <th scope="row" data-toggle="tooltip" data-placement="top" title="Weight  of the item/unit in pounds (lbs.)">
                                Weight (lbs) <span class="text-danger">*</span>
                              </th>
                              <td>
                                <input type="number" class="form-control text-center" id="weight" name="weight" placeholder="Weight (lbs)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" >
                              </td>
                            </tr>
                            <tr>
                              <th scope="row" data-toggle="tooltip" data-placement="top" title="Length of the item/unit in inches (in)">
                                Length (in) <span class="text-danger">*</span>
                              </th>
                              <td>
                                <input type="number" class="form-control text-center" id="length" name="length" placeholder="Length (in)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" >
                              </td>
                            </tr>
                            <tr>
                              <th scope="row" data-toggle="tooltip" data-placement="top" title="Width (depth)of the item/unit in inches (in)">
                                Width (in) <span class="text-danger">*</span>
                              </th>
                              <td>
                                <input type="number" class="form-control text-center" id="width" name="width" placeholder="Width (in)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" > 
                              </td>
                            </tr>
                            <tr>
                              <th scope="row" data-toggle="tooltip" data-placement="top" title="Height of the item/unit in inches (in)">
                                Height (in) <span class="text-danger">*</span>
                              </th>
                              <td>
                                <input type="number" class="form-control text-center" id="height" name="height" placeholder="Height (in)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" > 
                              </td>
                            </tr>			
                          </tbody>
                        </table>
                      </div>
                      <div class="col-lg-6">
                        <table class="table ">
                          <thead>
                            <tr>
                              <td colspan="2"><h3 class="card-title text-center">Product Dimensions: Unit</h3></td>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <th scope="row" data-toggle="tooltip" data-placement="top" title="Weight  of the item/unit in pounds (lbs.)">
                                Weight (lbs) 
                              </th>
                              <td>
                                <input type="number" class="form-control text-center" id="unit_weight" name="unit_weight" placeholder="Weight (lbs)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" >
                              </td>
                            </tr>
                            <tr>
                              <th scope="row" data-toggle="tooltip" data-placement="top" title="Length of the item/unit in inches (in)">
                                Length (in)
                              </th>
                              <td>
                                <input type="number" class="form-control text-center" id="unit_length" name="unit_length" placeholder="Length (in)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" >
                              </td>
                            </tr>
                            <tr>
                              <th scope="row" data-toggle="tooltip" data-placement="top" title="Width (depth)of the item/unit in inches (in)">
                                Width (in) 
                              </th>
                              <td>
                                <input type="number" class="form-control text-center" id="unit_width" name="unit_width" placeholder="Width (in)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" > 
                              </td>
                            </tr>
                            <tr>
                              <th scope="row" data-toggle="tooltip" data-placement="top" title="Height of the item/unit in inches (in)">
                                Height (in) 
                              </th>
                              <td>
                                <input type="number" class="form-control text-center" id="unit_height" name="unit_height" placeholder="Height (in)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" > 
                              </td>
                            </tr>			
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  
                
                  <div class="form-group col-md-12">
                    <label for="inputEmail4" class="" data-toggle="tooltip" data-placement="top" title="List of all ingredients">Ingredients</label>
                    <input type="text" class="form-control" id="ingredients" name="ingredients" placeholder="Ingredients">
                  </div>
                  <div class="form-group col-md-12">
                    <label for="AllergensDropd" class="" data-toggle="tooltip" data-placement="top" title="List of all AllergensDropd">Allergens</label>
                    <select id="AllergensDropd" name="AllergensDropd" class="form-control all" id="AllergensDropd">
                        <option value=''> -- Select a value  -- </option>
                        <option value='Yes'> Yes </option>
                        <option value='No'> No </option>
                    </select>
                  </div>
                  <div class="form-group col-md-12">
                    <div class="row">
                      <div class="form-group col-lg-6" id="AllergensID">
                        <label for="allergens" class="" data-toggle="tooltip" data-placement="top" title="List of known allergens">Allergens Not Assigned</label>
                        <div class="custom_one_line_cards_container AllergensDrop border">
                        @if ($allergens)
                          @foreach($allergens as $key=>$value)
                            <div class="allergens_cards custom_one_line_cards" id="{{ $key }}">{{ $value }}</div>
                          @endforeach
                        @endif
                        </div>
                      </div>
                      <div class="form-group col-lg-6" id="AllergensID2">
                        <input type="hidden" name="allergens" id="allergens">
                        <label for="allergens" class="" data-toggle="tooltip" data-placement="top" title="List of known allergens">Allergens Assigned </label>
                        <div class="custom_one_line_cards_container AllergensDropAssigned border">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group col-md-12">
                    <button class="btn btn-info" type="button" id="btn_more">More +</button>	
                  </div>
                  <div class="form-group col-md-12" id="MFG_shelf_lifeL">
                    <label for="inputEmail4" class="" data-toggle="tooltip" data-placement="top" title="Number of days between manufacturing & expiration">MFG Shelf Life (In days)</label>
                    <input type="number" class="form-control" id="MFG_shelf_life" name="MFG_shelf_life" placeholder="MFG Shelf Life" min="1" max="99999">
                  </div>
                  <div class="form-group col-md-12" id="country_of_originL">
                    <label for="inputEmail4" class="" data-toggle="tooltip" data-placement="top" title="Country where the product is produced, manufactured, or grown">Country of Origin  </label>
                    <select id="country_of_origin" name="country_of_origin" class="form-control select2" id="country_of_origin">
                        <option value=''> -- Select a value  -- </option>
                      @if ($country)
                        @foreach($country as $key=>$value)
                          <option value="{{ $key }}" >{{ $value }}</option>
                        @endforeach
                      @endif
                    </select>
                  </div>
                  <div class="form-group col-md-12" id="storageL">
                    <label for="inputEmail4" class="" data-toggle="tooltip" data-placement="top" title="Storage Description, i.e. The temperature and humidity ranges are designed to protect the quality attributes of the products. Products should be stored at a temperature of 70F +/- 5F, humidity of 50% +/- 10% Relative Humidity">Storage</label>
                    <input type="text" class="form-control"id="storage" name="storage" placeholder="Storage">
                  </div>
                  <div class="form-group col-md-12" id="package_informationL">
                    <label for="inputEmail4" class="" data-toggle="tooltip" data-placement="top" title="Description of the shipped packaging, i.e. Item is shipped inside of a cooler with dry ice and an outer box">Package Information</label>
                    <input type="text" class="form-control" id="package_information" name="package_information" placeholder="Package Information">
                  </div>
                </div>								
              </div>	
            </div>

            <div class="tab_W">
              <div class="card-header bg-transparent">		
                <h3 class="card-title"> Step 3: Description & Categories</h3>		
              </div>
              <div class="row">
                <div class="col-lg-12 col-lg-offset-4">
                  <div class="form-group col-lg-12">
                    <label for="inputEmail4" class="" data-toggle="tooltip" data-placement="top" title="Description of the product to be displayed on stores and marketplaces">Full Product Descrtiption <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_product_desc" name="full_product_desc" placeholder="Full Product Descrtiption" >
                  </div>
                  <div class="form-group col-lg-12" id="about_append">
                    <label for="about_this_item" class="" data-toggle="tooltip" data-placement="top" title="Bullet points highlighting the item in Amazon's 'About this item' section">About This Item <button type="button" class="btn btn-info" id="add_about" onclick="AddRow()">Add More</button> </label>
                    <input type="text" class="form-control  mb-3" id="about_this_item_1" name="about_this_item[]" placeholder="Point 1">
                  </div>
                  <div class="form-group col-lg-12">
                    <label for="inputEmail4" class="" data-toggle="tooltip" data-placement="top" title="Top product category of the hierarchy, i.e. Heat & Serve Meals">Product Category <span class="text-danger">*</span></label>
                    <select id="product_category" name="product_category" class="form-control select2 all_product_category" id="product_category" data-id='0'>
                        <option value=''> -- Select a value  -- </option>
                        @if ($categories)
                          @foreach($categories as $row_cat)
                            <option value="{{ $row_cat->id }}">{{ $row_cat->name }}</option>
                          @endforeach
                        @endif
                    </select>
                  </div>
                  <div id="next_product_container_0">

								  </div>
                 
                  <div class="form-group col-lg-12">
                    <div class="row">
                      <div class="form-group col-md-6">
                        <label for="product_tags"  data-toggle="tooltip" data-placement="top" title="Product tags/groups for filtering and identification, i.e. Gluten-Free, Vegetarian, Low-fat, good for you, Hospitality-resort, Hospitality-Urban, etc.">Product Tags Not Assigned</label>
                        <div class="custom_one_line_cards_container ProductTagsDrop border">
                          @if ($producttag)
                            @foreach($producttag as $key => $value)
                              <div class="product_tags_cards custom_one_line_cards" id="{{ $key }}">{{ $value }}</div>
                            @endforeach
                          @endif
                        </div>
                      </div>
                      <div class="form-group col-md-6">
                        <input type="hidden" name="product_tags" id="product_tags">
                        <label for="lobs" data-toggle="tooltip" data-placement="top" title="Product tags/groups for filtering and identification, i.e. Gluten-Free, Vegetarian, Low-fat, good for you, Hospitality-resort, Hospitality-Urban, etc.">Product Tags Assigned</label>
                        <div class="custom_one_line_cards_container ProductTagsDropAssigned border">
                        </div>
                      </div>
                    </div>
                  </div>
                  <table class="table ">
                    <thead>
                      <tr>
                        <td colspan="2"><h4 class="card-title text-center">Product Flags</h4></td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td scope="row" >
                          <label for="prop_65_flag" class="" data-toggle="tooltip" data-placement="top" title="Indicates the product contains parts, an ingredient, or is manufactured with chemicals known to cause cancer, birth defects or other reproductive harm defined by the State of California (https://oehha.ca.gov/proposition-65/proposition-65-list)">Prop 65 Flag</label>
                        </td>
                        <td>
                          <div class="form-group col-md-12">
                            <select id="prop_65_flag" name="prop_65_flag" class="form-control" id="prop_65_flag">
                                <option value=''> -- Select a value  -- </option>
                                <option value='Yes'> Yes </option>
                                <option value='No'> No </option>
                            </select>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="pro_65_container ban">
                          <div class="form-group">
                            <label for="Prop_65_ingredient"  data-toggle="tooltip" data-placement="top" title="The Prop 65 ingredient(s)/chemical(s)">Prop 65 Ingredient(s)<br> Not Assigned</label>
                            <div class="custom_one_line_cards_container Prop65IngredientDrop border">
                              @if ($prop_ingredients)
                                @foreach($prop_ingredients as $key=>$value)
                                  <div class="prop_65_ingredient_cards custom_one_line_cards" id="{{ $key }}">{{ $value }}</div>
                                @endforeach
                              @endif
                            </div>
                          </div>
                        </td>
                        <td class="pro_65_container ban">
                          <div class="form-group">
                            <input type="hidden" name="prop_65_ingredient" id="Prop_65_ingredient">
                            <label for="Prop_65_ingredient"  data-toggle="tooltip" data-placement="top" title="The Prop 65 ingredient(s)/chemical(s)">Prop 65 Ingredient(s) Assigned</label>
                            <div class="custom_one_line_cards_container Prop65IngredientDropAssigned border">
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td scope="row" >
                          <label for="hazardous_materials" class="" data-toggle="tooltip" data-placement="top" title="Indicates the product is hazardous via Yes/No">Hazardous Materials</label>
                        </td>
                        <td>
                          <select class="form-control" id="hazardous_materials" name="hazardous_materials">
                            <option value=""> -- Select a value -- </option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                          </select>
                        </td>
                      </tr>

                      <tr>
                        <td scope="row" >
                          <label for="consignment" class="" data-toggle="tooltip" data-placement="top" title="Indicates if the item is on consignment">Consignment Product</label>
                        </td>
                        <td>
                          <select class="form-control" id="consignment" name="consignment">
                            <option value="">--Select a value--</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                          </select>
                        </td>
                      </tr>

                      <tr>
                        <td scope="row" >
                        <label for="POG_flag" class="" data-toggle="tooltip" data-placement="top" title="Indicates the product is a planogram item for hospitality clients">POG Flag</label>
                        </td>
                        <td>
                          <select class="form-control" id="POG_flag" name="POG_flag">
                            <option value="">--Select a value--</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                          </select>
                        </td>
                      </tr>
                          
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="tab_W">
              <div class="card-header bg-transparent">		
                <h3 class="card-title"> Step 4: Configuration</h3>		
              </div>
              <div class="row">
                <div class="col-lg-12 col-lg-offset-4">
                  <div class="form-group col-lg-12">
                    <label for="status" class="" data-toggle="tooltip" data-placement="top" title="Current Product Status, i.e. Active, Deplete, Discontinued, Blocked, Pending">Status <span class="text-danger">*</span></label>
                    <select id="status" name="status" class="form-control select2" >
                      <option value=""> -- Select a value  -- </option>
                      <option value="Active">Active</option>
                      <option value="Deplete">Delete</option>
                      <option value="Discontinued">Discontinued</option>
                      <option value="Blocked">Blocked</option>
                      <option value="Pending">Pending</option>
                    </select>
                  </div>
                  <div class="form-group col-lg-12">
                    <label for="etailer_availability" class="" data-toggle="tooltip" data-placement="top" title="Indicates whether the item is stocked in house, special order or dropshipped, i.e. Stocked, Special Order, Dropship">e-tailer Availability <span class="text-danger">*</span></label>
                    <select id="etailer_availability" name="etailer_availability" class="form-control select2" id="etailer_availability" >
                      <option value=''> -- Select a value  -- </option>
                      @if ($etailers)
                        @foreach($etailers as $key=>$value)
                          <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach	
                      @endif
                    </select>
                  </div>
                  <div class="form-group col-lg-12">
                    <label for="warehouses_assigned" class="" data-toggle="tooltip" data-placement="top" title="Warehouse(s) stocking this product">Warehouse(s) Assigned <span class="text-danger">*</span></label>
                    <table class="table table-bordered">
                      <tr>
                        <th></th>
                        <th>Stocked</th>
                      </tr>
                      @if ($warehouse)
                        @foreach($warehouse as $warehouses)
                          <tr>
                            <td>{{ $warehouses }}</td>
                            <td><input type="checkbox" name="warehouses_assigned[]" value="{{ $warehouses }}"></td>
                          </tr>
                        @endforeach
                      @endif
                    </table>
                  </div>
                  <div class="form-group col-lg-12">
                    <div class="row">
                      <div class="form-group col-md-6">
                        <label for="lobs" class="" data-toggle="tooltip" data-placement="top" title="Clients & sites the product is assigned to">Clients & Sites Not Assigned</label>
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
                        <input type="hidden" name="lobs" id="lobs">
                        <label for="lobs" class="" data-toggle="tooltip" data-placement="top" title="Clients & sites the product is assigned to">Clients & Sites Assigned <span class="text-danger">*</span></label>
                        <div class="custom_one_line_cards_container LobsDropAssigned border">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group col-md-12">
                    <label for="inputEmail4" class="" data-toggle="tooltip" data-placement="top" title="*Notes where 3PL products on consignment can be sold
                    *Manufacturer, supplier or misc. restrictions where a product can or cannot be sold. i.e. Blocked from all, blocked from AMZ">Channel Listing Restrictions</label>
                    <input type="text" class="form-control" id="channel_listing_restrictions" name="channel_listing_restrictions" placeholder="Channel Listing Restrictions">
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" id="saveBtn" class="btn btn-primary saveAsDraft">Save as Draft</button>
              <button type="button" id="prevBtn" class="btn btn-info" onclick="nextPrev(-1)">Previous</button>
              <button type="button" id="nextBtn" class="btn btn-info next" onclick="nextPrev(1)">Next</button>
              <button type="submit" class="btn btn-info submit">Save</button> 
              <button type="button" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</button>

            </div>
          </form>
          <!-- model -->
        </div>
    </div>
  </div>
</div>
<!-- Modal -->

<script src="{{asset('assets/js/select2/new/select2.min.js')}}"></script>
<script>
    $('[data-toggle="tooltip"]').tooltip();
    $("button[type = 'submit']").click(function(){
      var $fileUpload = $("input[type='file']");
      if (parseInt($fileUpload.get(0).files.length) > 10){
        alert("You are only allowed to upload a maximum of 10 files");
        return false;
      }
    });

    var currentTab = 0; // Current tab is set to be the first tab (0)
    showTab(currentTab); // Display the current tab

    function showTab(n) {
      // This function will display the specified tab of the form ...
      var x = document.getElementsByClassName("tab_W");
      x[n].style.display = "block";
      // ... and fix the Previous/Next buttons:
      if (n == 0) {
      
        $('#prevBtn').prop('disabled', true);
      } else {
        $('#prevBtn').prop('disabled', false);
      }
      $(".submit").hide();
      if (n == (x.length - 1)) {
        document.getElementById("nextBtn").innerHTML = "Submit";
      $("#nextBtn").hide();
      $(".submit").show();
      } else {
      $("#nextBtn").show();
        document.getElementById("nextBtn").innerHTML = "Next";
      }
      // ... and run a function that displays the correct step indicator:
      fixStepIndicator(n)
    }

    function nextPrev(n) {
      // This function will figure out which tab to display
      var x = document.getElementsByClassName("tab_W");
      // Exit the function if any field in the current tab is invalid:
      //if (n == 1 && !validateForm()) return false;
      // Hide the current tab:
      x[currentTab].style.display = "none";
      // Increase or decrease the current tab by 1:
      currentTab = currentTab + n;
      // if you have reached the end of the form... :
    //   if (currentTab >= x.length) {
        //...the form gets submitted:
        // document.getElementById("regForm").submit();
        // return false;
    //   }
      // Otherwise, display the correct tab:
      showTab(currentTab);
    }

    function validateForm() {
      // This function deals with validation of the form fields
      var x, y, i, valid = true;
      x = document.getElementsByClassName("tab_W");
      y = x[currentTab].getElementsByTagName("input");
      // A loop that checks every input field in the current tab:
      for (i = 0; i < y.length; i++) {
        // If a field is empty...
        if (y[i].value == "") {
          // add an "invalid" class to the field:
          y[i].className += " invalid";
          // and set the current valid status to false:
          valid = false;
        }
      }
      // If the valid status is true, mark the step as finished and valid:
      if (valid) {
        document.getElementsByClassName("step")[currentTab].className += " finish";
      }
      return valid; // return the valid status
    }

    function fixStepIndicator(n) {
      // This function removes the "active" class of all steps...
      var i, x = document.getElementsByClassName("step");
      for (i = 0; i < x.length; i++) {
        x[i].className = x[i].className.replace(" active", "");
      }
      //... and adds the "active" class to the current step:
      x[n].className += " active";
    }
</script>
<script>


//Save as Draft0
$('.saveAsDraft').click(function(e){
	$('input').attr('required', false);
	
	// $("#product_add").submit(function(e) {
		e.preventDefault();
		var form_cust = $('#regForm')[0]; 
		let form1 = new FormData(form_cust);
		// var form = $('#product_add');
		var url = '/saveAsDraft';
		console.log(form1);
		$.ajax({
			type: "POST",
			url: url,
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
					toastr.success(response.msg);
					window.location.href = response.url
				}else{
					$(".submit").attr("disabled", false);
					toastr.error(response.msg);
				}
			},
			error: function(data){
				$(".submit").attr("disabled", false);
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
		});
	// });
});

$('.next').click(function(e){
	$('input').attr('required', false);
	
	// $("#product_add").submit(function(e) {
		e.preventDefault();
		var form_cust = $('#regForm')[0]; 
		let form1 = new FormData(form_cust);
		// var form = $('#product_add');
		var url = '/saveAsDraft';
		console.log(form1);
		$.ajax({
			type: "POST",
			url: url,
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				// if(response.error == 0){
				// 	toastr.success(response.msg);
				// 	// window.location.href = response.url
				// }else{
				// 	$(".submit").attr("disabled", false);
				// 	toastr.error(response.msg);
				// }
			},
			error: function(data){
				$(".submit").attr("disabled", false);
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
		});
	// });
});


// Brand Dropdown
$('#brand').change(function () {
	 var name = $(this).val();
	 var myurl1 = "{{url('getmanufacturer')}}" +"/"+ name;
	 var token = "{{csrf_token()}}";
	 $('#manufacturer').html('');
	 $.ajax({
		url:myurl1,				
		type:'GET',
		dataType:'json',
		data: {
			'_token': token,
			'_method': 'GET',
			'name' : name
			},
		success:function (response) {
			//alert(response);
			var len = 0;
			if (response.data != null) {
				len = response.data.length;
			}

			if (len>0) {
				for (var i = 0; i<len; i++) {
					 var name = response.data[i].manufacturer_name;					 
					 var option = '<option value="'+name+'">'+name+'</option>';
					 if(name != null )
						{
							$("#manufacturer").append(option);
						}
						
				}
			}
		}
	})
});


// Categoty - Subcategory dropdown


$('#prop_65_flag').change(function () {
	var propflag = $(this).val();
	if(propflag === 'Yes'){
		$('.pro_65_container').removeClass('ban');
	} else {
		$('.pro_65_container').addClass('ban');
	}
});


// Auto Populate Product Listing Name
$("#brand").on("input", function() {
	inputproductlisting()
});
$("#flavor").on("input", function() {
	inputproductlisting()
});
$("#product_type").on("input", function() {
	inputproductlisting()
});
$("#unit_num").on("input", function() {
	inputproductlisting()
});
$("#unit_list").on("input", function() {
	inputproductlisting()
});
$("#unit_description").on("input", function() {
	inputproductlisting()
});
$("#pack_form_count").on("input", function() {
	inputproductlisting()
});
$("#unit_in_pack").on("input", function() {
	inputproductlisting()
});
$("#item_form_description").on("change", function() {
	inputproductlisting()
});
function inputproductlisting(){
   var brand = $("#brand").val();
   var flavor = $("#flavor").val();
   var product_type = $("#product_type").val();
   var unit_num = $("#unit_num").val();
   var unit_list = $("#unit_list").val();
   var unit_description = $("#unit_description").val();
   var pack_form_count = $("#pack_form_count").val();
   var unit_in_pack = $("#unit_in_pack").val();
   var item_form_description = $("#item_form_description").val();
   
   
   var productlisting = brand+' '+flavor+' '+product_type+', '+unit_num+' '+unit_list+' ' +unit_description+' ('+pack_form_count+'-'+unit_in_pack+' '+ item_form_description+')';
   $("#product_listing_name").val();
   $("#product_listing_name").val(productlisting);
}

// Auto Populate Total Ounces
$("#unit_num").on("input", function() {
	inputtotalounces()
});
$("#pack_form_count").on("input", function() {
	inputtotalounces()
});
function inputtotalounces(){
	var unit_num = $("#unit_num").val();
	var pack_form_count = $("#pack_form_count").val();
	var totalounces = unit_num * pack_form_count;
	$("#total_ounces").val();
	$("#total_ounces").val(totalounces);
  }


	</script>
<script>
	$(".warehouse_cards").draggable({
		appendTo: "body",
		cursor: "move",
		helper: "clone",
		revert: "invalid",
	});

	$(".warehouseDrop").droppable({
		tolerance: "intersect",
		accept: ".warehouse_cards",
		activeClass: "ui-state-default",
		hoverClass: "ui-state-hover",
		drop: function (event, ui) {
			var dropped_warehouse = ui.draggable.attr('id');
			var warehouses_assigned = $("#warehouses_assigned").val();
			var warehouse_array = [];
			if(warehouses_assigned == ''){
				
			}else{
				warehouse_array = warehouses_assigned.split(',');
				warehouse_array.splice($.inArray(dropped_warehouse, warehouse_array), 1);
				$("#warehouses_assigned").val(warehouse_array.join(','));
			}
			$(this).append($(ui.draggable));
		},
	});

	$(".warehouseDropAssigned").droppable({
		tolerance: "intersect",
		accept: ".warehouse_cards",
		activeClass: "ui-state-default",
		hoverClass: "ui-state-hover",
		drop: function (event, ui) {
			var dropped_warehouse = ui.draggable.attr('id');
			var warehouses_assigned = $("#warehouses_assigned").val();
			var warehouse_array = [];
			if(warehouses_assigned == ''){
				warehouse_array.push(dropped_warehouse);
				$("#warehouses_assigned").val(warehouse_array.join(','));
			}else{
				warehouse_array = warehouses_assigned.split(',');
				warehouse_array.push(dropped_warehouse);
				$("#warehouses_assigned").val(warehouse_array.join(','));
			}
			$(this).append($(ui.draggable));
		},
	});


	$('.LobsDrop ').on('click','.lobs_cards',function(e){
		var dropped_lobs = $(this).attr('id');
		console.log(dropped_lobs);
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
	});
	$('.ProductTagsDrop ').on('click','.product_tags_cards',function(e){
		var dropped_product_tags = $(this).attr('id');
		console.log(dropped_product_tags);
		var product_tags_assigned = $("#product_tags").val();
		var product_tags_array = [];
		if(product_tags_assigned == ''){
			product_tags_array.push(dropped_product_tags);
			$("#product_tags").val(product_tags_array.join(','));
		}else{
			product_tags_array = product_tags_assigned.split(',');
			product_tags_array.push(dropped_product_tags);
			$("#product_tags").val(product_tags_array.join(','));
		}
		
		$('.ProductTagsDropAssigned').append(this);
	});

	$(".ProductTagsDropAssigned").on('click','.product_tags_cards',function(e){
		var dropped_product_tags = $(this).attr('id');
		console.log(dropped_product_tags);
		var product_tags_assigned = $("#product_tags").val();
		var product_tags_array = [];
		if(product_tags_assigned == ''){
			
		}else{
			product_tags_array = product_tags_assigned.split(',');
			product_tags_array.splice($.inArray(dropped_product_tags, product_tags_array), 1);
			$("#product_tags").val(product_tags_array.join(','));
		}
		$('.ProductTagsDrop').append(this);
	});

	$('.Prop65IngredientDrop ').on('click','.prop_65_ingredient_cards',function(e){
		var dropped_Prop_65_ingredient = $(this).attr('id');
		console.log(dropped_Prop_65_ingredient);
		var Prop_65_ingredient_assigned = $("#Prop_65_ingredient").val();
		var Prop_65_ingredient_array = [];
		if(Prop_65_ingredient_assigned == ''){
			Prop_65_ingredient_array.push(dropped_Prop_65_ingredient);
			$("#Prop_65_ingredient").val(Prop_65_ingredient_array.join(','));
		}else{
			Prop_65_ingredient_array = Prop_65_ingredient_assigned.split(',');
			Prop_65_ingredient_array.push(dropped_Prop_65_ingredient);
			$("#Prop_65_ingredient").val(Prop_65_ingredient_array.join(','));
		}
		
		$('.Prop65IngredientDropAssigned').append(this);
	});

	$(".Prop65IngredientDropAssigned").on('click','.prop_65_ingredient_cards',function(e){
		var dropped_Prop_65_ingredient = $(this).attr('id');
		console.log(dropped_Prop_65_ingredient);
		var Prop_65_ingredient_assigned = $("#Prop_65_ingredient").val();
		var Prop_65_ingredient_array = [];
		if(Prop_65_ingredient_assigned == ''){
			
		}else{
			Prop_65_ingredient_array = Prop_65_ingredient_assigned.split(',');
			Prop_65_ingredient_array.splice($.inArray(dropped_Prop_65_ingredient, Prop_65_ingredient_array), 1);
			$("#Prop_65_ingredient").val(Prop_65_ingredient_array.join(','));
		}
		$('.Prop65IngredientDrop').append(this);
	});

	$('.AllergensDrop ').on('click','.allergens_cards',function(e){
		var dropped_allergens = $(this).attr('id');
		console.log(dropped_allergens);
		var allergens_assigned = $("#allergens").val();
		var allergens_array = [];
		if(allergens_assigned == ''){
			allergens_array.push(dropped_allergens);
			$("#allergens").val(allergens_array.join(','));
		}else{
			allergens_array = allergens_assigned.split(',');
			allergens_array.push(dropped_allergens);
			$("#allergens").val(allergens_array.join(','));
		}
		
		$('.AllergensDropAssigned').append(this);
	});

	$(".AllergensDropAssigned").on('click','.allergens_cards',function(e){
		var dropped_allergens = $(this).attr('id');
		console.log(dropped_allergens);
		var allergens_assigned = $("#allergens").val();
		var allergens_array = [];
		if(allergens_assigned == ''){
			
		}else{
			allergens_array = allergens_assigned.split(',');
			allergens_array.splice($.inArray(dropped_allergens, allergens_array), 1);
			$("#allergens").val(allergens_array.join(','));
		}
		$('.AllergensDrop').append(this);
	});
</script>

<script>
  $("#regForm").validate({
    submitHandler(form){
      $(".submit").attr("disabled", true);
      var form_cust = $('#regForm')[0]; 
      let form1 = new FormData(form_cust);
      $.ajax({
        type: "POST",
        url: '{{route('addmasterproductwizard')}}',
        data: form1, 
        processData: false,
        contentType: false,
        success: function( response ) {
          if(response.error == 0){
            toastr.success(response.msg);
            window.location.href = response.url
          }else{
            $(".submit").attr("disabled", false);
            toastr.error(response.msg);
          }
        },
        error: function(data){
          $(".submit").attr("disabled", false);
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

  $(document).on('change','.all_product_category',function () {
	 var id = $(this).val();
	 var level = $(this).data('id');
	 var myurl = "{{url('getsubcategories')}}" +"/"+ id;
	 var token = "{{csrf_token()}}";
	 
	//  for(var i=level+2; i<=10; i++){
	// 	$('#div_product_subcategory'+i).remove();
	//  }

	 $.ajax({
		url:myurl,
		type:'GET',
		dataType:'json',
		data: {
			'_token': token,
			'_method': 'GET',
			'id' : id,
			},
		success:function (response) {
			var len = 0;
			if (response.data != null) {
				len = response.data.length;
			}
      console.log(response.data);
      
			var html = '';
			$("#div_product_subcategory"+(level+1)).remove();
			if (len>0) {
				html += '<div class="form-group col-md-12" id="div_product_subcategory'+(level+1)+'">';
				html += '<label for="product_subcategory1" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="2nd product category of the hierarchy, i.e. Frozen Meals">Product Sub-Category '+(level+1)+'</label>'
				html += '<select id="product_subcategory'+(level+1)+'" name="product_subcategory'+(level+1)+'" class="form-control select2 all_product_category" id="product_subcategory'+(level+1)+'" data-id="'+(level+1)+'">';
				html += '<option value=""> -- Select a value  -- </option>';
				for (var i = 0; i<len; i++) {
					 var id = response.data[i].id;
					 var name = response.data[i].name;
					 if(name != null )
					{
						html += '<option value='+id+'>'+name+'</option>';
					}
				}
				html += '</select>';
				html += '</div><div id="next_product_container_'+(level+1)+'"></div>';
				$('#next_product_container_'+level).html(html);
				// $("#product_subcategory"+(level+1)).select2();
        $('#product_subcategory'+(level+1)).select2({
          dropdownParent: $('#MyModalwizard')
      });
				
			}
		}
	})
});
</script>
<script>
	$("#AllergensID").hide();
	$("#AllergensID2").hide();
	$(document).on('click','.all',function(e){
      var editans = $("select[name='AllergensDropd']").val();
      if(editans == 'Yes'){
    $("#AllergensID").show();
    $("#AllergensID2").show();
      }else{
          $("#AllergensID").hide();
    $("#AllergensID2").hide();
      }
  });

	$("#MFG_shelf_lifeL").hide();
	$("#country_of_originL").hide();
	$("#storageL").hide();
	$("#package_informationL").hide();
	$(document).on('click','#btn_more',function(e){
		$("#MFG_shelf_lifeL").toggle();
		$("#country_of_originL").toggle();
		$("#storageL").toggle();
		$("#package_informationL").toggle();
  });

	  var i = 1;
    var j = 1;
    function AddRow(){
      j++;
      i++;
      var html = '';
		  if(i <= 5){
        html+='<input type="text" class="form-control mb-3" name="about_this_item[]" id="about_this_item_'+i+'" placeholder="Point '+i+'">';
        // console.log(html);
            $("#about_append").append(html);  
        if(i == 5){
          $('#add_about').hide();
        }
      }else{
        $('#add_about').hide();
      }
    }
    
    $('.select2').select2({
        dropdownParent: $('#MyModalwizard')
    });

    function changeSuppliers(type) {
  
      if (type === 'client') {
        toAppend = @json($client);
      } else if (type === 'supplier') {
        toAppend = @json($supplier)
      }      
      var select_elem = document.getElementById('current_supplier');
      var options = select_elem.getElementsByTagName('option');
      for (var i = options.length; i--;) {	
         select_elem.removeChild(options[i]);
      }
      
      let opt = document.createElement("option");
      opt.value = ''; 
      opt.innerHTML = '-- Select a value  --'; 
      select_elem.append(opt);
      
      for (var key in toAppend) {
        let opt = document.createElement("option");
        opt.value = key; 
        opt.innerHTML = toAppend[key]; 
        select_elem.append(opt); 
      }
    }
    
</script>
<script>
  function disableEnableTextBox(value, id){
	if (!value.checked) {
		$("#" + id).attr("disabled", "disabled");	
		$("#" + id).val("");
	} else {
		$("#" + id).removeAttr("disabled");
	}
}
</script>

        