<div class="modal-dialog mt-5">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="exampleModalLabel">Add Channel</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="javascript:void(0);" method="POST" id="storeChannelForm">
            @csrf
            <input type="hidden" name="client_id" id="client_id" value="{{ $id }}">
            <div class="row">
                <div class="col-md-12">
                    <label for="channel_type">Channel Type *</label>
                    <select name="channel_type" id="channel_type" class="form-control required">
                        <option value="">--Select--</option>
                        <option value="Amazon">Amazon</option>
                        <option value="BigCommerce">BigCommerce</option>
                        <option value="Kroger">Kroger</option>
                        <option value="Shopify">Shopify</option>
                        <option value="Walmart DSV">Walmart DSV</option>
                        <option value="Walmart Marketplace">Walmart Marketplace</option>
                        <option value="WooCommerce">WooCommerce</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="channel">Channel Name *</label>
                    <input type="text" name="channel" id="channel" class="form-control required">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="store_url">Store URl</label>
                    <input type="text" name="store_url" id="store_url" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="admin_url">Admin URL</label>
                    <input type="text" name="admin_url" id="admin_url" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="username">Username</label>
                    <input type="email" name="username" id="username" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="password">Password </label>
                    <input type="password" name="password" id="password" class="form-control">
                </div>
            </div>            
            <div class="modal-footer mt-4">
                <button type="submit" class="btn btn-primary submit">Save</button>
            </div>
          </form>
      </div>
    </div>
</div>

<script>

$("#storeChannelForm").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
		var form_cust = $('#storeChannelForm')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('clients.storeChannel') }}',
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
