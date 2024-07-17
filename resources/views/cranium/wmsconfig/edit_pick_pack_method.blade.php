     
<div class="modal-dialog">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Pick/Pack</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        <form  method="POST" action="javascript:void(0)" id="edit_allergens_form" >
            @csrf
            <div class="modal-body">
                <table class="table table-border">
                    <tr>
                        <th>Client</th>
                        <td colspan="2">
                            <select name="client_id" class="form-control" required>
                                <option value="0">Select</option>
                                @if($client)
                                    @foreach($client as $key_c => $row_c)
                                        <option value="{{$key_c}}" @if($key_c == $row->client_id) selected=selected @endif>{{ $row_c }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Temperature</th>
                        <th>Method of Pick</th>
                        <th>Method of Pac</th>
                    </tr>
                    <tr>
                        <th>Frozen</th>
                        <td>
                            <select class="form-control" name="frozen_pick" required>
                                <option value="">Select</option>
                                <option value="Pick Sheet" @if($row->frozen_pick == 'Pick Sheet')  selected=selected @endif>Pick Sheet</option>
                                <option value="Scan" @if($row->frozen_pick == 'Scan')  selected=selected @endif>Scan</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="frozen_pack" required>
                                <option value="">Select</option>
                                <option value="Pick Sheet" @if($row->frozen_pack == 'Pick Sheet')  selected=selected @endif>Pick Sheet</option>
                                <option value="Scan" @if($row->frozen_pack == 'Scan')  selected=selected @endif>Scan</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Dry</th>
                        <td>
                            <select class="form-control" name="dry_pick" required>
                                <option value="">Select</option>
                                <option value="Pick Sheet" @if($row->dry_pick == 'Pick Sheet')  selected=selected @endif>Pick Sheet</option>
                                <option value="Scan" @if($row->dry_pick == 'Scan')  selected=selected @endif>Scan</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="dry_pack" required>
                                <option value="">Select</option>
                                <option value="Pick Sheet" @if($row->dry_pack == 'Pick Sheet')  selected=selected @endif>Pick Sheet</option>
                                <option value="Scan" @if($row->dry_pack == 'Scan')  selected=selected @endif>Scan</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Refrigerated</th>
                        <td>
                            <select class="form-control" name="refrigerated_pick" required>
                                <option value="">Select</option>
                                <option value="Pick Sheet" @if($row->refrigerated_pick == 'Pick Sheet')  selected=selected @endif>Pick Sheet</option>
                                <option value="Scan" @if($row->refrigerated_pick == 'Scan')  selected=selected @endif>Scan</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="refrigerated_pack" required>
                                <option value="">Select</option>
                                <option value="Pick Sheet" @if($row->refrigerated_pack == 'Pick Sheet')  selected=selected @endif>Pick Sheet</option>
                                <option value="Scan" @if($row->refrigerated_pack == 'Scan')  selected=selected @endif>Scan</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div> 
                
            <div class="modal-footer"> 
                <button type="submit" class="btn btn-primary" >Save</button> 
                <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
            </div>
        </form>
    </div>
</div>

<script>
$("#edit_allergens_form").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        var form_cust = $('#edit_allergens_form')[0]; 
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '{{route('update_pick_pack_method',$row->id)}}',
            data: form1, 
            processData: false,
            contentType: false,
            success: function( response ) {
                if(response.error == 0){
                    toastr.success(response.msg);
                    GetPickPackMethod();
                    $("#MyModalProcessingGroup").modal('hide');
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
