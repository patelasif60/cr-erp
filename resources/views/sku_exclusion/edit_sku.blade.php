<div class="modal-dialog mt-5">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title">Add Excluded SKU(s)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="javascript:void(0);" method="POST" id="edit_sku">
            <input type="hidden" name="client_id" id="client_id" value="{{ $client_id }}" />
            <input type="hidden" name="bl_sku_id" id="bl_sku_id" value="{{ $mp_id }}" />
            <input type="hidden" name="bl_sku_name" id="bl_sku_name" value="{{ $sku }}" />
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <label for="client"><h5>Client</h5></label>
                    <input class="form-control" type="text" name="client" id="client" value="{{ $client }}" disabled />
                </div>
            </div>
            <br />            
            <div class="row">
                <div class="form-group col-md-12">                    
                    <input type="hidden" name="chanel_ids_bl" id="chanel_ids_bl" value="{{ $channel_ids }}">
                    <div id="client_channel_container_bl"></div>
                </div>                
            </div>
            <br />         
            <div class="row">
                <div class="col-md-12">
                    <label for="wl_sku"><h5>Exclusion SKUs (Comma Seperated Values) *</h5></label>
                    <textarea rows="3" name="bl_sku" id="bl_sku" class="form-control required">{{ $sku }}</textarea>
                </div>
            </div>
            <div class="modal-footer mt-4">
                <button type="button" class="btn btn-danger mr-2" data-dismiss="modal" aria-label="Close">
                    <i class="i-Close mr-2"></i>
                    Cancel
                </button>
                <button type="submit" class="btn btn-success submit">
                    <i class="i-Disk mr-2"></i>
                    Update
                </button>
            </div>
          </form>
      </div>
    </div>
</div>

<script>

    $(document).ready(function () {
        GetClientChanel({{ $client_id }});
    });

    function GetClientChanel(clientId){
        var channel_ids = $("#chanel_ids_bl").val();
        $('#preloader').show();
        $.ajax({
            method:'POST',
            url:'{{ route('getClientChanels') }}',
            data:{
                lobs: clientId,
                bl: 1,
                chanel_ids: channel_ids
            },
            dataType:'html',
            success:function(res){
                $('#preloader').hide();
                $("#client_channel_container_bl").html(res);
            },
            error:function(data) {
                $('#preloader').hide();
            }
        })
    }

    $("#edit_sku").validate({
        submitHandler(form){
            $(".submit").attr("disabled", true);
            var form_cust = $('#edit_sku')[0];
            let form1 = new FormData(form_cust);
            $.ajax({
                type: "POST",
                url: '{{ route('sku.update') }}',
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