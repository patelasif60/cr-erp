<link rel="stylesheet" href="{{asset('assets/styles/css/select2/new/select2.min.css')}}">

<div class="modal-dialog modal-lg">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Carrier Account</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        <form  method="POST" action="javascript:void(0)" id="add_carrier_shipping" >
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="service_type_etailer" class="ul-form__label">e-Tailer</label>
                    <input type="text" class="form-control" name="service_type_etailer" "/>
                </div>
                <div class="form-group">
                    <label for="service_type_ups" class="ul-form__label">Ups</label>
                    <select id="service_type_ups" name="service_type_ups" class="form-control select2" >
                        @foreach($shippingServiceTypes as $shippingServiceType)
                            @if($shippingServiceType['service_type'] == 'ups')
                                <option value="{{$shippingServiceType['id']}}">{{$shippingServiceType['service_name']}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="service_type_fedex" class="ul-form__label">Fedex</label>
                    <select id="service_type_fedex"  name="service_type_fedex" class="form-control select2" >
                        @foreach($shippingServiceTypes as $shippingServiceType)
                            @if($shippingServiceType['service_type'] == 'fedex')
                                <option value="{{$shippingServiceType['id']}}">{{$shippingServiceType['service_name']}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
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
<script src="{{asset('assets/js/select2/new/select2.min.js')}}"></script>


<script>
$('.select2').select2({
    dropdownParent: $('#MyCarrierModal')
});

$("#add_carrier_shipping").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        var form_cust = $('#add_carrier_shipping')[0]; 
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '{{route('carriers.storeCarriershipping')}}',
            data: form1, 
            processData: false,
            contentType: false,
            success: function( response ) {
                if(response.error == 0){
                    toastr.success(response.msg);
                    setTimeout(function(){
                        GetAllServiceConf();
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