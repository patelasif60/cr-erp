
<style>
  
form .form-group {
    margin-right: -5px;
    display: inline-block;
}

</style>

<?php $__env->startSection('main-content'); ?>
    <div class="breadcrumb">
        <h1>Restock Product SettingsComponents</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>
    <div class="col-md-12 mt-4">
        <form method="POST" action="#" enctype="" id="edit_restock_product_setting_form">
            <?php echo csrf_field(); ?>
            <div class="card">
                <div class="card-body ">
                    <div class="row col-lg-12">
                        <h4 class="col-md-12">Higher</h4>
                        <div class="form-group col-md-6">
                            <label for="inputEmail4" class="ul-form__label" data-toggle="tooltip"  title="">Min </label>
                            <input type="number" class="form-control" name="high_min" value="<?php echo e($settings->high_min); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputEmail4" class="ul-form__label" data-toggle="tooltip"  title="">Max </label>
                            <input type="number" class="form-control" name="high_max" value="<?php echo e($settings->high_max); ?>">
                        </div>
                        
                    </div>
                    <div class="row col-lg-12 mt-2">
                        <h4 class="col-md-12">Medium</h4>
                        <div class="form-group col-md-6">
                            <label for="inputEmail4" class="ul-form__label" data-toggle="tooltip"  title="">Min </label>
                            <input type="number" class="form-control" name="mid_min" value="<?php echo e($settings->mid_min); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputEmail4" class="ul-form__label" data-toggle="tooltip"  title="">Max </label>
                            <input type="number" class="form-control" name="mid_max" value="<?php echo e($settings->mid_max); ?>">
                        </div>
                        
                    </div>
                    <div class="row col-lg-12 mt-2">
                        <h4 class="col-md-12">Lower</h4>
                        <div class="form-group col-md-6">
                            <label for="inputEmail4" class="ul-form__label" data-toggle="tooltip"  title="">Min </label>
                            <input type="number" class="form-control" name="low_min" value="<?php echo e($settings->low_min); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputEmail4" class="ul-form__label" data-toggle="tooltip"  title="">Max </label>
                            <input type="number" class="form-control" name="low_max" value="<?php echo e($settings->low_max); ?>">
                        </div>
                    </div>			
                </div>
                <div class="card-footer">
                    <div class="mc-footer">
                        <div class="row">
                            <div class="col-lg-12 text-left">
                                <button type="button" id="UpdateRequest" class="btn  btn-primary m-1 submit">Save</button>
                                <button type="cancel" class="btn btn-outline-secondary m-1">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-js'); ?>

<script src="<?php echo e(asset('assets/js/validation/jquery.validate.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/validation/additional-methods.min.js')); ?>"></script>
<script>
$("#UpdateRequest").click(function(e){
        $(".submit").attr("disabled", true);
        var form_cust = $('#edit_restock_product_setting_form')[0]; 
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '<?php echo e(route('setting.getrestockproductsettingstore')); ?>',
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
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\cranium_new\resources\views/cranium/settings/index.blade.php ENDPATH**/ ?>