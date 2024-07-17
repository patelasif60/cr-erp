<div class="modal-dialog mt-5 ">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title">Add Excluded SKU(s)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="javascript:void(0);" method="POST" id="store_sku">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <label for="client"><h5>Client *</h5></label>
                    <select name="client" id="client" class="form-control required select2" onchange="change_channel(this)">
                        <option value="">--Select--</option>
                        @isset($clients)
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
            </div>
            <br />
            <div class="row" style="display: none;" id='channel_div'>
                <div class="col-md-12">
                    <label for="channel"><h5>Channel</h5></label>
                    <select name="channel[]" id="channel" class="form-control" multiple>                                              
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">                    
                    <input type="hidden" name="chanel_ids_bl" id="chanel_ids_bl" value="">
                    <div id="client_channel_container_bl"></div>
                </div>                
            </div>
            <br />         
            <div class="row">
                <div class="col-md-12">
                    <label for="wl_sku"><h5>Exclusion SKUs (Comma Seperated Values) *</h5></label>
                    <textarea rows="3" name="bl_sku" id="bl_sku" class="form-control required"></textarea>
                </div>
            </div>
            <div class="modal-footer mt-4">
                <button type="button" class="btn btn-danger mr-2" data-dismiss="modal" aria-label="Close">
                    <i class="i-Close mr-2"></i>
                    Cancel
                </button>
                <button type="submit" class="btn btn-success submit">
                    <i class="i-Disk mr-2"></i>
                    Save
                </button>
            </div>
          </form>
      </div>
    </div>
</div>

<script>
    
    function GetClientChanel(clientId){
        $('#preloader').show();
        $.ajax({
            method:'POST',
            url:'{{ route('getClientChanels') }}',
            data:{
                lobs: clientId,
                bl: 1
            },
            dataType:'html',
            success:function(res){
                $('#preloader').hide();
                $("#client_channel_container_bl").html(res);
            },
            error: function(data) {
                $('#preloader').hide();
            }
        })
    }

    function change_channel(type) {        
        // var channel_div = document.getElementById('channel_div');
        if (type.value.toLowerCase() == '') {
            channel_div.style.display = 'none';
            return;
		}
        
        var selected_client = +document.getElementById('client').options[document.getElementById('client').selectedIndex].value;
        GetClientChanel(selected_client);

        /*var toAppend = @json($channels);

        if (toAppend.length <= 0) return;

        var select_elem = document.getElementById('channel');
		var options = select_elem.getElementsByTagName('option');
		for (var i = options.length; i--;) {	
			select_elem.removeChild(options[i]);
		}
		
		let opt = document.createElement("option");
		opt.value = ''; 
		opt.innerHTML = 'Select'; 
		select_elem.append(opt);
		
		for (let item of toAppend) {
            if (item.client_id != selected_client) continue;
			let opt = document.createElement("option");
			opt.value = item.id; 
			opt.innerHTML = item.channel; 
			select_elem.append(opt);
		}
        channel_div.style.display = '';*/
    }

    $("#store_sku").validate({
        submitHandler(form){
            $(".submit").attr("disabled", true);
            var form_cust = $('#store_sku')[0];
            let form1 = new FormData(form_cust);            
            $.ajax({
                type: "POST",
                url: '{{ route('sku.store') }}',
                data: form1,
                processData: false,
                contentType: false,
                success: function( response ) {
                    if(response.error == 0){
                        toastr.success(response.msg);
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);                        
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