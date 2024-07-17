<link rel="stylesheet" href="{{asset('assets/styles/css/select2/new/select2.min.css')}}">

<div class="modal-dialog mt-5 modal-lg" style="min-width: 90%">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header bg-light">
            <h5>Create New Outer Box</h5>
            <!--Close/Cross Button-->
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div> 
        <div class="modal-body">
            <table class="table table-bordered text-center" id="order_details">
                <thead class="thead-dark">
                    <tr>
                        <th colspan="2">New Outer Box</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Box Name:</td>
                        <td>
                            <select id="box_name" class="form-control select2" name="box_name" required>
                                <option value="">-Select-</option>
                                @if (isset($outer_box))
                                    @foreach ($outer_box as $ob)                                        
                                        <option value="{{ $ob->id }}">{{ $ob->product_description }} - {{ $ob->ETIN }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </td>                        
                    </tr>
                    <tr>
                        <td>Client:</td>
                        <td>
                            <select required id="client_name" class="form-control select2" name="client_name" onchange="fetchChannelAndProduct(this.value);">
                                <option value="">-Select-</option>
                                @if (isset($clients))
                                    @foreach ($clients as $cl)                                        
                                        <option value="{{ $cl->id }}">{{ $cl->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </td>                        
                    </tr>
                    <tr>
                        <td>Channel:</td>
                        <td>
                            <select id="channel_name" class="form-control select2" name="channel_name[]" multiple>
                                <option value="">-Select-</option>
                            </select>
                        </td>                        
                    </tr>
                    <tr>
                        <td>Product:</td>
                        <td>
                            <select required id="product_name" class="form-control select2" name="product_name[]" multiple>
                                <option value="">-Select-</option>
                            </select>
                        </td>                        
                    </tr>
                    <tr>
                        <td>Transit Day:</td>
                        <td><input required type="number" id="transit_day" name="transit_day" class="form-control"/></td>                        
                    </tr>
                    <tr>
                        <td>Max Item Count:</td>
                        <td><input required type="number" id="max_item_count" name="max_item_count" class="form-control" /></td>                        
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-success float-right" onclick="storeCustomBox();">Save</button>
        </div>
    </div>
</div>

<script src="{{asset('assets/js/select2/new/select2.min.js')}}"></script>
<script>
    $('.select2').select2({
        dropdownParent: $('#MyModalwizard')
    });

    function fetchChannelAndProduct(clientId) {
        $.ajax({
			url:'/get_client_channels_and_product/' + clientId,
			method:'GET',
			success:function(response){
				var channels = response.channels;
				var products = response.products;

                appendChannel(channels);
                appendProduct(products);
			}
		});
    }

    function appendChannel(channelList) {
        var select_elem = document.getElementById('channel_name');
        var options = select_elem.getElementsByTagName('option');
        for (var i = options.length; i--;) {	
            select_elem.removeChild(options[i]);
        }
        
        let opt = document.createElement("option");
        opt.value = ''; 
        opt.innerHTML = '-- Select a value  --'; 
        select_elem.append(opt);
        
        for (var i = 0; i < channelList.length; i++) {
            let opt = document.createElement("option");
            opt.value = channelList[i].id; 
            opt.innerHTML = channelList[i].channel; 
            select_elem.append(opt); 
        }
    }

    function appendProduct(productList) {
        var select_elem = document.getElementById('product_name');
        var options = select_elem.getElementsByTagName('option');
        for (var i = options.length; i--;) {	
            select_elem.removeChild(options[i]);
        }
        
        let opt = document.createElement("option");
        opt.value = ''; 
        opt.innerHTML = '-- Select a value  --'; 
        select_elem.append(opt);
        
        for (var i = 0; i < productList.length; i++) {
            let opt = document.createElement("option");
            opt.value = productList[i].id; 
            opt.innerHTML = productList[i].product_name; 
            select_elem.append(opt); 
        }
    }

    function storeCustomBox() {

        var boxName = $('#box_name').val();
        if (!boxName || boxName == '') { toastr.error('Box name is mandatory'); return; }
        
        var clientId = $('#client_name').val();
        if (!clientId || clientId == '') { toastr.error('Client is mandatory'); return; }
        
        var channelIds = $('#channel_name').val();            
        var productIds = $('#product_name').val();
        if (!productIds || productIds.length <= 0) { toastr.error('Product is mandatory'); return; }
        
        var transitDay = $('#transit_day').val();
        if (!transitDay || transitDay == '') { toastr.error('Transit Day is mandatory'); return; }

        var maxItemCount = $('#max_item_count').val();
        if (!maxItemCount || maxItemCount == '') { toastr.error('Max Item count is mandatory'); return; }

        var form = new FormData();
        form.append('box_name', boxName);
        form.append('client_id', clientId);
        if (channelIds && channelIds.length > 0) form.append('channel_ids', channelIds.join());
        form.append('product_ids', productIds.join());
        form.append('transit_day', transitDay);
        form.append('max_item_count', maxItemCount);

        $.ajax({
			url:'/store_custom_outer',
			method:'POST',
            data: form,
            processData: false,
			contentType: false,
			success:function(response){		
                if(response.error == 0){                    
					toastr.success(response.msg);
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                    $('#MyModalwizard').modal('hide');
				}else{
                    toastr.error(response.msg);					
				}		
			}
		});
    }

</script>
