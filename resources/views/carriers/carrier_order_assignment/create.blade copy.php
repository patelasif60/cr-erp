     
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
                    <input required type="text" value="{{ $id > 0 ? $carrierAccounts->description:'' }}" class="form-control" name="description" placesholder="enter Description" id="description"/> 
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th>Temperature/<br>Processing Group</th>
                        @if($warehouses)
                            @foreach($warehouses as $row_ware)
                                <th>{{ $row_ware->warehouses }}</th>
                            @endforeach
                        @endif
                    </tr>
                    @if($processing_groups)
                        @foreach($processing_groups as $row_pro)
                        <tr>
                            <th>{{$row_pro->group_name}}</th>
                            @if($warehouses)
                                @foreach($warehouses as $row_ware)
                                    <td>
                                        <select required id="carrier_id" name="row[{{$row_pro->id}}][{{$row_ware->id}}][carrier_id]" class="form-control">
                                            <option value="">Select carrier</option>
                                            @if($carriers)
                                                @foreach($carriers as $row)
                                                    <option {{ $id > 0  ? $carrierAccounts->carrier_id == $row->id ? 'selected' : ''  : '' }} value="{{ $row->id }}">{{ $row->company_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <select required id="carrier_id" name="row[{{$row_pro->id}}][{{$row_ware->id}}][account_id]" class="form-control mt-3">
                                            <option value="">Select Account</option>
                                            @if($carrier_accounts)
                                                @foreach($carrier_accounts as $row)
                                                    <option {{ $id > 0  ? $carrierAccounts->carrier_id == $row->id ? 'selected' : ''  : '' }} value="{{ $row->id }}">{{ $row->account_number }} - {{ $row->description }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </td>
                                @endforeach
                            @endif
                        </tr>
                        @endforeach
                    @endif
                    
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
<script>
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