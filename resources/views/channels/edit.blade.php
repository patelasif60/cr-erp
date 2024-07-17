<div class="modal-dialog mt-5">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="editModalLabel">Edit Channel</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="{{ route('clients.updateChannel',$id) }}" method="POST" id="editChannelForm">
            {{ csrf_field() }}
            <input type="hidden" name="client_id" id="client_id" value="{{ $row->client_id }}">
            <div class="row">
                <div class="col-md-12">
                    <label for="channel_type">Channel Type *</label>
                    <select name="channel_type" id="channel_type" class="form-control required">
                        <option value="">--Select--</option>
                        <option value="Amazon" @if($row->channel_type == "Amazon") selected @endif>Amazon</option>
                        <option value="BigCommerce" @if($row->channel_type == "BigCommerce") selected @endif>BigCommerce</option>
                        <option value="Kroger" @if($row->channel_type == "Kroger") selected @endif>Kroger</option>
                        <option value="Shopify" @if($row->channel_type == "Shopify") selected @endif>Shopify</option>
                        <option value="Walmart DSV" @if($row->channel_type == "Walmart DSV") selected @endif>Walmart DSV</option>
                        <option value="Walmart Marketplace" @if($row->channel_type == "Walmart Marketplace") selected @endif>Walmart Marketplace</option>
                        <option value="WooCommerce" @if($row->channel_type == "WooCommerce") selected @endif>WooCommerce</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="channel">Channel Name *</label>
                    <input type="text" name="channel" id="channel" class="form-control required" value="{{ $row->channel }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="store_url">Store URl</label>
                    <input type="text" name="store_url" id="store_url" class="form-control" value="{{ $row->store_url }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="admin_url">Admin URL</label>
                    <input type="text" name="admin_url" id="admin_url" class="form-control" value="{{ $row->admin_url }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="username">Username</label>
                    <input type="email" name="username" id="username" class="form-control" value="{{ $row->username }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="password">Password </label>
                    <input type="text" name="password" id="password" class="form-control" value="{{ $row->channel }}">
                </div>
            </div>
            <div class="modal-footer mt-4">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </form>
      </div>
    </div>
</div>

<script>
    // $('#editChannelForm').validate();
    $("#editChannelForm").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
		var form_cust = $('#editChannelForm')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('clients.updateChannel',$id) }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
					toastr.success(response.msg);
					GetChanels();
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
</script>
