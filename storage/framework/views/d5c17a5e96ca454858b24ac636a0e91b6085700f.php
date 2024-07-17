
<?php $__env->startSection('page-css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/datatables.min.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main-content'); ?>
    <script src="<?php echo e(asset('assets/js/vendor/datatables.min.js')); ?>"></script>
    <div class="breadcrumb">
        <h1>Order Management and Routing</h1>
    </div>    
    <div class="separator-breadcrumb border-top"></div>
    <div class="col-md-12 mt-4">
        <ul class="nav nav-tabs nav-justified">
            <li class="nav-item">
                <a class="nav-link active" 
                    href="#tab_exclusions" 
                    id="sku_exclusions" 
                    role="tab" 
                    aria-controls="sku_exclusions" 
                    area-selected="true" data-toggle="tab">
                    <b>Excluded SKUs</b>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" 
                    href="#tab_client_config" 
                    id="client_config" 
                    role="tab" 
                    aria-controls="client_config" 
                    area-selected="false" data-toggle="tab">
                    <b>Client Configuration</b>
                </a>
            </li>								
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab_exclusions" role="tabpanel" area-labelledby="sku_exclusions">
                <?php echo $__env->make('sku_exclusion.excluded_sku', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div class="tab-pane fade show" id="tab_client_config" role="tabpanel" area-labelledby="client_config">
                <?php echo $__env->make('sku_exclusion.client_config', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"></div>
    <script>
        function getModal(url){
            $.ajax({
                type:'GET',
                url:url,
                success:function(response){
                    $("#exampleModal").html('');
                    $('#exampleModal').html(response);
                    $('#exampleModal').modal('show');
                }
            })
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\cranium_new\resources\views/sku_exclusion/index.blade.php ENDPATH**/ ?>