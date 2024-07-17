     
<div class="modal-dialog modal-lg">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Carrier Account</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        <form  method="POST" action="javascript:void(0)" id="add_order_upgrades" >
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="rules">Client</label>
                    <select name="client_id" onchange="getchannel(this)" class="form-control">
                        <option value="">Select</option>
                        @if($client)
                            @foreach($client as $key_c => $row_c)
                                <option {{$id > 0 && $key_c == $row_orders->client_id?'selected':''}}  value="{{$key_c}}">{{ $row_c }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
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
                <div class="form-group">
                    <lable for="group_details">Temperature</lable>
                    <?php $selected_temp = $id > 0 ? explode(',',$row_orders->group_detail) : [];?>
                    <select name="group_details[]" id="group_details" class="form-control select2" multiple>
                        <option @if(in_array("dry",$selected_temp)) selected=selected @endif value="dry">dry</option>
                        <option @if(in_array("frozen",$selected_temp)) selected=selected @endif value="frozen">frozen</option>
                        <option @if(in_array("refreg",$selected_temp)) selected=selected @endif value="refreg">refreg</option>
                    </select>
                </div>
                <div class="form-group">
                    <lable for="group_details">Select Transit Day</lable>
                    <?php $selected_days = $id > 0 ? explode(',',$row_orders->transit_day) : [];?>
                    <select name="days[]" id="days" class="form-control select2" multiple>
                        <option @if(in_array(1,$selected_days)) selected=selected @endif value="1">1</option>
                        <option @if(in_array(2,$selected_days)) selected=selected @endif value="2">2</option>
                        <option @if(in_array(3,$selected_days)) selected=selected @endif value="3">3</option>
                        <option @if(in_array(4,$selected_days)) selected=selected @endif value="4">4</option>
                        <option @if(in_array(5,$selected_days)) selected=selected @endif value="5">5</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="service_type_ups" class="ul-form__label">Ups</label>
                    <select id="service_type_ups" name="service_type_ups" class="form-control select2" >
                        @foreach($shippingServiceTypes as $shippingServiceType)
                            <option {{$id > 0 && $shippingServiceType['id'] == $row_orders->service_type_id?'selected':''}}  value="{{$shippingServiceType['id']}}">{{$shippingServiceType['service_name']}}</option>
                        @endforeach
                    </select>
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
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script src="jquery.tag-editor.js"></script>

<script>
$("#add_order_upgrades").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        var form_cust = $('#add_order_upgrades')[0]; 
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '{{route('carriers.storeOrderUpgrades')}}',
            data: form1, 
            processData: false,
            contentType: false,
            success: function( response ) {
                if(response.error == 0){
                    toastr.success(response.msg);
                    setTimeout(function(){
                        GetAutomaticupgrades();
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
function getchannel(x) {
    $.ajax({
            url: '{{route('carriers.getDropdown')}}',
            method:'GET',
            data:{id:x.value},
            success:function(res){
                $("#client_channel_configurations_ids").html(res);
            }
        });
}

</script>