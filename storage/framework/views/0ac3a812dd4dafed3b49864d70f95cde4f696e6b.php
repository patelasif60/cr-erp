     
<div class="modal-dialog">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Add Product</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        <form  method="POST" action="javascript:void(0)" id="add_order_item_form" >
            <input type="hidden" value=<?php echo e($request->type); ?> name="frm_type" />
            <input type="hidden" value=<?php echo e($request->re_sub_order); ?>  name= "re_sub_order" />
            <?php echo csrf_field(); ?>
            <div class="modal-body">
                <div class="form-group">
                    <input type="hidden" name="order_number" value="<?php echo e($order_number); ?>">
                    <label>Product</label>
                    <select class="form-control select2" id="ETIN" name="ETIN">
                        <option value="">Select Product</option>
                        <?php if($products): ?>
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($row['ETIN']); ?>" data-cost="<?php echo e($row['cost']); ?>"><?php echo e($row['product_name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Qty</label>
                    <input type="number" class="form-control" name="quantity_ordered" id="quantity_ordered">
                </div>
                <div class="form-group" id='warehouse_count'>
                </div>
            </div> 
                
            <div class="modal-footer"> 
                <button type="submit" class="btn btn-primary submit" id="add_manufacturer">Save</button> 
                <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
            </div>
        </form>
    </div>
</div>


<script>

$("#add_order_item_form").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        var form_cust = $('#add_order_item_form')[0]; 
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '<?php echo e(route('orders.store_product')); ?>',
            data: form1, 
            processData: false,
            contentType: false,
            success: function( response ) {
                if(response.error === false){
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

$('.select2').select2({
    dropdownParent: $('#MyModalOrderItm')
});

$("#ETIN").on('change',function(e){
    // let price = $(this).find(':selected').data('cost');
    // $("#channel_unit_price").val(price);
    let etin = $(this).find(':selected').val()
    if (etin && etin !== '') {
        GetWarehouseCount(etin);
    } else {
        parent = document.getElementById('warehouse_count');
        while (parent.firstChild) {
            parent.firstChild.remove()
        }
    }
})

function GetWarehouseCount(etin){
    $.ajax({
        url: '/summery_orders/product_wh_count/' + etin,
        method:'GET',
        success:function(res){
            div_el = document.getElementById('warehouse_count')
            while (div_el.firstChild) {
                div_el.firstChild.remove()
            }       
            res.data.forEach(function (obj) {
                ch = document.createElement('label')
                ch.innerText = obj.name + ': ' + obj.count + ' -- Available Qty (' + obj.count_1+')';
                div_el.appendChild(ch);
                div_el.appendChild(document.createElement('br'))
            })
        }
    });
}

</script><?php /**PATH C:\wamp64\www\cranium_new\resources\views/orders/add_product.blade.php ENDPATH**/ ?>