<div class="modal-dialog">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Update Warehouse</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        <form  method="POST" action="javascript:void(0)" id="update_wh_form" >
            <input type="hidden" name="sub_order_number" value="<?php echo e($sub_order_number); ?>">
            <?php echo csrf_field(); ?>
            <div class="modal-body">
                <label for="status_id">Warehouse</label>
                <select id="wh_assigned_so" class="form-control select2" name="wh_assigned_so">
                    <?php if(!isset($wh_assigned) && count($wh_assigned) <= 0): ?>
                        <option value=''>No Warehouse Found</option>
                    <?php else: ?>
                        <option value=''>Select</option>
                        <?php $__currentLoopData = $wh_assigned; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($wh); ?>"><?php echo e($wh); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </select>
            </div>  
                
            <div class="modal-footer"> 
                <button type="submit" class="btn btn-primary submit">Update</button> 
                <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
            </div>
        </form>
    </div>
</div>

<script>
$("#update_wh_form").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        var form_cust = $('#update_wh_form')[0]; 
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '<?php echo e(route('orders.UpdateOrderDetailWh')); ?>',
            data: form1, 
            processData: false,
            contentType: false,
            success: function( response ) {
                if(response.error == 0){
                    toastr.success(response.msg);
                    setTimeout(function(){
                        location.reload();
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
</script><?php /**PATH C:\wamp64\www\cranium_new\resources\views/orders/update_sub_order_wh.blade.php ENDPATH**/ ?>