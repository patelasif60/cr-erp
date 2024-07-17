     
<div class="modal-dialog">
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
                    <input required type="text" value="{{ $id > 0 ? $carrierAccounts->description:'' }}" class="form-control" name="description" placesholder="enter Description" id="description"/> 
                </div>
                <div class="form-group">
                    <label for="description">Carrier</label>
                    <select required id="carrier_id" name="carrier_id" class="form-control">
                        <option value="">Select</option>
                        @if($carriers)
                            @foreach($carriers as $row)
                                <option {{ $id > 0  ? $carrierAccounts->carrier_id == $row->id ? 'selected' : ''  : '' }} value="{{ $row->id }}">{{ $row->company_name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <label for="account">Account#</label>
                    <input required type="text" value="{{ $id > 0 ? $carrierAccounts->account_number:'' }}" class="form-control" name="account" placesholder="enter account" id="account"/> 
                </div>
                <div class="form-group">
                    <label for="apikey">API Key</label>
                    <input required type="text" value="{{ $id > 0 ? $carrierAccounts->api_key:'' }}" class="form-control" name="apikey" placesholder="enter api key" id="apikey"/> 
                </div>
                <div class="form-group">
                    <label for="account_rules">Account Rules</label>
                     <select required id="account_rules" name="account_rules" class="form-control">
                        <option value="">Select</option>
                        <option {{ $id > 0 ? $carrierAccounts->account_rules =='Do Not Return' ? 'selected' : ''  : '' }} value="Do Not Return">Do Not Return </option>
                        <option  {{ $id > 0 ? $carrierAccounts->account_rules =='Shipper Release' ? 'selected' : ''  : '' }} value="Shipper Release"> Shipper Release</option>
                        <option  {{ $id > 0 ? $carrierAccounts->account_rules =='Deliver on first attempt' ? 'selected' : ''  : '' }} value="Deliver on first attempt"> Deliver on first attempt</option>
                    </select>
                    <!-- <textarea required class="form-control" name="account_rules" id="account_rules">{{ $id > 0 ? $carrierAccounts->account_rules:'' }}</textarea> --> 
                </div>
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
<script>
$("#add_carrier_account_form").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        var form_cust = $('#add_carrier_account_form')[0]; 
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '{{route('carriers.createCarrierAccountStore')}}',
            data: form1, 
            processData: false,
            contentType: false,
            success: function( response ) {
                if(response.error == 0){
                    toastr.success(response.msg);
                    setTimeout(function(){
                        GetAllCarrierAccounts();
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
</script>