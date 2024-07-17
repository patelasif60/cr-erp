

<?php $__env->startSection('page-css'); ?>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/datatables.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/custom/css/custom.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/sweetalert2.min.css')); ?>">
<link rel="stylesheet" href="https://phpcoder.tech/multiselect/css/jquery.multiselect.css">
<style>
    .ms-options > ul{
        list-style-type: none !important;
    }
    .ms-options-wrap > .ms-options{
        position: relative;
    }
    .table-responsive .dropdown-menu{
        /* position: relative; */
        min-width:300px;
        z-index: 950 !important;
        padding:10px;
    }
    .filter-input-text{
        width:200%;
    }
    .dropdown-menu.show1 {
        display: block;
    }


</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<input type="hidden" value=<?php echo e($row_id); ?> name="summery_id" id ="summery_id">
<div class="breadcrumb">
    <div class="form-group col-12">
        <h3>Cycle Detail List</h3>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card js-location-approved">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-10">
                        <h3 class="w-50 float-left card-title m-0">Cycle Detail List</h3>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="datatable2" class="table table-bordered text-center dataTable_filter">
                    <thead>
                        <tr>
                            <th scope="col">Location</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">UPC</th>
                            <th scope="col">Current Quantity</th>
                            <th scope="col">Counted Quantity</th>
                            <th scope="col">Exp/Lot NUmber</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>           
        </div>       
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
<script src="<?php echo e(asset('assets/js/vendor/datatables.min.js')); ?>"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>
    <script src="<?php echo e(asset('assets/js/vendor/sweetalert2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/sweetalert.script.js')); ?>"></script>
    <script src="https://phpcoder.tech/multiselect/js/jquery.multiselect.js"></script>
<script>
GetWarehouseApprovedProducts()
    function GetWarehouseApprovedProducts(){
        summery_id = $("#summery_id").val();
         var csrfToken = $('meta[name="csrf-token"]').attr('content');
        table1 = $('#datatable2').DataTable({
            // dom:"Bfrtip",
            paging:   true,
            destroy: true,
            responsive: false,
            processing: true,
            serverSide: true,
            autoWidth: false,
            colReorder: true,
            searching:true,
            ajax:{
                    url: '<?php echo e(route('cyclecomplatelist')); ?>',
                    method:'POST',
                    data: {
                        cc_sum_id: summery_id,
                        _token: csrfToken,
                    },
                },
            lengthMenu: [[25,50, 100, 500], [25,50, 100, 500]],
            pageLength: 25,
            columns: [
                {data: 'address', name: 'Location', defaultContent:'-'},
                {data: 'product_listing_name', name: 'Product Name', defaultContent:'-'},
                {data: 'upc', name: 'UPC', defaultContent:'-'},
                {data: 'total_on_hand', name: 'Current Quantity', defaultContent:'-'},
                {data: 'total_counted', name: 'Counted Quantity', defaultContent:'-'},
                {data: 'upc', name: 'Exp/Lot NUmber', defaultContent:'-'},
            ],
             columnDefs: [
                {
                    orderable: true,
                    targets: 2
                },
            ],
            order: [[2, 'asc']],
            fnInitComplete: function (oSettings, json) {
                $("#preloader").hide();
            }
        });
    };

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\cranium_new\resources\views/WarehouseManagment/cyclecomplatelist.blade.php ENDPATH**/ ?>