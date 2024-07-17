
<?php $__env->startSection('page-css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/datatables.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
    <div class="breadcrumb">
        <h1>Suppliers</h1>
        <!-- <ul>
            <li><a href="">UI Kits</a></li>
            <li>Datatables</li>
        </ul> -->
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                 <?php if(ReadWriteAccess('AddNewSupplier')): ?>
                <div class="card-header text-right bg-transparent">
                    <a href="<?php echo e(route('suppliers.create')); ?>" class="btn btn-primary btn-md m-1"><i class="i-Add-User text-white mr-2"></i> New Supplier</a>
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="all_suppliers" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($result): ?>
                                    <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($row->name); ?></td>
                                            <td><?php echo e($row->email); ?></td>
                                            <td><?php echo e($row->phone); ?></td>
                                            <td><?php echo e($row->address); ?></td>
                                            <td><?php echo e($row->status); ?></td>
                                            <td>
                                                <?php if(ReadWriteAccess('EditSupplier')): ?>
                                                <a href="<?php echo e(route('suppliers.edit',$row->id)); ?>" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Edit">
                                                    <i class="nav-icon i-Pen-2 "></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php if(ReadWriteAccess('DeleteSupplier')): ?>
                                                <form class="d-inline" action="<?php echo e(route('suppliers.destroy',$row->id)); ?>" method="POST">
                                                <?php echo e(method_field('DELETE')); ?>

                                                <?php echo e(csrf_field()); ?>

                                                <button type="submit" class="btn btn-danger mr-1" value="delete" onClick="return confirm('Are You Sure You Want To Delete This?')"><i class="nav-icon i-Close-Window "></i></button>
                                                <?php endif; ?>
                                            </form>
                                               
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end of col -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
    <script src="<?php echo e(asset('assets/js/vendor/datatables.min.js')); ?>"></script>
    <script>
       $(document).ready(function () {
            $('#all_suppliers').DataTable();
       });
   </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\cranium_new\resources\views/suppliers/index.blade.php ENDPATH**/ ?>