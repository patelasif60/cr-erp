<?php $required = '<span class="text-danger">*</span>'; ?>
<div class="modal-dialog mt-5 modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="editEventModalLabel">Lot & Exp. #'s - {{$status}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="#" method="POST" id="editEventForm">
            {{ csrf_field() }}
            <table class="table table-border">
                <tr>
                    <th>ETIN</th>
                    <th>Product Listing Name</th>
                    <th>
                        <div class="row">
                            <div class="col-4">EXP Date</div>
                            <div class="col-4">Lot #</div>
                            <div class="col-4">Qty</div>
                        </div>
                    </th>
                </tr>
                @if($result)
                    @foreach($result as $row)
                        @if($row->product)
                            <tr>
                                <td>{{$row->etin}}</td>
                                <td>{{$row->product->product_listing_name}}</td>
                                <td>
                                    <div id="pro_{{$row->id}}">
                                        <?php #dump($row->exp_and_lot);?>
                                        @if(count($row->exp_and_lot) > 0)
                                            @foreach($row->exp_and_lot as $key_exp => $row_exp)
                                                <div class="row no-gutters mb-1">
                                                    <div class="col-4">
                                                        <input type="text" class="form-control date_exp @if(!in_array($status,['Submitted','Ready',""])) custom_readonly @endif" name="EL[{{$row->id}}][exp_date][{{$key_exp}}]" value="{{$row_exp->exp_date}}" @if(!in_array($status,['Submitted','Ready',""])) readonly @endif>
                                                        <input type="hidden" value="{{$row->etin}}" class="form-control" name="EL[{{$row->id}}][ETIN][0]" >
                                                        <input type="hidden" value="{{$row->asn_bol_shipped_qty}}" class="form-control" name="EL[{{$row->id}}][asn_bol_shipped_qty][0]" >
                                                        <input type="hidden" value="{{$row->bol_number}}" class="form-control" name="EL[{{$row->id}}][bol_number][0]" >
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="text" class="form-control" name="EL[{{$row->id}}][lot][{{$key_exp}}]" value="{{$row_exp->lot}}" @if(!in_array($status,['Submitted','Ready', ''])) readonly @endif>
                                                    </div>
                                                    <div class="col-2">
                                                        <input type="text" class="form-control" name="EL[{{$row->id}}][qty][{{$key_exp}}]" value="{{$row_exp->qty}}" @if(!in_array($status,['Submitted','Ready',""])) readonly @endif>
                                                    </div>
                                                    @if($key_exp != 0 && in_array($status,['Submitted','Ready',""]))
                                                        <div class="col-2">
                                                            <button class="btn btn-danger" type="button" onClick="RemoveMe(this)">-</button>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="row no-gutters">
                                                <div class="col-4">
                                                    <input type="text" class="form-control date_exp" name="EL[{{$row->id}}][exp_date][0]" @if(!in_array($status,['Submitted','Ready',""])) readonly @endif>
                                                    <input type="hidden" value="{{$row->etin}}" class="form-control" name="EL[{{$row->id}}][ETIN][0]">
                                                    <input type="hidden" value="{{$row->asn_bol_shipped_qty}}" class="form-control" name="EL[{{$row->id}}][asn_bol_shipped_qty][0]" >
                                                    <input type="hidden" value="{{$row->bol_number}}" class="form-control" name="EL[{{$row->id}}][bol_number][0]" >
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" class="form-control" name="EL[{{$row->id}}][lot][0]" @if(!in_array($status,['Submitted','Ready',""])) readonly @endif>
                                                </div>
                                                <div class="col-2">
                                                    <input type="text" class="form-control" name="EL[{{$row->id}}][qty][0]" @if(!in_array($status,['Submitted','Ready',""])) readonly @endif>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col text-right">
                                            @if(in_array($status,['Submitted','Ready',""]))
                                                <button class="btn btn-primary" type="button" onClick="AddMore('pro_{{$row->id}}',{{$row->id}})">+</button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @endif
            </table>
            
            <div class="modal-footer mt-4">
                @if(in_array($status,['Submitted','Ready',""]))
                    <button type="submit" class="btn btn-primary submit">Save</button>
                @endif
            </div>
          </form>
      </div>
    </div>
</div>

<!-- <script>
    $('#editEventForm').validate();
</script> -->


<script>
    $('.date_exp').flatpickr({
        static: true,
        enableTime: false,
        dateFormat: "Y-m-d",
    });

    $('.select2').select2({
        dropdownParent: $('#editEventModal')
    });
    // $('#editChannelForm').validate();
    $("#editEventForm").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
		var form_cust = $('#editEventForm')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('purchase_order.SaveLotAndExp',$order) }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
					toastr.success(response.msg);
					// GetLinks();
                    $('#exampleModal').modal('hide');
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
var i = 0;
function AddMore(html_id,id){
    i = i + 1;
    var html = `<div class="row mt-3 no-gutters">
            <div class="col-4">
                <input type="text" class="form-control date_exp" name="EL[${id}][exp_date][${i}]">
            </div>
            <div class="col-4">
                <input type="text" class="form-control" name="EL[${id}][lot][${i}]">
            </div>
            <div class="col-2">
                <input type="text" class="form-control" name="EL[${id}][qty][${i}]">
            </div>
            <div class="col-2">
                <button class="btn btn-danger" type="button" onClick="RemoveMe(this)">-</button>
            </div>
        </div>`;
    
        $("#"+html_id).append(html).promise()
.done(function() {
    $(this).find('.date_exp').flatpickr({
        static: true,
        enableTime: false,
        dateFormat: "Y-m-d",
    });;
});;
}

function RemoveMe($this){
    $($this).parent().parent().remove();
}
</script>
