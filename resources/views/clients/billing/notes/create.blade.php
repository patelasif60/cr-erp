<div class="modal-dialog mt-5">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="noteModalLabel">Add Note</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="javascript:void(0);" method="POST" id="storeNoteForm">
            @csrf
            <input type="hidden" name="client_id" id="client_id" value="{{ $id }}">
            <div class="row">
                <div class="col-md-8">
                    <label for="channel">Type *</label>
                    <!-- <select name="type" id="type" class="form-control" required>
                        <option value="Reship / Credit">Reship / Credit</option>
                        <option value="Pallet Out">Pallet Out</option>
                        <option value="Pallet In">Pallet In</option>
                        <option value="Pallet Disposal">Pallet Disposal</option>
                        <option value="Frozen Monthly High Pallet Count">Frozen Monthly High Pallet Count</option>
                        <option value="Ambient Monthly High Pallet Count">Ambient Monthly High Pallet Count</option>
                        <option value="Refrigerated Monthly High Pallet Count">Refrigerated Monthly High Pallet Count</option>
                        <option value="Outbound Pallet Creation">Outbound Pallet Creation</option>
                        <option value="Additional IT time">Additional IT time</option>
                        <option value="Additional Managerial Time">Additional Managerial Time</option>
                        <option value="Additional Inventory Time">Additional Inventory Time</option>
                        <option value="kitting">kitting</option>
                        <option value="temperature tracking">temperature tracking</option>
                        <option value="ordering hours">ordering hours</option>
                        <option value="ice invoice">ice invoice</option>
                        <option value="additional Item Promo">additional Item Promo</option>
                        <option value="WMS OMS Connection">WMS OMS Connection</option>
                        <option value="WMS OMS Item Creation">WMS OMS Item Creation</option>
                        <option value="Manual Spreadsheet Upload">Manual Spreadsheet Upload</option>
                        <option value="Amazon SKU Creation">Amazon SKU Creation</option>
                        <option value="Walmart SKU Creation">Walmart SKU Creation</option>
                        <option value="Kroger SKU Creation">Kroger SKU Creation</option>
                        <option value="ICS SKU Creation">ICS SKU Creation</option>
                        <option value="RocketDSD SKU Creation">RocketDSD SKU Creation</option>
                        <option value="Other Channel SKU Creation">Other Channel SKU Creation</option>
                        <option value="Amazon Account Creation">Amazon Account Creation</option>
                        <option value="Amazon Brand Registration">Amazon Brand Registration</option>
                        <option value="Amazon Enhanced Storefront">Amazon Enhanced Storefront</option>
                        <option value="Amazon A+ Item Creation">Amazon A+ Item Creation</option>
                        <option value="Product Changes">Product Changes</option>
                        <option value="Product Deletion">Product Deletion</option>
                        <option value="Brandstore Development">Brandstore Development</option>
                        <option value="Brandstore Email Marketing Campaign">Brandstore Email Marketing Campaign</option>
                        <option value="Brandstore Image Adjustments">Brandstore Image Adjustments</option>
                        <option value="Brandstore Promotional Code Setup">Brandstore Promotional Code Setup</option>
                        <option value="Brandstore Hero Carousel Banner Creation">Brandstore Hero Carousel Banner Creation</option>
                        <option value="Outbound Pallet Creation">Outbound Pallet Creation</option>
                        <option value="Other">Other</option>
                    </select> -->
                    <select name="type" id="type" class="form-control" required>
                        @foreach($billingNotes as $billingNote)
                        <option value="{{$billingNote->option}}">{{$billingNote->option}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="date">Task Date *</label>
                    <input name="date" id="date" class="form-control required">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="location">Location</label>
                    <select name="location" id="location" class="form-control">
                        <option value="">Select Location</option>
                        <option value="WI">WI</option>
                        <option value="PA">PA</option>
                        <option value="NV">NV</option>
                        <option value="OKC">OKC</option>
                        <option value="Office">Office</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="details">Details</label>
                    <textarea type="text" name="details" id="details" class="form-control" rows="5"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="document">Upload Document</label>
                    <input type="file" name="document" id="document" class="form-control">
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-8">
                    <label for="invoice_date">Invoice Date</label>
                    <input name="invoice_date" id="invoice_date" class="form-control">
                </div>
            </div>
             <div class="row mt-4">
                <div class="col-md-8">
                   <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="is_billable" name="is_billable" value="1">
                      <label class="form-check-label" for="is_billable">Is Billable</label>
                    </div>
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

$(document).ready(function(){
    $('#date').flatpickr({
        static: true,
        enableTime: false,
        dateFormat: "Y-m-d",
    });
    $('#invoice_date').flatpickr({
        static: true,
        enableTime: false,
        dateFormat: "Y-m-d",
    });
})

$("#storeNoteForm").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
        $('#preloader').show();
		var form_cust = $('#storeNoteForm')[0];
		let form1 = new FormData(form_cust);
        console.log(form1);
		$.ajax({
			type: "POST",
			url: '{{ route('clients.storeBillingNote') }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
                $('#preloader').hide();
				if(response.error == 0){
					toastr.success(response.msg);
					billingNoteList();
                    $('#noteModal').modal('hide');
				}else{
					$(".submit").attr("disabled", false);
					toastr.error(response.msg);
				}
			},
			error: function(data){
				$(".submit").attr("disabled", false);
                $('#preloader').hide();
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

