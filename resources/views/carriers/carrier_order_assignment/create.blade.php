     
<div class="modal-dialog modal-lg">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Carrier Account</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        <form  method="POST" action="javascript:void(0)" id="add_carrier_account_form" >
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="description">Description</label>
                    <input required type="text" value="{{ $id > 0 ? $row_orders->description:'' }}" class="form-control" name="description" placesholder="enter Description" id="description" @if($id == 1) readonly @endif/> 
                </div>
                @if($id != 1)
                <div class="form-group">
                    <label for="rules">Rules</label>
                    <select onchange="displyField()" name="rules" id="rules" class="form-control">
                        <option value="Processing Group" @if($id > 0 && $row_orders->rules == 'Processing Group') selected = selected @endif>Processing Group & WH (DEFAULT)</option>
                        <option value="Client" @if($id > 0 && $row_orders->rules == 'Client') selected = selected @endif>Client</option>
                        <option value="3rd Party Billing" @if($id > 0 && $row_orders->rules == '3rd Party Billing') selected = selected @endif>3rd Party Billing</option>
                        <option value="Zip Code" @if($id > 0 && $row_orders->rules == 'Zip Code') selected = selected @endif>Zip Code</option>
                    </select>
                </div>
                <div  class="js-client form-group {{ $id > 0 && $row_orders->client_id  ? ' ' :' d-none '}} ">
                    <label for="rules">Client</label>
                    <select onchange="getchannel1(this)" name="client_id" class="form-control">
                        <option value="">Select</option>
                        @if($client)
                            @foreach($client as $key_c => $row_c)
                                <option value="{{$key_c}}" {{$id > 0 && $key_c == $row_orders->client_id?'selected':''}}  >{{ $row_c }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group js-client form-group {{ $id > 0 && $row_orders->client_id  ? ' ' :' d-none '}} ">
                    <label for="rules">Client Channel</label>
                    <select multiple id="client_channel_configurations_ids" name="client_channel_configurations_ids[]" class="form-control">
                        <option value="">Select</option>
                        <?php $selected_channel = $id > 0 ? explode(',',$row_orders->client_channel_configurations_ids) : [];?>
                        @if($id > 0)
                            @foreach($chanels as $key_c => $row_c)
                                <option @if(in_array($key_c,$selected_channel)) selected=selected @endif value="{{$key_c}}">{{ $row_c }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="js-zip form-group {{ $id > 0 && $row_orders->group_details  ? ' ' : ' d-none '}} ">
                    <lable for="group_details">Temperature</lable>
                    <?php $selected_temp = $id > 0 ? explode(',',$row_orders->group_details) : [];?>
                    <select name="group_details[]" id="group_details" onchange="displayZipField('add')" class="form-control select2" multiple>
                        <option @if(in_array("dry",$selected_temp)) selected=selected @endif value="dry">dry</option>
                        <option @if(in_array("frozen",$selected_temp)) selected=selected @endif value="frozen">frozen</option>
                        <option @if(in_array("refrigerated",$selected_temp)) selected=selected @endif value="refrigerated">refrigerated</option>
                    </select>
                </div>
                <input type="hidden" name="ruletype" id="ruletype" value="{{ $id > 0 && $row_orders->group_details ? $row_orders->group_details : '' }}">
                <div class="js-zip form-group {{ $id > 0 && $row_orders->group_details  ? ' ' : ' d-none '}}">
                    <label for="warehouses" class="ul-form__label">Warehouse</label>
                    <select id="warehouses" onchange="displayZipField('add')" name="warehouses" class="form-control select2" >
                        @foreach($warehouses as $warehouse)
                        <option  {{$id > 0 && $warehouse->warehouses == $row_orders->warehouse?'selected':''}} value="{{$warehouse->warehouses}}">{{$warehouse->warehouses}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="js-zip form-group {{ $id > 0 && $row_orders->group_details  ? ' ' : ' d-none '}}">
                    <label for="tags" class="ul-form__label">Zipcode</label>
                    <input type="text" name="tags" id="tags" value="{{ $id > 0 && $row_orders->zipcode ? $row_orders->zipcode : '' }}" />
                </div> 
                @endif
                <table class="table table-bordered mt-3">
                    <tr>
                        <th>Temperature/<br>Processing Group</th>
                        <th class="js-display WI">WI</th>
                        <th class="js-display PA">NV</th>
                        <th class="js-display NV">OKC</th>
                        <th class="js-display OKC">PA</th>
                    </tr>
                    <tr class="js-display dry">
                        <th>Dry</th>
                        <td  class="js-display WI">
                            <select  id="dry_wi_carrier_id" name="dry_wi_carrier_id" class="carrier form-control" onChange="ReRenderCarrierAccount('dry_wi_carrier_id','dry_wi_account_id')">
                                <option value="">Select carrier</option>
                                @if($carriers)
                                    @foreach($carriers as $row)
                                        <option value="{{ $row->id }}" @if($id > 0 && $row_orders->dry_wi_carrier_id == $row->id) selected = "selected" @endif>{{ $row->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <select  id="dry_wi_account_id" name="dry_wi_account_id" class="form-control carrier mt-3">
                                <option value="">Select Account</option>
                                @if($carrier_accounts)
                                    @foreach($carrier_accounts as $row)
                                        @if($id > 0 &&  $row->carrier_id == $row_orders->dry_wi_carrier_id)
                                            <option value="{{ $row->id }}" @if($id > 0 && $row_orders->dry_wi_account_id == $row->id) selected = selected @endif>{{ $row->account_number }} - {{ $row->description }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        <td  class="js-display NV">
                            <select  id="dry_nv_carrier_id" name="dry_nv_carrier_id" class="form-control carrier" onChange="ReRenderCarrierAccount('dry_nv_carrier_id','dry_nv_account_id')">
                                <option value="">Select carrier</option>
                                @if($carriers)
                                    @foreach($carriers as $row)
                                        <option value="{{ $row->id }}" @if($id > 0 && $row_orders->dry_nv_carrier_id == $row->id) selected = selected @endif>{{ $row->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <select  id="dry_nv_account_id" name="dry_nv_account_id" class="form-control carrier mt-3">
                                <option value="">Select Account</option>
                                @if($carrier_accounts)
                                    @foreach($carrier_accounts as $row)
                                        @if($id > 0 &&  $row->carrier_id == $row_orders->dry_nv_carrier_id)
                                            <option value="{{ $row->id }}" @if($id > 0 && $row_orders->dry_nv_account_id == $row->id) selected = selected @endif>{{ $row->account_number }} - {{ $row->description }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        <td class="js-display OKC">
                            <select  id="dry_ok_carrier_id" name="dry_ok_carrier_id" class="form-control carrier" onChange="ReRenderCarrierAccount('dry_ok_carrier_id','dry_ok_account_id')">
                                <option value="">Select carrier</option>
                                @if($carriers)
                                    @foreach($carriers as $row)
                                        <option value="{{ $row->id }}" @if($id > 0 && $row_orders->dry_ok_carrier_id == $row->id) selected = selected @endif>{{ $row->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <select  id="dry_ok_account_id" name="dry_ok_account_id" class="form-control carrier mt-3">
                                <option value="">Select Account</option>
                                @if($carrier_accounts)
                                    @foreach($carrier_accounts as $row)
                                        @if($id > 0 &&  $row->carrier_id == $row_orders->dry_ok_carrier_id)
                                            <option value="{{ $row->id }}" @if($id > 0 && $row_orders->dry_ok_account_id == $row->id) selected = selected @endif>{{ $row->account_number }} - {{ $row->description }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        <td class="js-display PA">
                            <select  id="dry_pa_carrier_id" name="dry_pa_carrier_id" class="form-control carrier" onChange="ReRenderCarrierAccount('dry_pa_carrier_id','dry_pa_account_id')">
                                <option value="">Select carrier</option>
                                @if($carriers)
                                    @foreach($carriers as $row)
                                        <option value="{{ $row->id }}" @if($id > 0 && $row_orders->dry_pa_carrier_id == $row->id) selected = selected @endif>{{ $row->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <select  id="dry_pa_account_id" name="dry_pa_account_id" class="form-control carrier mt-3">
                                <option value="">Select Account</option>
                                @if($carrier_accounts)
                                    @foreach($carrier_accounts as $row)
                                        @if($id > 0 &&  $row->carrier_id == $row_orders->dry_pa_carrier_id)
                                            <option value="{{ $row->id }}" @if($id > 0 && $row_orders->dry_pa_account_id == $row->id) selected = selected @endif>{{ $row->account_number }} - {{ $row->description }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </td>
                    </tr>
                    <tr class="js-display frozen">
                        <th>Frozen</th>
                        <td class="js-display WI">
                            <select  id="frozen_wi_carrier_id" name="frozen_wi_carrier_id" class="form-control carrier" onChange="ReRenderCarrierAccount('frozen_wi_carrier_id','frozen_wi_account_id')">
                                <option value="">Select carrier</option>
                                @if($carriers)
                                    @foreach($carriers as $row)
                                        <option value="{{ $row->id }}" @if($id > 0 && $row_orders->frozen_wi_carrier_id == $row->id) selected = selected @endif>{{ $row->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <select  id="frozen_wi_account_id" name="frozen_wi_account_id" class="form-control carrier mt-3">
                                <option value="">Select Account</option>
                                @if($carrier_accounts)
                                    @foreach($carrier_accounts as $row)
                                        @if($id > 0 &&  $row->carrier_id == $row_orders->frozen_wi_carrier_id)
                                            <option value="{{ $row->id }}" @if($id > 0 && $row_orders->frozen_wi_account_id == $row->id) selected = selected @endif>{{ $row->account_number }} - {{ $row->description }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        <td  class="js-display NV">
                            <select  id="frozen_nv_carrier_id" name="frozen_nv_carrier_id" class="form-control carrier" onChange="ReRenderCarrierAccount('frozen_nv_carrier_id','frozen_nv_account_id')">
                                <option value="">Select carrier</option>
                                @if($carriers)
                                    @foreach($carriers as $row)
                                        <option value="{{ $row->id }}" @if($id > 0 && $row_orders->frozen_nv_carrier_id == $row->id) selected = selected @endif>{{ $row->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <select  id="frozen_nv_account_id" name="frozen_nv_account_id" class="form-control carrier mt-3">
                                <option value="">Select Account</option>
                                @if($carrier_accounts)
                                    @foreach($carrier_accounts as $row)
                                        @if($id > 0 &&  $row->carrier_id == $row_orders->frozen_nv_carrier_id)
                                            <option value="{{ $row->id }}" @if($id > 0 && $row_orders->frozen_nv_account_id == $row->id) selected = selected @endif>{{ $row->account_number }} - {{ $row->description }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        <td class="js-display OKC">
                            <select  id="frozen_ok_carrier_id" name="frozen_ok_carrier_id" class="form-control carrier" onChange="ReRenderCarrierAccount('frozen_ok_carrier_id','frozen_ok_account_id')">
                                <option value="">Select carrier</option>
                                @if($carriers)
                                    @foreach($carriers as $row)
                                        <option value="{{ $row->id }}" @if($id > 0 && $row_orders->frozen_ok_carrier_id == $row->id) selected = selected @endif>{{ $row->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <select  id="frozen_ok_account_id" name="frozen_ok_account_id" class="form-control carrier mt-3">
                                <option value="">Select Account</option>
                                @if($carrier_accounts)
                                    @foreach($carrier_accounts as $row)
                                        @if($id > 0 &&  $row->carrier_id == $row_orders->frozen_ok_carrier_id)
                                            <option value="{{ $row->id }}" @if($id > 0 && $row_orders->frozen_ok_account_id == $row->id) selected = selected @endif>{{ $row->account_number }} - {{ $row->description }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        <td class="js-display PA">
                            <select  id="frozen_pa_carrier_id" name="frozen_pa_carrier_id" class="form-control carrier" onChange="ReRenderCarrierAccount('frozen_pa_carrier_id','frozen_pa_account_id')">
                                <option value="">Select carrier</option>
                                @if($carriers)
                                    @foreach($carriers as $row)
                                        <option value="{{ $row->id }}" @if($id > 0 && $row_orders->frozen_pa_carrier_id == $row->id) selected = selected @endif>{{ $row->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <select  id="frozen_pa_account_id" name="frozen_pa_account_id" class="form-control carrier mt-3">
                                <option value="">Select Account</option>
                                @if($carrier_accounts)
                                    @foreach($carrier_accounts as $row)
                                        @if($id > 0 &&  $row->carrier_id == $row_orders->frozen_pa_carrier_id)
                                            <option value="{{ $row->id }}" @if($id > 0 && $row_orders->frozen_pa_account_id == $row->id) selected = selected @endif>{{ $row->account_number }} - {{ $row->description }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </td>
                    </tr>
                    <tr class="js-display refreg">
                        <th>Refrigerated</th>
                        <td class="js-display WI">
                            <select  id="refreg_wi_carrier_id" name="refreg_wi_carrier_id" class="form-control carrier" onChange="ReRenderCarrierAccount('refreg_wi_carrier_id','refreg_wi_account_id')">
                                <option value="">Select carrier</option>
                                @if($carriers)
                                    @foreach($carriers as $row)
                                        <option value="{{ $row->id }}" @if($id > 0 && $row_orders->refreg_wi_carrier_id == $row->id) selected = selected @endif>{{ $row->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <select  id="refreg_wi_account_id" name="refreg_wi_account_id" class="form-control carrier mt-3">
                                <option value="">Select Account</option>
                                @if($carrier_accounts)
                                    @foreach($carrier_accounts as $row)
                                        @if($id > 0 &&  $row->carrier_id == $row_orders->refreg_wi_carrier_id)
                                            <option value="{{ $row->id }}" @if($id > 0 && $row_orders->refreg_wi_account_id == $row->id) selected = selected @endif>{{ $row->account_number }} - {{ $row->description }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        <td  class="js-display NV">
                            <select  id="refreg_nv_carrier_id" name="refreg_nv_carrier_id" class="form-control carrier" onChange="ReRenderCarrierAccount('refreg_nv_carrier_id','refreg_nv_account_id')">
                                <option value="">Select carrier</option>
                                @if($carriers)
                                    @foreach($carriers as $row)
                                        <option value="{{ $row->id }}" @if($id > 0 && $row_orders->refreg_nv_carrier_id == $row->id) selected = selected @endif>{{ $row->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <select  id="refreg_nv_account_id" name="refreg_nv_account_id" class="form-control carrier mt-3">
                                <option value="">Select Account</option>
                                @if($carrier_accounts)
                                    @foreach($carrier_accounts as $row)
                                        @if($id > 0 &&  $row->carrier_id == $row_orders->refreg_nv_carrier_id)
                                            <option value="{{ $row->id }}" @if($id > 0 && $row_orders->refreg_nv_account_id == $row->id) selected = selected @endif>{{ $row->account_number }} - {{ $row->description }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        <td class="js-display OKC">
                            <select  id="refreg_ok_carrier_id" name="refreg_ok_carrier_id" class="form-control carrier" onChange="ReRenderCarrierAccount('refreg_ok_carrier_id','refreg_ok_account_id')">
                                <option value="">Select carrier</option>
                                @if($carriers)
                                    @foreach($carriers as $row)
                                        <option value="{{ $row->id }}" @if($id > 0 && $row_orders->refreg_ok_carrier_id == $row->id) selected = selected @endif>{{ $row->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <select  id="refreg_ok_account_id" name="refreg_ok_account_id" class="form-control carrier mt-3">
                                <option value="">Select Account</option>
                                @if($carrier_accounts)
                                    @foreach($carrier_accounts as $row)
                                        @if($id > 0 &&  $row->carrier_id == $row_orders->refreg_ok_carrier_id)
                                            <option value="{{ $row->id }}" @if($id > 0 && $row_orders->refreg_ok_account_id == $row->id) selected = selected @endif>{{ $row->account_number }} - {{ $row->description }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        <td class="js-display PA">
                            <select  id="refreg_pa_carrier_id" name="refreg_pa_carrier_id" class="form-control carrier" onChange="ReRenderCarrierAccount('refreg_pa_carrier_id','refreg_pa_account_id')">
                                <option value="">Select carrier</option>
                                @if($carriers)
                                    @foreach($carriers as $row)
                                        <option value="{{ $row->id }}" @if($id > 0 && $row_orders->refreg_pa_carrier_id == $row->id) selected = selected @endif>{{ $row->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <select  id="refreg_pa_account_id" name="refreg_pa_account_id" class="form-control carrier mt-3">
                                <option value="">Select Account</option>
                                @if($carrier_accounts)
                                    @foreach($carrier_accounts as $row)
                                        @if($id > 0 &&  $row->carrier_id == $row_orders->refreg_pa_carrier_id)
                                            <option value="{{ $row->id }}" @if($id > 0 && $row_orders->refreg_pa_account_id == $row->id) selected = selected @endif>{{ $row->account_number }} - {{ $row->description }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </td>
                    </tr>
                    
                    
                </table>
                 <input type="hidden" name="id" id="id" value="{{$id}}">
            </div> 
            
                
            <div class="modal-footer"> 
                <button type="submit" class="btn btn-primary submit" id="add_manufacturer">Save</button> 
                <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('assets/js/validation/jquery.validate.js') }}"></script>
<script src="{{ asset('assets/js/validation/additional-methods.min.js') }}"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>


<script>
 $(document).ready(function () {
    count = -1;
    editId = $('#ruletype').val();
    if(editId)
    {
        count = 0;
        displayZipField('edit');
    }
    
 });
$("#add_carrier_account_form").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        var form_cust = $('#add_carrier_account_form')[0]; 
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '{{route('carriers.storeAssignedOrderAccounts')}}',
            data: form1, 
            processData: false,
            contentType: false,
            success: function( response ) {
                if(response.error == 0){
                    toastr.success(response.msg);
                    setTimeout(function(){
                        GetDefaultCarrierDefaultOrderAssigned();
                         GetAllCarrierAccountsAssignments();
                        $("#MyCarrierModal").modal('hide');
                    },2000);
                }else{
                    $(".submit").attr("disabled", false);
                    toastr.error(response.msg);
                }
            },
            error: function(data){
                $(".submit").attr("disabled", false);
                var errors = data.responseJSON;
                $.each( errors.errors, function( key, value ) {
                    var ele = "#"+key;
                    $(ele).addClass('error');
                    $('<label class="error">'+ value +'</label>').insertAfter(ele);
                });
          }
        })
        return false;
    }
});
function displyField(){
    if($("#rules").val() == 'Client' || $("#rules").val() == '3rd Party Billing'){
        $(".js-client ").removeClass('d-none');
        $(".js-zip ").addClass('d-none');
        $(".js-display").removeClass('d-none');
    }
    if($("#rules").val() == 'Zip Code'){
        $(".js-client ").addClass('d-none');
        $(".js-zip ").removeClass('d-none');
        $(".js-display").removeClass('d-none');
    }
    if($("#rules").val() == 'Processing Group'){
        $(".js-client ").addClass('d-none');
        $(".js-zip ").addClass('d-none');
        $(".js-display").removeClass('d-none');
    }
}
function displayZipField(mode){
    $(".js-display").addClass('d-none');
    GroupArray =  $("#group_details").val()
    GroupArray.forEach(function(item) {
         $("."+ item).removeClass('d-none');
    });
    $("." +$("#warehouses").val() ).removeClass('d-none');
    if(mode == 'add' && count== 0){
        count=1;
        //$('#carrier_id option:selected').remove();
        $('.carrier option:selected').removeAttr('selected');
        //$("#carrier_id").removeAttr('selected');
    }
}
</script>
<script stype="text/javascript">
    jQuery(document).ready(function($) {
      var tags = $('#tags').inputTags({
        tags: [],
        autocomplete: {
          values: []
        },
        init: function(elem) {
          $('span', '#events').text('init');
        },
        create: function() {
          $('span', '#events').text('create');
        },
        update: function() {
          $('span', '#events').text('update');
        },
        destroy: function() {
          $('span', '#events').text('destroy');
        },
        selected: function() {
          $('span', '#events').text('selected');
        },
        unselected: function() {
          $('span', '#events').text('unselected');
        },
        change: function(elem) {
          $('.results').empty().html('<strong>Tags:</strong> ' + elem.tags.join(' - '));
        },
        blur: function(){
          //  $('span', '#events').text('create');
        },
        autocompleteTagSelect: function(elem) {
          console.info('autocompleteTagSelect');
        }
      });

      
    });
    function getchannel1(x) {
    $.ajax({
            url: '{{route('carriers.getDropdown')}}',
            method:'GET',
            data:{id:x.value},
            success:function(res){
                $("#client_channel_configurations_ids").html(res);
            }
        });
    }

    function ReRenderCarrierAccount(from, to){
        $carrier_id = $("#"+from).val();
        var html  = '';
        
        @if($carrier_accounts)
            @foreach($carrier_accounts as $row)
                var current_carrier_id = {{$row->carrier_id}}
                if(current_carrier_id == $carrier_id){
                    html+= '<option value="{{$row->id}}">{{ $row->account_number }} - {{ $row->description }}</option>';
                }

            @endforeach
        @endif

        $("#"+to).html(html);
    }
    
  </script>