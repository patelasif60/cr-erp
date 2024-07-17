<?php
    $clients = \App\Client::all();
?>
<div class="row">
    <div class="col-md-12">
        <div class="card o-hidden mb-4">
            <div class="card-header">
                <h3 class="w-50 float-left card-title m-0">Client Configuration</h3>
            </div>
            <div class="card-body">
                <div class="form-row">                    
                    <div class="form-group col-md-6">
                        <label for="client_config_client">Client</label>
                        <select class="select2" name="client_config_client" id="client_config_client" onchange="get_channel_list(this)">
                            <option value="">Select</option>
                            <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($client->id); ?>"><?php echo e($client->company_name); ?></option>                            
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>                    
                </div>
            </div>
            <div class="card-body">                
                <div class="table-responsive">
                    <table id="channels_tb" class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th scope="col">Channel</th>
                                <th scope="col">
                                    Enable Whitelist/DNE Override
                                    <a id="all_enable_btn" onclick="override()" style="display: none;"> - Override/Enable All</a>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="channels_tb_body">                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#channels_tb').DataTable();
    });

    function get_channel_list(type) {        

        var select_elem = document.getElementById('channels_tb_body');

        if (type && type.value == '') {
            $('#channels_tb').DataTable().row( $(this).parents('tr') )
                .remove()
                .draw();
            $('#all_enable_btn').css('display', 'none');
            return;
        }

        <?php 
            $channels = \App\ClientChannelConfiguration::all();                    
        ?>

        var channels = <?php echo json_encode($channels, 15, 512) ?>;
        if (channels.length <= 0) return;

        removeRows();
        $('#all_enable_btn').css('display', '');
        $('#all_enable_btn').css('text-decoration', 'underline');

        for(let i = 0; i < channels.length; i++) {      
            if (channels[i].client_id != type.value) continue;

            let newRow = select_elem.insertRow(-1);
            
            let newCell = newRow.insertCell(-1);
            let newText = document.createTextNode(channels[i].channel);
            newCell.appendChild(newText);
            
            newCell = newRow.insertCell(-1);
            var aTag = document.createElement("a");
            aTag.text = !channels[i].is_dne ? 'Override/Enable' : 'Disable';
            aTag.setAttribute("onclick", 
                !channels[i].is_dne 
                    ? 'override(' + channels[i].id + ', ' + type.value + ')'
                    : 'overrideDisable(' + channels[i].id + ', ' + type.value + ')');
            aTag.classList.add("btn", "btn-primary", "btn-sm", 'text-white');
            newCell.appendChild(aTag);
        } 
    }

    function removeRows() {
        let tableRef = document.getElementById('channels_tb_body');
        while(tableRef.rows.length) {
            tableRef.deleteRow(0);
        }
    }

    function override(channelId, clientId) {
        
        var form = new FormData();
        form.append('channel_id', channelId ? channelId : '');
        form.append('client_id', clientId ? clientId : '');

        $.ajax({
            type: "POST",
            url: '/enable_dne',
            data: form,
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
    }

    function overrideDisable(channelId, clientId) {
        
        var form = new FormData();
        form.append('channel_id', channelId ? channelId : '');
        form.append('client_id', clientId ? clientId : '');

        $.ajax({
            type: "POST",
            url: '/disable_dne',
            data: form,
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
    }
</script><?php /**PATH C:\wamp64\www\cranium_new\resources\views/sku_exclusion/client_config.blade.php ENDPATH**/ ?>