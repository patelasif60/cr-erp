
<?php $__env->startSection('page-css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/datatables.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
    <div class="breadcrumb">
        <h1>Clients</h1>
        <!-- <ul>
            <li><a href="">UI Kits</a></li>
            <li>Datatables</li>
        </ul> -->
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header text-right bg-transparent">
                    <?php if(ReadWriteAccess('AddNewClient')): ?>
                    <a href="<?php echo e(route('clients.create')); ?>" class="btn btn-primary btn-md m-1"><i class="i-Add-User text-white mr-2"></i> New Client</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="all_clients" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Client Company Name</th>
                                    <th>Business Relationship</th>
                                    <th>Account Manager</th>
                                    <th>Sales manager</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($result): ?>
                                    <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($row->company_name); ?></td>
                                            <td><?php echo e($row->business_relationship); ?></td>
                                            <td><?php echo e($row->account_manager); ?></td>
                                            <td><?php echo e($row->sales_manager); ?></td>
                                            <?php if($row->is_enable == 1): ?>
                                                <td>Active</td>
                                            <?php elseif($row->is_enable == 2): ?>                                
                                                <td>On Hold</td>
                                            <?php elseif($row->is_enable == 3): ?>                                
                                                <td>Discontinued</td>
                                            <?php endif; ?>                                            
                                            <td>
                                                <?php if(ReadWriteAccess('EditClient')): ?>
                                                <a href="<?php echo e(route('clients.edit',$row->id)); ?>" class="btn btn-warning"  data-toggle="tooltip" data-placement="top" title="Edit">
                                                    <i class="nav-icon i-Pen-2 "></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php if(ReadWriteAccess('DeleteClient')): ?>
                                                <form class="d-inline" action="<?php echo e(route('clients.destroy',$row->id)); ?>" method="POST">
                                                <?php echo e(method_field('DELETE')); ?>

                                                <?php echo e(csrf_field()); ?>

                                                <button type="submit" class="btn btn-danger mr-1" value="delete" onClick="return confirm('Are You Sure You Want To Delete This?')">
                                                    <i class="nav-icon i-Close-Window "></i>
                                                </button>
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
            $('#all_clients').DataTable();
       });
   </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\cranium_new\resources\views/clients/index.blade.php ENDPATH**/ ?>