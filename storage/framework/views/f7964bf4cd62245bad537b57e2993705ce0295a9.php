
<?php $__env->startSection('page-css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/datatables.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/sweetalert2.min.css')); ?>">
    <style>
        .flatpickr-wrapper{
            width:100%;
        }
        .form-file-control {
            display: block;
            width: 100%;
            border: 1px solid #ced4da;
            padding: 0.375rem 0.75rem;
            font-size: .813rem;
            line-height: 1.5;
            color: #665c70;
            background-color: #fff;
            background-clip: padding-box;
            border-radius: 0.25rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .action{
            padding-top: 17px;
            display: inline-flex;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
    <?php $required_span = '<span class="text-danger">*</span>' ?>
    <div class="breadcrumb">
        <h1>Clients</h1>
        <ul>
            <li><a href="<?php echo e(route('clients.index')); ?>">Clients</a></li>
            <li>Edit</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Edit Client</h6>
                </div>
                    <div class="card-body">
                        <form action="#" id="new_client">
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="id" value="<?php echo e($row->id); ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-row ">
                                        <div class="form-group col-md-12">
                                            <label for="company_name" class="ul-form__label">Client Company Name:<?php echo $required_span; ?></label>
                                            <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Enter Client Company Name" value="<?php echo e($row->company_name); ?>">
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label for="status" class="ul-form__label">Status:</label>
                                            <select class="form-control" id="status" name="status" >
                                                <option value="Active" <?php if($row->status == 'Active'): ?> selected="selected" <?php endif; ?>>Active</option>
                                                <option value="Inactive" <?php if($row->status == 'Inactive'): ?> selected="selected" <?php endif; ?>>Inactive</option>
                                                <option value="Secondary" <?php if($row->status == 'Secondary'): ?> selected="selected" <?php endif; ?>>Secondary</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <?php if(auth()->user()->role == 1): ?>
                                                <label for="business_relationship" class="ul-form__label">Business Relationship:</label>
                                                <select class="form-control select2" id="business_relationship" name="business_relationship" >
                                                    <option value="Fulfillment" <?php if($row->business_relationship == 'Fulfillment'): ?> selected="selected" <?php endif; ?>>Fulfillment</option>
                                                    <option value="Wholesale" <?php if($row->business_relationship == 'Wholesale'): ?> selected="selected" <?php endif; ?>>Wholesale</option>
                                                </select>
                                            <?php else: ?>
                                                <label for="business_relationship" class="ul-form__label">Business Relationship: <i>(To change type contact Admin)</i></label>
                                                <select class="form-control" id="business_relationship" name="business_relationship" disabled >
                                                    <option value="Fulfillment" <?php if($row->business_relationship == 'Fulfillment'): ?> selected="selected" <?php endif; ?>>Fulfillment</option>
                                                    <option value="Wholesale" <?php if($row->business_relationship == 'Wholesale'): ?> selected="selected" <?php endif; ?>>Wholesale</option>
                                                </select>
                                            <?php endif; ?>                                            
                                        </div>
                                        
                                        <div class="form-group col-md-12">
                                            <label for="account_manager" class="ul-form__label">Account Manager:</label>
                                            <select class="form-control select2" id="account_manager" name="account_manager">
                                                <option value="">--Select--</option>
                                                <?php if($managers): ?>
                                                    <?php $__currentLoopData = $managers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($id); ?>" <?php if($id == $row->account_manager): ?> selected <?php endif; ?>><?php echo e($val); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="sales_manager" class="ul-form__label">Sales Manager:</label>
                                            <select class="form-control select2" id="sales_manager" name="sales_manager">
                                                <option value="">--Select--</option>
                                                <?php if($managers): ?>
                                                    <?php $__currentLoopData = $managers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($id); ?>" <?php if($id == $row->sales_manager): ?> selected <?php endif; ?>><?php echo e($val); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="active_status" class="ul-form__label">Active Status:</label>
                                            <select class="form-control select2" id="active_status" name="active_status" required>
                                                <option value="1" <?php if($row->is_enable == 1): ?> selected <?php endif; ?>>Active</option>                                            
                                                <option value="2" <?php if($row->is_enable == 2): ?> selected <?php endif; ?>>On Hold</option>                                            
                                                <option value="3" <?php if($row->is_enable == 3): ?> selected <?php endif; ?>>Disconnected</option>                                            
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    
                                    <div class="form-group col-md-12">
                                        <label for="time_zone_id" class="ul-form__label">Time Zone:</label>
                                        <select class="form-control select2" id="time_zone_id" name="time_zone_id">
                                            <option value="">--Select--</option>
                                            <?php if($time_zones): ?>
                                            <?php $__currentLoopData = $time_zones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($id); ?>"  <?php if($row->time_zone_id == $id): ?> selected <?php endif; ?>><?php echo e($val); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="time_zone_id" class="ul-form__label">Address:</label>
                                        <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address" value="<?php echo e($row->address); ?>">
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="time_zone_id" class="ul-form__label">Address 2:</label>
                                        <input type="text" class="form-control" id="address2" name="address2" placeholder="Enter Address 2" value="<?php echo e($row->address2); ?>">
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="time_zone_id" class="ul-form__label">Zip:</label>
                                        <input type="text" class="form-control" id="zip" name="zip" placeholder="Enter Zip" value="<?php echo e($row->zip); ?>">
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="time_zone_id" class="ul-form__label">City:</label>
                                        <input type="text" class="form-control" id="city" name="city" placeholder="Enter City" value="<?php echo e($row->city); ?>">
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="time_zone_id" class="ul-form__label">State:</label>
                                        <input type="text" class="form-control" id="state" name="state" placeholder="Enter state" value="<?php echo e($row->state); ?>">
                                    </div>
                                    
                                    <?php if(count($priceGroup)>0): ?>
                                    <div class="form-group col-md-12">
                                        <label for="sales_manager" class="ul-form__label">Price Group:</label>
                                        <select class="form-control select2" onchange="pricegroupChange()" id="price_group" name="price_group">
                                            <?php $__currentLoopData = $priceGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($val->id); ?>"><?php echo e($val->group_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="row" style="margin-top:30px;">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn  btn-primary m-1">Submit</button>
                                    <a href="<?php echo e(route('clients.index')); ?>" class="btn btn-outline-secondary m-1">Cancel</a>
                                </div>
                            </div>
                        </form>
                        <!-- tab start -->

                        <div class="col-md-12 mt-4">
                            <ul class="nav nav-tabs nav-justified">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#tab_product_management" id="product_management_tab" role="tab" aria-controls="product_management_tab" area-selected="true" data-toggle="tab">Product Management</a>
                                </li>
                                <?php if(auth()->user()->client == ''): ?>
                                    <li class="nav-item">
                                        <a class="nav-link " href="#tab_client_config" id="client_config_tab" role="tab" aria-controls="client_config_tab" area-selected="false" data-toggle="tab">Client
                                            Configuration</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="#tab_account_management" id="account_management_tab" role="tab" aria-controls="account_management_tab" area-selected="false" data-toggle="tab">Account Management</a>
                                    </li>
                                <?php endif; ?>
                                <?php if(isset($row->business_relationship) && strtolower($row->business_relationship) === 'fulfillment'): ?>
                                    <li class="nav-item">
                                        <a class="nav-link " href="#tab_warehouse_orders" id="warehouse_orders_tab" role="tab" aria-controls="warehouse_orders_tab" area-selected="false" data-toggle="tab">Warehouse Orders</a>
                                    </li>
                                <?php endif; ?>
                                <?php if(auth()->user()->client == ''): ?>
                                    <li class="nav-item">
                                        <a class="nav-link " href="#tab_billing" id="billing_tab" role="tab" aria-controls="billing_tab" area-selected="false" data-toggle="tab">Billing</a>
                                    </li>
                                <?php endif; ?>
                                <li class="nav-item">
                                    <a class="nav-link " href="#tab_contacts" id="contacts_tab" role="tab" aria-controls="contacts_tab" area-selected="false" data-toggle="tab">Contacts</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link " href="#tab_orders" id="orders_tab" role="tab" aria-controls="orders_tab" area-selected="false" data-toggle="tab">Orders</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link " href="#tab_documents" id="documents_tab" role="tab" aria-controls="documents_tab" area-selected="false" data-toggle="tab">Documents</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link " href="#tab_reports" id="reports_tab" role="tab" aria-controls="reports_tab" area-selected="false" data-toggle="tab">Reports</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link " href="#tab_notification_settings" id="notification_settings_tab" role="tab" aria-controls="notification_settings_tab" area-selected="false" data-toggle="tab">Notification Settings</a>
                                </li>                                
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tab_product_management" role="tabpanel" area-labelledby="product_management_tab">
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="card o-hidden mb-4">
                                                <div class="card-header">
                                                    <h3 class="w-25 float-left card-title m-0">Product Listings</h3>
                                                    <div class="separator-breadcrumb" style="<?php if($row->business_relationship == 'Fulfillment'): ?> display:block <?php else: ?> display:none <?php endif; ?>">
                                                        <?php if(ReadWriteAccess('AddNewParentProduct')): ?>
                                                            <a href="<?php echo e(route('addnewmasterproductview')); ?>?client_id=<?php echo e($row->id); ?>" class="btn btn-primary btn-icon m-1" style=" float: right;" target="_blank">
                                                                <img src="<?php echo e(asset('assets/images/addnew.png')); ?>" style="width: 15px; cursor: pointer;">&nbsp; Add New Parent Product
                                                            </a>
                                                        <?php endif; ?>
                                                            <a href="<?php echo e(route('kits.create')); ?>?client_id=<?php echo e($row->id); ?>" class="btn btn-primary btn-icon m-1" style=" float: right;"  target="_blank">
                                                                <img src="<?php echo e(asset('assets/images/addnew.png')); ?>" style="width: 15px; cursor: pointer;">&nbsp; Add New Kit
                                                            </a>
                                                            <a href="javascript:void(0);" onClick="getModal('<?php echo e(route('upload_bulk_product',$row->id)); ?>')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                                <img src="<?php echo e(asset('assets/images/addnew.png')); ?>" style="width: 15px; cursor: pointer;">&nbsp; Bulk Upload
                                                            </a>
                                                            <a href="javascript:void(0);" onClick="getModal('<?php echo e(route('map_client_product_file',$row->id)); ?>')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                                <img src="<?php echo e(asset('assets/images/addnew.png')); ?>" style="width: 15px; cursor: pointer;">&nbsp; Map File
                                                            </a>
                                                    </div>
                                                    <div class="dropdown dropleft text-right w-50 float-right">
                                                    </div>

                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-lg-8"></div>
                                                        
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table id="datatable" class="table table-bordered text-center dataTable_filter">

                                                            <thead>
                                                                <tr>

                                                                    <th scope="col">ETIN</th>
                                                                    <th scope="col">Product Listing Name</th>
                                                                    <th scope="col">Group Price</th>
                                                                    <th scope="col">Channels</th>
                                                                    <th scope="col">Inventory</th>
                                                                    <th scope="col">UPC</th>
                                                                    <th scope="col">Status</th>
                                                                    <th scope="col">Action</th>
                                                                </tr>
                                                                <!-- <tr>
                                                                    <th scope="col">
                                                                        <select name="etin_filter[]" id="etin_filter" class="form-control select2" multiple>
                                                                            <option value="">Select</option>
                                                                            <?php $__currentLoopData = $getet; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <option value="<?php echo e($id); ?>"><?php echo e($val); ?></option>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                        </select>
                                                                    </th>
                                                                    <th scope="col">
                                                                        <select  id="listing_name_filter" name="listing_name_filter[]" class="form-control select2" multiple>
                                                                            <option value=''>Select</option>
                                                                            <?php $__currentLoopData = $listing_name; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ln): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <option value="<?php echo e($ln); ?>"><?php echo e($ln); ?></option>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                        </select>
                                                                    </th>
                                                                    <th scope="col"></th>
                                                                    <th scope="col"></th>
                                                                    <th scope="col">
                                                                        <select id="upc_filter" name="upc_filter[]" class="form-control select2" multiple>
                                                                            <option value=''>Select</option>
                                                                            <?php $__currentLoopData = $upcs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $upc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <option value="<?php echo e($upc); ?>"><?php echo e($upc); ?></option>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                        </select>
                                                                    </th>
                                                                    <th scope="col">
                                                                        <select id="status_filter" name="status_filter[]" class="form-control select2" multiple>
                                                                            <option value=''>Select</option>
                                                                            <option value="Active">Active</option>
                                                                            <option value="Deplete">Deplete</option>
                                                                            <option value="Discontinued">Discontinued</option>
                                                                        </select>
                                                                    </th>
                                                                    <th scope="col"></th>

                                                                </tr> -->
                                                            </thead>

                                                            <tbody>
                                                            <!-- DATATABLE Here -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab_client_config" role="tabpanel" area-labelledby="client_config_tab">
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="card o-hidden mb-4">
                                                <div class="card-header">
                                                    <h3 class="w-50 float-left card-title m-0">Channels</h3>
                                                    <div class="separator-breadcrumb">
                                                        <a href="javascript:void(0);" onclick="getModal('<?php echo e(route('clients.createChannel',$row->id)); ?>')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                            <img src="<?php echo e(asset('assets/images/addnew.png')); ?>" style="width: 15px; cursor: pointer;">&nbsp; New Channel
                                                        </a>
                                                    </div>
                                                    <div class="dropdown dropleft text-right w-50 float-right"></div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="channels" class="table table-bordered text-center channels_filter">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" id="idclass">#</th>
                                                                    <th scope="col">Channel</th>
                                                                    <th scope="col">Store URL</th>
                                                                    <th scope="col">Admin URL</th>
                                                                    <th scope="col">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            <!-- DATATABLE Here -->
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <div class="row mt-4">
                                                        <div class="col-lg-12">
                                                            <table class="table table-borderless">
                                                                <form action="javascript:void(0)" id="updateClientManagementDetails" method="POST">
                                                                <tr>
                                                                    <td>Product Consignments</td>
                                                                    <td>
                                                                        <select name="product_consignment" id="product_consignment" class="form-control">
                                                                            <option value="No" <?php if($row->product_consignment == "No"): ?> selected <?php endif; ?>>No</option>
                                                                            <option value="Yes" <?php if($row->product_consignment == "Yes"): ?> selected <?php endif; ?>>Yes</option>
                                                                            
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        
                                                                    </td>
                                                                </tr>    
                                                                <tr>
                                                                    <th class="w-25">Client Management</th>
                                                                    <th class="w-25">Select</th>
                                                                    <th>Notes</th>
                                                                </tr>
                                                                <tr>
                                                                    <td>Inventory Management</td>
                                                                    <td>
                                                                        <select name="inventory_management" id="inventory_management" class="form-control">
                                                                            <option value="client" <?php if($row->inventory_manager == "client"): ?> selected <?php endif; ?>>Client</option>
                                                                            <option value="e-tailer" <?php if($row->inventory_manager == "e-tailer"): ?> selected <?php endif; ?>>e-tailer</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="inventory_management_notes" id="inventory_management_notes" class="form-control" value="<?php echo e($row->inventory_management_notes); ?>">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Purchasing Management</td>
                                                                    <td>
                                                                        <select name="purchasing_management" id="purchasing_management" class="form-control">
                                                                            <option value="client" <?php if($row->purchasing_management == "client"): ?> selected <?php endif; ?>>Client</option>
                                                                            <option value="e-tailer" <?php if($row->purchasing_management == "e-tailer"): ?> selected <?php endif; ?>>e-tailer</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="purchasing_management_notes" id="purchasing_management_notes" class="form-control"  value="<?php echo e($row->purchasing_management_notes); ?>">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Order Management</td>
                                                                    <td>
                                                                        <select name="order_management" id="order_management" class="form-control">
                                                                            <option value="client" <?php if($row->order_management == "client"): ?> selected <?php endif; ?>>Client</option>
                                                                            <option value="e-tailer" <?php if($row->order_management == "e-tailer"): ?> selected <?php endif; ?>>e-tailer</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="order_management_notes" id="order_management_notes" class="form-control" value=" <?php echo e($row->order_management_notes); ?>">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Custom packaging</td>
                                                                    <td>
                                                                        <select name="custom_packaging" id="custom_packaging" class="form-control" >
                                                                            <option value="client" <?php if($row->custom_packaging == "client"): ?> selected <?php endif; ?>>Client</option>
                                                                            <option value="e-tailer" <?php if($row->custom_packaging == "e-tailer"): ?> selected <?php endif; ?>>e-tailer</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="custom_packaging_notes" id="custom_packaging_notes" class="form-control" value="<?php echo e($row->custom_packaging_notes); ?>">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Channel Owner</td>
                                                                    <td>
                                                                        <select name="channel_owner" id="channel_owner" class="form-control">
                                                                            <option value="client" <?php if($row->store_owner == "client"): ?> selected <?php endif; ?>>Client</option>
                                                                            <option value="e-tailer" <?php if($row->store_owner == "e-tailer"): ?> selected <?php endif; ?>>e-tailer</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="channel_owner_notes" id="channel_owner_notes" class="form-control" value="<?php echo e($row->channel_owner_notes); ?>">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Price Management</td>
                                                                    <td>
                                                                        <select name="price_management" id="price_management" class="form-control">
                                                                            <option value="client" <?php if($row->price_management == "client"): ?> selected <?php endif; ?>>Client</option>
                                                                            <option value="e-tailer" <?php if($row->price_management == "e-tailer"): ?> selected <?php endif; ?>>e-tailer</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="price_management_notes" id="price_management_notes" class="form-control" value="<?php echo e($row->price_management_notes); ?>">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Consumer Service</td>
                                                                    <td>
                                                                        <select name="customer_service" id="customer_service" class="form-control" onchange="showHideConsumerServices(this.value)">
                                                                            <option value="client" <?php if($row->customer_service == "client"): ?> selected <?php endif; ?>>Client</option>
                                                                            <option value="e-tailer" <?php if($row->customer_service == "e-tailer"): ?> selected <?php endif; ?>>e-tailer</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="customer_service_notes" id="customer_service_notes" class="form-control" value="<?php echo e($row->customer_service_notes); ?>">
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <table class="table table-bordered" id="consumer_services_div">
                                                                        <tr>
                                                                            <th width="10%">Consumer Service</td>
                                                                            <th width="5%">e-tailer</td>
                                                                            <th class="w-25">Notes</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Phone</td>
                                                                            <td class="text-center">
                                                                                <input type="checkbox" name="is_phone_etailer" value="1" <?php if(isset($customerServiceRow->is_phone_etailer) && $customerServiceRow->is_phone_etailer == 1): ?> checked <?php endif; ?>>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" name="phone_etailer_notes" value="<?php if(isset($customerServiceRow->phone_etailer_notes)): ?><?php echo e($customerServiceRow->phone_etailer_notes ?? ''); ?><?php endif; ?>" class="form-control">
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Email</td>
                                                                            <td class="text-center">
                                                                                <input type="checkbox" name="is_email_etailer" value="1" <?php if(isset($customerServiceRow->is_email_etailer) && $customerServiceRow->is_email_etailer == 1): ?> checked <?php endif; ?>>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" name="email_etailer_notes" value="<?php if(isset($customerServiceRow->email_etailer_notes)): ?><?php echo e($customerServiceRow->email_etailer_notes ?? ''); ?><?php endif; ?>" class="form-control">
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Live Chat</td>
                                                                            <td class="text-center">
                                                                                <input type="checkbox" name="is_live_chat_etailer" value="1" <?php if(isset($customerServiceRow->is_live_chat_etailer) && $customerServiceRow->is_live_chat_etailer == 1): ?> checked <?php endif; ?>>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" name="live_chat_etailer_notes" value="<?php if(isset($customerServiceRow->live_chat_etailer_notes)): ?><?php echo e($customerServiceRow->live_chat_etailer_notes ?? ''); ?><?php endif; ?>" class="form-control">
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Miscellaneous</td>
                                                                            <td class="text-center">
                                                                                <input type="checkbox" name="is_miscellaneous_etailer" value="1" <?php if(isset($customerServiceRow->is_miscellaneous_etailer) && $customerServiceRow->is_miscellaneous_etailer == 1): ?> checked <?php endif; ?>>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" name="miscellaneous_etailer_notes" value="<?php if(isset($customerServiceRow->miscellaneous_etailer_notes)): ?><?php echo e($customerServiceRow->miscellaneous_etailer_notes); ?><?php endif; ?>" class="form-control">
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <button type="submit" class="btn  btn-primary m-1">Submit</button>
                                                                                <a href="<?php echo e(route('clients.index')); ?>" class="btn btn-outline-secondary m-1">Cancel</a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </form>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab_billing" role="tabpanel" area-labelledby="billing_tab">
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="card o-hidden mb-4">
                                                <div class="card-header">
                                                    <h3 class="w-50 float-left card-title m-0">Billing Tasks & Events</h3>
                                                    <div class="separator-breadcrumb">
                                                        <a href="javascript:void(0);" onclick="getEventCreateModal('<?php echo e(route('clients.createBillingEvent',$row->id)); ?>')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                            <img src="<?php echo e(asset('assets/images/addnew.png')); ?>" style="width: 15px; cursor: pointer;">&nbsp; New Event
                                                        </a>
                                                    </div>
                                                    <div class="dropdown dropleft text-right w-50 float-right">
                                                    </div>

                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="billing_events" class="table table-bordered text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" id="idclass">#</th>
                                                                    <th scope="col">Event</th>
                                                                    <th scope="col">Frequency</th>
                                                                    <th scope="col">Day & Time</th>
                                                                    <th scope="col">Details</th>
                                                                    <th scope="col">Owner</th>
                                                                    <th scope="col">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            <!-- DATATABLE Here -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="card o-hidden mb-4">
                                                <div class="card-header">
                                                    <h3 class="w-50 float-left card-title m-0">Billing Notes </h3>
                                                    <div class="separator-breadcrumb">
                                                        <a href="javascript:void(0);" onclick="getNoteCreateModal('<?php echo e(route('clients.createBillingNote',$row->id)); ?>')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                            <img src="<?php echo e(asset('assets/images/addnew.png')); ?>" style="width: 15px; cursor: pointer;">&nbsp; New Note
                                                        </a>
                                                    </div>
                                                    <div class="dropdown dropleft text-right w-50 float-right">
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="billing_notes" class="table table-bordered text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" id="idclass">Type</th>
                                                                    <th scope="col">Detail</th>
                                                                    <th scope="col">Date</th>
                                                                    <th scope="col">Date/Time Submitted</th>
                                                                    <th scope="col">Added By</th>
                                                                    <th scope="col">Invoice Date</th>
                                                                    <th scope="col">Location</th>
                                                                    <th scope="col">Billable</th>
                                                                    <th scope="col">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            <!-- DATATABLE Here -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab_account_management" role="tabpanel" area-labelledby="account_management_tab">
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="card o-hidden mb-4">
                                                <div class="card-header">
                                                    <h3 class="w-50 float-left card-title m-0">Recurring Tasks & Events</h3>
                                                    <div class="separator-breadcrumb">
                                                        <a href="javascript:void(0);" onclick="getEventCreateModal('<?php echo e(route('clients.createEvent',$row->id)); ?>')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                            <img src="<?php echo e(asset('assets/images/addnew.png')); ?>" style="width: 15px; cursor: pointer;">&nbsp; New Event
                                                        </a>
                                                    </div>
                                                    <div class="dropdown dropleft text-right w-50 float-right">
                                                    </div>

                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="events" class="table table-bordered text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" id="idclass">#</th>
                                                                    <th scope="col">Event</th>
                                                                    <th scope="col">Frequency</th>
                                                                    <th scope="col">Day & Time</th>
                                                                    <th scope="col">Details</th>
                                                                    <th scope="col">Owner</th>
                                                                    <th scope="col">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            <!-- DATATABLE Here -->
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="card o-hidden mb-4">
                                                <div class="card-header">
                                                    <h3 class="w-50 float-left card-title m-0">Account Notes </h3>

                                                    <div class="separator-breadcrumb">
                                                            <a href="javascript:void(0);" onclick="getNoteCreateModal('<?php echo e(route('clients.createNote',$row->id)); ?>')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                                <img src="<?php echo e(asset('assets/images/addnew.png')); ?>" style="width: 15px; cursor: pointer;">&nbsp; New Note
                                                            </a>
                                                    </div>
                                                    <div class="dropdown dropleft text-right w-50 float-right">
                                                    </div>

                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="notes" class="table table-bordered text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" id="idclass">#</th>
                                                                    <th scope="col">Event</th>
                                                                    <th scope="col">Detail</th>
                                                                    <th scope="col">Date & Time</th>
                                                                    <th scope="col">Added By</th>
                                                                    <th scope="col">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            <!-- DATATABLE Here -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="card o-hidden mb-4">
                                                <div class="card-header">
                                                    <h3 class="w-50 float-left card-title m-0">Warehouse & Fulfillment</h3>
                                                    <div class="separator-breadcrumb">
                                                        <a href="javascript:void(0);" onclick="getModal('<?php echo e(route('clients.createWarehouseAndFulfillment',$row->id)); ?>')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                            <img src="<?php echo e(asset('assets/images/addnew.png')); ?>" style="width: 15px; cursor: pointer;">&nbsp; Create New
                                                        </a>
                                                    </div>
                                                    <div class="dropdown dropleft text-right w-50 float-right"></div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="warehouses" class="table table-bordered text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" id="idclass">#</th>
                                                                    <th scope="col">Event</th>
                                                                    <th scope="col">Frequency</th>
                                                                    <th scope="col">Day & Time</th>
                                                                    <th scope="col">Details</th>
                                                                    <th scope="col">Owner</th>
                                                                    <th scope="col">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            <!-- DATATABLE Here -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab_contacts" role="tabpanel" area-labelledby="contacts_tab">
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="card o-hidden mb-4">
                                                <div class="card-header">
                                                    <h3 class="w-50 float-left card-title m-0">Contacts</h3>

                                                    <div class="separator-breadcrumb">
                                                            <a href="javascript:void(0);" onclick="getModal('<?php echo e(route('clients.createContact',$row->id)); ?>')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                                <img src="<?php echo e(asset('assets/images/addnew.png')); ?>" style="width: 15px; cursor: pointer;">&nbsp; Add New Contact
                                                            </a>
                                                    </div>
                                                    <div class="dropdown dropleft text-right w-50 float-right">
                                                    </div>

                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="contacts" class="table table-bordered text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" id="idclass">#</th>
                                                                    <th scope="col">Name</th>
                                                                    <th scope="col">Title</th>
                                                                    <th scope="col">Email</th>
                                                                    <th scope="col">Office Phone</th>
                                                                    <th scope="col">Cell Phone</th>
                                                                    <th scope="col">Contact Notes</th>
                                                                    <th scope="col">Cranium</th>
                                                                    <th scope="col">Primary</th>
                                                                    <th scope="col">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            <!-- DATATABLE Here -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab_orders" role="tabpanel" area-labelledby="orders_tab">
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="card o-hidden mb-4">
                                                <div class="card-header">
                                                    <h3 class="w-40 float-left card-title m-0">Orders</h3>

                                                    <div class="separator-breadcrumb">
                                                        <a href="<?php echo e(route('orders.create',$row->id)); ?>" target="_blank"  class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                            <img src="<?php echo e(asset('assets/images/addnew.png')); ?>" style="width: 15px; cursor: pointer;">&nbsp; Add New Manual Order
                                                        </a>
                                                        <a href="<?php echo e(route('orders.bulk_upload_order', $row->id)); ?>" target="_blank"  class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                            <img src="<?php echo e(asset('assets/images/addnew.png')); ?>" style="width: 15px; cursor: pointer;">&nbsp; Add Bulk Order
                                                        </a>
                                                        <a href="/templates/Bulk_Order_Template.CSV" target="_blank"  class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                            <i class="nav-icon i-Down"></i>&nbsp; Bulk Order Template
                                                        </a>
                                                    </div>
                                                    <div class="dropdown dropleft text-right w-50 float-right">
                                                    </div>

                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="datatableOrder" class="table table-bordered text-center " style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">Order Date</th>
                                                                    <th scope="col">e-tailer Order Number</th>
                                                                    <th scope="col">Client</th>
                                                                    <th scope="col">Order Source</th>
                                                                    <th scope="col">Destination</th>
                                                                    <th scope="col">Channel Delivery Date</th>
                                                                    <th scope="col">Ship By</th>
                                                                    <th scope="col">Order Status</th>
                                                                    <th scope="col">Ship Dates</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab_documents" role="tabpanel" area-labelledby="documents_tab">
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="card o-hidden mb-4">
                                                <div class="card-header">
                                                    <h3 class="w-50 float-left card-title m-0">Documents</h3>

                                                    <div class="separator-breadcrumb">
                                                            <a href="javascript:void(0);" onclick="getModal('<?php echo e(route('clients.createDocument',$row->id)); ?>')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                                <img src="<?php echo e(asset('assets/images/addnew.png')); ?>" style="width: 15px; cursor: pointer;">&nbsp; Add New Document
                                                            </a>
                                                    </div>
                                                    <div class="dropdown dropleft text-right w-50 float-right">
                                                    </div>

                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="documents" class="table table-bordered text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" id="idclass">#</th>
                                                                    <th scope="col">Type</th>
                                                                    <th scope="col">Name</th>
                                                                    <th scope="col">Description</th>
                                                                    <th scope="col">Date</th>
                                                                    <th scope="col">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            <!-- DATATABLE Here -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card o-hidden mb-4">
                                                <div class="card-header">
                                                    <h3 class="w-50 float-left card-title m-0">Links</h3>

                                                    <div class="separator-breadcrumb">
                                                            <a href="javascript:void(0);" onclick="getModal('<?php echo e(route('clients.createLink',$row->id)); ?>')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                                                <img src="<?php echo e(asset('assets/images/addnew.png')); ?>" style="width: 15px; cursor: pointer;">&nbsp; Add New
                                                            </a>
                                                    </div>
                                                    <div class="dropdown dropleft text-right w-50 float-right">
                                                    </div>

                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="links" class="table table-bordered text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" id="idclass">#</th>
                                                                    <th scope="col">URL</th>
                                                                    <th scope="col">Name</th>
                                                                    <th scope="col">Description</th>
                                                                    <th scope="col">Date</th>
                                                                    <th scope="col">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            <!-- DATATABLE Here -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab_reports" role="tabpanel" area-labelledby="reports_tab">
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <?php echo $__env->make('clients.reportindex', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab_warehouse_orders" role="tabpanel" area-labelledby="warehouse_orders_tab">
                                    <div class="row">
                                        <table class="table table-border" id="purchase_summary">
                                            <thead>
                                                <tr>
                                                    <th>Warehouse</th>
                                                    <th>Order Number</th>
                                                    <th>Bol Number</th>
                                                    <th>Order Date</th>
                                                    <th>Delivery Date</th>
                                                    <th>Status</th> 
                                                    <th>Actions</th>                                            
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- <?php if($result): ?>
                                                    <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td><?php echo e($res['warehouse']); ?></td>
                                                            <td><?php echo e($res['order']); ?></td>
                                                            <td><?php echo e($res['bol_numbers']); ?></td>
                                                            <td><?php echo e($res['order_date']); ?></td>
                                                            <td><?php echo e($res['delivery_date']); ?></td>
                                                            <td><?php echo e($res['po_status']); ?></td>
                                                            <td>
                                                                <?php if($res['po_status'] && ($res['po_status'] == 'Pending' || $res['po_status'] == 'Submitted')): ?>
                                                                    <a href="<?php echo e(url('/purchase_order/edit/' . $row->id . '/' . $res['id'] . '/client')); ?>" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Edit">
                                                                        <i class="nav-icon i-Pen-2 "></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                                <?php if($res['report_path']): ?>
                                                                    <a href="<?php echo e(url('/' . $res['report_path'])); ?>" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Download Report">
                                                                        <i class="nav-icon i-Down"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                                <?php if($res['po_status'] && $res['po_status'] != 'Pending'): ?>
                                                                    <a href="<?php echo e(url('/purchase_order/editasnbol/' . $row->id . '/' . $res['id'] . '/client')); ?>" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Submit ASN/BOL">
                                                                        <i class="nav-icon">Submit ASN/BOL</i>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?> -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">                                
                                        <div class="col-md-12 text-left">
                                            <a href="<?php echo e(route('purchase_order.create_purchase_order',['id' => $row->id, 'type' => 'client'])); ?>" class="btn btn-primary">New Purchase Order</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab_notification_settings" role="tabpanel" area-labelledby="notification_settings_tab">
                                    <form action="#" id="notification_form">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table class="table table-stripped">
                                                        <tr>
                                                            <th>Notification</th>
                                                            <th>Check</th>
                                                        </tr>
                                                        <tr>
                                                            <td>Notification Type</td>
                                                            <td>
                                                                <select class="form-control select2" id="notification_type" name="notification_type[]" multiple="multiple">
                                                                    <option value="in_app" <?php if(isset($notification->notification_type) &&  in_array('in_app',explode(',',$notification->notification_type))): ?> selected <?php endif; ?>>In app</option>
                                                                    <option value="email" <?php if(isset($notification->notification_type) &&  in_array('email',explode(',',$notification->notification_type))): ?> selected <?php endif; ?>>Email</option>
                                                                </select>
                                                            </td>   
                                                        </tr>
                                                        
                                                        <tr>
                                                            <td>Order Management By Type</td>
                                                            <td>
                                                                <select class="form-control select2" id="order_by_order_type" name="order_by_order_type[]" multiple="multiple">
                                                                    <?php if($ots): ?>
                                                                        <?php $__currentLoopData = $ots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <option value='<?php echo e($ot->id); ?>' <?php if(isset($notification->order_by_order_type) &&  in_array($ot->id,explode(',',$notification->order_by_order_type))): ?> selected <?php endif; ?>><?php echo e($ot->name); ?></option>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php endif; ?>
                                                                </select>
                                                            </td>   
                                                        </tr>
                                                        <tr>
                                                            <td>Order Management By Shipping Type</td>
                                                            <td>
                                                                <select class="form-control select2" id="order_by_shipping_speed" name="order_by_shipping_speed[]" multiple="multiple">
                                                                    <?php if($shipping_service_types): ?>
                                                                        <?php $__currentLoopData = $shipping_service_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row_sst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <option value="<?php echo e($row_sst->id); ?>" <?php if(isset($notification->order_by_shipping_speed) &&  in_array($row_sst->id,explode(',',$notification->order_by_shipping_speed))): ?> selected <?php endif; ?>><?php echo e($row_sst->service_name); ?></option>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php endif; ?>
                                                                    
                                                                </select>
                                                            </td>   
                                                        </tr>
                                                        
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="mc-footer">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <button type="submit" class="btn  btn-primary m-1">Submit</button>
                                                        <a href="<?php echo e(route('users.index')); ?>" class="btn btn-outline-secondary m-1">Cancel</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- tab end -->
                    </div>

            </div>
        </div>
    </div>
    <!-- end of col -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"></div>
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true"></div>
<div class="modal fade" id="createEventModal" tabindex="-1" role="dialog" aria-labelledby="createEventModalLabel" aria-hidden="true"></div>
<div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="editEventModalLabel" aria-hidden="true"></div>
<div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="noteModalLabel" aria-hidden="true"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
    <script src="<?php echo e(asset('assets/js/vendor/datatables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/vendor/sweetalert2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/sweetalert.script.js')); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.21/dataRender/datetime.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>

    <script>
    $( function() {
    });
    $options = $('#report_type').find( 'option' );
    function reportType(obj){
        if(obj.value != ''){
            $("#report_type").html( $options.filter( '[ref_val="' + obj.value + '"]' ) );
        }else{
            $("#report_type").html( $options.filter( '[ref_val=""]' ) );
        }
        if(obj.value != 'order_report'){
            $(".js-fromdate").addClass('d-none');
            $(".js-todate").addClass('d-none');
            $(".js-own-inventory").addClass('d-none');
            $(".js-own-order").addClass('d-none');
            $(".js-product-own").addClass('d-none');
            $("#client_id").attr("required","true");
        }else{
            $(".js-fromdate").removeClass('d-none');
            $(".js-todate").removeClass('d-none');
            $("#client_id").removeAttr("required");
        }
        var reportSubType = $("#report_type").val();                                                                                                       
        if(reportSubType == 'own_inventory')
        {
            $(".js-own-inventory").removeClass('d-none');
        }
        else
        {
            $(".js-own-inventory").addClass('d-none');
            $(".js-own-order").addClass('d-none');
            $(".js-product-own").addClass('d-none');
            if(reportSubType == "own_order")
            {
                $(".js-own-order").removeClass('d-none');
            }
            if(reportSubType == "own")
            {
                $(".js-product-own").removeClass('d-none');
            }
        }
        $(".js-client").removeClass('d-none');
    }
    function ShowHideColumn(obj,column){
        if(obj.checked){
            table1.column( column ).visible( true );
            $('.fl_'+column).css('display','inline-block');
        }else{
            table1.column( column ).visible( false );
            $('.fl_'+column).css('display','none');
        }
        $('#btn_open_save_as_modal').show();
        $('#btn_save_smart_filter').show();
    }
    function changeSubReport(){
        var reportSubType = $("#report_type").val();                                                                                                       
        if(reportSubType == 'own_inventory')
        {
            $(".js-own-inventory").removeClass('d-none');
        }
        else
        {
            $(".js-own-inventory").addClass('d-none');
            $(".js-own-order").addClass('d-none');
            $(".js-product-own").addClass('d-none');
            if(reportSubType == "own_order")
            {
                $(".js-own-order").removeClass('d-none');
            }
            if(reportSubType == "own")
            {
                $(".js-product-own").removeClass('d-none');
            }
        }
        if(reportSubType == "restoke" ||  reportSubType == "transfer" ||  reportSubType == "inventory_adjustment" ||  reportSubType == "perpetual" ||  reportSubType == "own_inventory") {
        // $(".js-client").addClass('d-none');
            if($('#client_id').val() > 0){
            //   $('option:selected', '#client_id').remove();
            } 
        }
        else{
            $(".js-client").removeClass('d-none');
        }
    }
    function buildReport(){  
        if ($('#build_report').parsley().validate()) {
            var report = $("#report").val();
            var reportSubType = $("#report_type").val();
            $(".submit").attr("disabled", true);
            var url = '<?php echo e(route('report-genrate')); ?>';
            var form_cust1 = $('#build_report')[0]; 
            var form_cust2 = $('#column_visibility_form')[0];

            if($("#report_type").val()== "own") {
                form1 = new FormData(form_cust2);
                form1.append("report",$("#report").val())
                form1.append("report_type",$("#report_type").val())
                form1.append("client_id",$("#client_id").val())
                form1.append("warehouseId",$("#warehouseId").val())
            }
            //if($("#report_type").val() == "all" || $("#report_type").val() == "receive" || $("#report_type").val() == "putaway" ) {
            else{    
                form1 = new FormData(form_cust1);
            }
            //var new_object = $.extend(form1, form2);
            //console.log(form1)
            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: "POST",
                url: url,
                data: form1,
                processData: false,
                contentType: false,
                success: function( response,status, xhr ) {
                    var disposition = xhr.getResponseHeader('content-disposition');
                    var blob = new Blob([response],{
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    var name = `${report}_${reportSubType}_`;
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download =  name +"<?php echo e(date('Ymdi')); ?>"+".xlsx";
                    document.body.appendChild(link);
                    link.click();
                    
                    document.body.removeChild(link);
                    $(".submit").attr("disabled", false);
                },
                error: function(response,status, xhr){
                    $(".submit").attr("disabled", false);
                    toastr.error('Time excution limit reached');
                }
            })
            return false;
        }

    }
    </script>
    <script>

    // $("#etin_filter").on('change',function(){
    //     GetProducts();
    // });
	// $("#listing_name_filter").on('change',function(){
    //     GetProducts();
    // });
	// $("#brand_filter").on('change',function(){
    //     GetProducts();
    // });
	// $("#manufacturer_filter").on('change',function(){
    //     GetProducts();
    // });
	// $("#supplier_filter").on('change',function(){
    //     GetProducts();
    // });
	// $("#unit_description_filter").on('change',function(){
    //     GetProducts();
    // });
	// $("#product_filter").on('change',function(){
    //     GetProducts();
    // });
	// $("#upc_filter").on('change',function(){
    //     GetProducts();
    // });
	// $("#status_filter").on('change',function(){
    //     GetProducts();
    // });

       $(document).ready(function () {
        //alert($('#price_group').val());

        // $(':checkbox[name=warehouses_assigned]').on('change', function() {
        //     var assignedTo = $(':checkbox[name=warehouses_assigned]:checked').map(function() {
        //         return this.value;
        //     })
        //     .get();
        //     // console.log(JSON.stringify( assignedTo ))
        //     var checked_vals = JSON.stringify( assignedTo );
        //     console.log(checked_vals);
        //     $.ajax({
        //     type:'GET',
        //     url:'<?php echo e(route('clients.updateWarehouseAssigned')); ?>',
        //     data:{client_id:client_id,checked_val:checked_val},
        //     success:function(response){
        //         toastr.success("Success")
        //         // location.reload();
        //     },
        //     error:function(response){
        //         toastr.error("Something went wrong!")
        //     }
        // })

        // });

            <?php if($row->customer_service == "e-tailer"): ?>
                $('#consumer_services_div').show();
            <?php else: ?>
                $('#consumer_services_div').hide();
            <?php endif; ?>


            GetProducts();
            GetChanels();
            eventList();
            noteList();
            warehouseAndFulfillmentList();
            contactList();
            documentList();
            GetLinks();
            billingEventList();
            billingNoteList();
            <?php if(isset($row->business_relationship) && strtolower($row->business_relationship) === 'fulfillment'): ?>
                WarehouseOrders();
            <?php endif; ?>
       });
    pricegroupChange = () => {
      GetProducts();
    }
    function GetProducts(){
        var table = $('#datatable').DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
			ajax:{
                    url: '<?php echo e(route('getmasterproductsbyclient',$row->id)); ?>',
                    method:'GET',
                    data: {
                        etin_filter:$("#etin_filter").val(),
                        listing_name_filter:$("#listing_name_filter").val(),
						unit_description_filter:$("#unit_description_filter").val(),
						product_filter:$("#product_filter").val(),
						upc_filter:$("#upc_filter").val(),
						status_filter:$("#status_filter").val(),
                        price_group : $('#price_group').val()
                    }
                },
            columns: [
                {data: 'ETIN', name: 'ETIN'},
				{data: 'product_listing_name', name: 'product_listing_name'},
                {data: 'group_price', name: 'group_price'},
                {data: 'channel', name: 'channel'},
                {data: 'inventory', name: 'inventory'},
                {data: 'upc', name: 'upc'},
				{data: 'status', name: 'status'},
                {data: 'action', name: 'Action', orderable: false},
            ],

            // columnDefs: [
            //     {
            //         "targets": [ 0 ],
            //         "visible": false
            //     }
            // ],
            oLanguage: {
                "sSearch": "Search:"
            },
        });
    }

    function WarehouseOrders(){
        var table = $('#purchase_summary').DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
			ajax:{
                    url: '<?php echo e(route('clients.WareHouseOrders',$row->id)); ?>',
                    method:'POST'
                },
            columns: [
                {data: 'warehouse', name: 'warehouse'},
				{data: 'order', name: 'order'},
                {data: 'bol_numbers', name: 'bol_numbers'},
                {data: 'purchasing_asn_date', name: 'purchasing_asn_date'},
                {data: 'delivery_date', name: 'delivery_date'},
				{data: 'status', name: 'status'},
                {data: 'action', name: 'Action', orderable: false},
            ],
            aaSorting: [],
            // columnDefs: [
            //     {
            //         "targets": [ 0 ],
            //         "visible": false
            //     }
            // ],
            oLanguage: {
                "sSearch": "Search:"
            },
        });
    }

    function DeleteWarehouseOrder($id,$all){
        $.ajax({
            url:'<?php echo e(url('DeleteWarehouseOrder')); ?>/'+$id+'/'+$all+'/Delete',
            method:'GET',
            success:function(response){
                toastr.success("Success")
                WarehouseOrders();
            },
            error:function(response){
                toastr.error("Something went wrong!")
            }
        })
    }

    function GetChanels(){
        var table = $('#channels').DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
			ajax:{
                    url: '<?php echo e(route('clients.datatable.channelList',$row->id)); ?>',
                    method:'GET',
                },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'channel', name: 'channel'},
				{data: 'store_url', name: 'store_url'},
                {data: 'admin_url', name: 'admin_url'},
                // {data: 'username', name: 'username'},
				// {data: 'password', name: 'password'},
                {data: 'action', name: 'Action', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false
                }
            ],
            oLanguage: {
                "sSearch": "Search:"
            },
        });
    }

    function eventList(){

        $('#events').DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
			ajax:{
                    url: '<?php echo e(route('clients.datatable.eventList',$row->id)); ?>',
                    method:'GET',
                },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'event', name: 'event'},
				{data: 'frequency', name: 'frequency'},
                {data: 'date', name: 'date'},
                {data: 'details', name: 'details'},
				{data: 'owner', name: 'owner'},
                {data: 'action', name: 'Action', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false
                }
            ],
            oLanguage: {
                "sSearch": "Search:"
            },
        });
    }

    function noteList(){
         $('#notes').DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
			ajax:{
                    url: '<?php echo e(route('clients.datatable.noteList',$row->id)); ?>',
                    method:'GET',
                },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'event', name: 'event'},
				{data: 'details', name: 'details'},
                {data: 'date', name: 'date'},
                {data: 'added_by', name: 'added_by'},
                {data: 'action', name: 'Action', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false
                }
            ],
            oLanguage: {
                "sSearch": "Search:"
            },
        });
    }

    function warehouseAndFulfillmentList(){
        $('#warehouses').DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
			ajax:{
                    url: '<?php echo e(route('clients.datatable.warehouseAndFulfillmentList',$row->id)); ?>',
                    method:'GET',
                },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'event', name: 'event'},
				{data: 'frequency', name: 'frequency'},
                {data: 'date', name: 'date'},
                {data: 'details', name: 'details'},
				{data: 'owner', name: 'owner'},
                {data: 'action', name: 'Action', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false
                }
            ],
            oLanguage: {
                "sSearch": "Search:"
            },
        });
    }

    function billingEventList(){
        $('#billing_events').DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax:{
                    url: '<?php echo e(route('clients.datatable.billingeventList',$row->id)); ?>',
                    method:'GET',
                },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'event', name: 'event'},
                {data: 'frequency', name: 'frequency'},
                {data: 'date', name: 'date'},
                {data: 'details', name: 'details'},
                {data: 'owner', name: 'owner'},
                {data: 'action', name: 'Action', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false
                }
            ],
            oLanguage: {
                "sSearch": "Search:"
            },
        });
    }

    const FROM_PATTERN = 'YYYY-MM-DD';
    const TO_PATTERN   = 'MM-DD-YYYY';
    
    function billingNoteList(){
        $('#billing_notes').DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax:{
                    url: '<?php echo e(route('clients.datatable.billingNoteList',$row->id)); ?>',
                    method:'GET',
                },
            columns: [
                {data: 'type', name: 'type'},
                {data: 'details', name: 'details'},
                {data: 'date', name: 'date'},
                {data: 'created_at', name: 'created_at'},
                {data: 'added_by', name: 'added_by'},
                {data: 'invoice_date', name: 'invoice_date'},
                {data: 'location', name: 'location'},  
                {data: 'is_billable', name: 'is_billable'},              
                {data: 'action', name: 'Action', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": true
                },
                {
                    'targets': [ 2, 5 ],
                    'render': $.fn.dataTable.render.moment(FROM_PATTERN, TO_PATTERN)
                },
            ],
            oLanguage: {
                "sSearch": "Search:"
            },
        });
    }

    function contactList(){
        $('#contacts').DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
			ajax:{
                    url: '<?php echo e(route('clients.datatable.clientContactList',$row->id)); ?>',
                    method:'GET',
                },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
				{data: 'title', name: 'title'},
                {data: 'email', name: 'email'},
                {data: 'office_phone', name: 'office_phone'},
				{data: 'cell_phone', name: 'cell_phone'},
                {data: 'contact_note', name: 'contact_note'},
                {data: 'cranium', name: 'cranium'},
                {data: 'status', name: 'status',searchable: false},
                {data: 'action', name: 'Action', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false,
                }
            ],
            oLanguage: {
                "sSearch": "Search:"
            },
        });
    }

    function documentList(){
        $('#documents').DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
			ajax:{
                    url: '<?php echo e(route('clients.datatable.documentList',$row->id)); ?>',
                    method:'GET',
                },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'type', name: 'type'},
				{data: 'name', name: 'name'},
                {data: 'description', name: 'description'},
                {data: 'created_date', name: 'created_date'},
                {data: 'action', name: 'Action', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false
                }
            ],
            oLanguage: {
                "sSearch": "Search:"
            },
        });
    }

    function GetLinks(){
        $('#links').DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
			ajax:{
                    url: '<?php echo e(route('clients.datatable.linkList',$row->id)); ?>',
                    method:'GET',
                },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'url', name: 'type'},
				{data: 'name', name: 'name'},
                {data: 'description', name: 'description'},
                {data: 'created_date', name: 'created_date'},
                {data: 'action', name: 'Action', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false
                }
            ],
            oLanguage: {
                "sSearch": "Search:"
            },
        });
    }


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

    function editModal(url) {
        $.ajax({
            type:'GET',
            url:url,
            success:function(response){
                $("#editModal").html('');
                $('#editModal').html(response);
                $('#editModal').modal('show');
            }
        })
    }

    function getEventCreateModal(url){
        $.ajax({
            type:'GET',
            url:url,
            success:function(response){
                $("#createEventModal").html('');
                $('#createEventModal').html(response);
                $('#createEventModal').modal('show');
            }
        })
    }

    function editEventModal(url) {
        $.ajax({
            type:'GET',
            url:url,
            success:function(response){
                $("#editEventModal").html('');
                $('#editEventModal').html(response);
                $('#editEventModal').modal('show');
            }
        })
    }

    function getNoteCreateModal(url){
        $.ajax({
            type:'GET',
            url:url,
            success:function(response){
                $("#noteModal").html('');
                $('#noteModal').html(response);
                $('#noteModal').modal('show');
            }
        })
    }

    function editNoteModal(url) {
        $.ajax({
            type:'GET',
            url:url,
            success:function(response){
                $("#noteModal").html('');
                $('#noteModal').html(response);
                $('#noteModal').modal('show');
            }
        })
    }

    function deleteChanel(url){
        if(confirm('Are You Sure You Want To Delete This?')){
            $.ajax({
                type:'GET',
                url:url,
                success:function(response){
                    toastr.success("Success")
                    GetChanels()
                }
            })
        }
    }

    function deleteEvent(url){
        if(confirm('Are You Sure You Want To Delete This?')){
            $.ajax({
                type:'GET',
                url:url,
                success:function(response){
                    toastr.success("Success")
                    eventList()
                    billingEventList()
                }
            })
        }
    }

    function deleteNote(url){
        if(confirm('Are You Sure You Want To Delete This?')){
            $.ajax({
                type:'GET',
                url:url,
                success:function(response){
                    toastr.success("Success")
                    noteList();
                    billingNoteList();
                }
            })
        }
    }

    function deleteWarehouseAndFulfillment(url){
        if(confirm('Are You Sure You Want To Delete This?')){
            $.ajax({
                type:'GET',
                url:url,
                success:function(response){
                    toastr.success("Success")
                    warehouseAndFulfillmentList()
                }
            })
        }
    }

    function deleteContact(url){
        if(confirm('Are You Sure You Want To Delete This?')){
            $.ajax({
                type:'GET',
                url:url,
                success:function(response){
                    toastr.success("Success")
                    contactList()
                }
            })
        }
    }

    function deleteDocument(url){
        if(confirm('Are You Sure You Want To Delete This?')){
            $.ajax({
                type:'GET',
                url:url,
                success:function(response){
                    toastr.success("Success")
                    documentList()
                }
            })
        }
    }

    function deleteLink(url){
        if(confirm('Are You Sure You Want To Delete This?')){
            $.ajax({
                type:'GET',
                url:url,
                success:function(response){
                    toastr.success("Success")
                    GetLinks();
                }
            })
        }
    }

    function setPrimaryContact(checkbox_obj,id){
        debugger
        if(checkbox_obj.checked) {
           var yn = confirm('Do You want to set as primary?');
        }
        else{
            var yn = confirm('Do You want to remove as primary?');
        }
        if(yn){
            $.ajax({
                type:'GET',
                url:'<?php echo e(route('clients.setPrimaryContact')); ?>',
                data:{id:id},
                success:function(response){
                    toastr.success(response.msg)
                    contactList();
                },
                error:function(response){
                    toastr.error("Something went wrong!")
                }
            })
        }
        else{
            contactList();
        }
    }

    function updateWarehouseAssigned(checkbox_obj,client_id){

        var assignedTo = $(':checkbox[name=warehouses_assigned]:checked')
        .map(function() {
                return this.value;
        })
        .get();
        var checked_val = JSON.stringify( assignedTo );
        $.ajax({
            type:'GET',
            url:'<?php echo e(route('clients.updateWarehouseAssigned')); ?>',
            data:{client_id:client_id,checked_val:checked_val},
            success:function(response){
                toastr.success("Success")
                // location.reload();
            },
            error:function(response){
                toastr.error("Something went wrong!")
            }
        })
    }

    function showHideConsumerServices(params) {
       if(params != "client"){
           $('#consumer_services_div').show();
       }
       else{
        $('#consumer_services_div').hide();
       }
    }

    $("#new_client").on('submit',function(e){
        e.preventDefault();
        $(".submit").attr("disabled", true);
        var form_cust = $('#new_client')[0];
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '<?php echo e(route('clients.update',$row->id)); ?>',
            data: form1,
            processData: false,
            contentType: false,
            success: function( response ) {
                $(".submit").attr("disabled", false);
                if(response.error == false){
                    toastr.success(response.msg);
                    setTimeout(function(){
                        // location.href= response.url;
                        location.reload();
                    },2000);
                }else{
                    toastr.error(response.msg);
                }
            },
            error: function(data){
                $(".submit").attr("disabled", false);
                var errors = data.responseJSON;
                $.each( errors.errors, function( key, value ) {
                    var ele = "#"+key;
                    $(ele).addClass('border-danger');
                    $('<label class="text-danger">'+ value +'</label>').insertAfter(ele);
                });
            }
        })

    })

    $("#updateClientManagementDetails").on('submit',function(e){
        e.preventDefault();
        $(".submit").attr("disabled", true);
        var form_cust = $('#updateClientManagementDetails')[0];
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '<?php echo e(route('clients.updateClientManagementDetails',$row->id)); ?>',
            data: form1,
            processData: false,
            contentType: false,
            success: function( response ) {
                $(".submit").attr("disabled", false);
                if(response.error == false){
                    toastr.success(response.msg);
                    setTimeout(function(){
                        // location.href= response.url;
                    },1800);
                }else{
                    toastr.error(response.msg);
                }
            },
            error: function(data){
                $(".submit").attr("disabled", false);
                var errors = data.responseJSON;
                $.each( errors.errors, function( key, value ) {
                    var ele = "#"+key;
                    $(ele).addClass('border-danger');
                    $('<label class="text-danger">'+ value +'</label>').insertAfter(ele);
                });
            }
        })

    })

    $.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		GetActiveProducts();
		//Active Product List
		function GetActiveProducts(){
			$("#preloader").show();
            var url = '<?php echo e(route('getOptimizedorders')); ?>';
			table1 = $('#datatableOrder').DataTable({
				// dom:"Bfrtip",
				paging:   true,
				destroy: true,
				responsive: false,
				processing: true,
				serverSide: true,
				autoWidth: false,
				colReorder: true,
				searching:true,
				colReorder: {
					order: [  ]
				},
				// scrollX: true,
				// stateSave: true,

				lengthMenu: [[10,25, 100, -1], [10, 25, 100, "All"]],
				pageLength: 10,

				ajax:{
						url: url,
						method:'POST',
						data: function(d) {
							// console.log(d);
							var frm_data = $('#form_filters').serializeArray();
							$.each(frm_data, function(key, val) {
								d[val.name] = val.value;
							});
							
								d['client_id'] = '<?php echo e($row->id); ?>';
                                d['client_order'] = 1;
							
						}
					},
				columns: [
					{data: 'created_at', name: 'created_at',defaultContent:'', searchable: false},
                    {data: 'etailer_order_number', name: 'etailer_order_number',defaultContent:'', searchable: false},
                    {data: 'client_name', name: 'client_name',defaultContent:'', searchable: false},
                    {data: 'order_source', name: 'order_source',defaultContent:'', searchable: false},
                    {data: 'ship_to_state', name: 'ship_to_state',defaultContent:'', searchable: false},
                    {data: 'channel_estimated_delivery_date', name: 'channel_estimated_delivery_date',defaultContent:'', searchable: false},
                    {data: 'ship_by_date', name: 'ship_by_date',defaultContent:'', searchable: false},
                    {data: 'order_status_name', name: 'order_status_name',defaultContent:'', searchable: false},
                    {data: 'ship_dates', name: 'ship_dates'},
                    {data: 'action', name: 'action'},
				],
					// columnDefs: [
					// 	{
					// 		"targets": [8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28],
					// 		"visible": false,
					// 	},
				// ],
				oLanguage: {
					"sSearch": "Search:",

				},
				fnInitComplete: function (oSettings, json) {
					$("#preloader").hide();
				},
				rowCallback: function(row, data) {
					$('td:eq(1)', row).css('color', '#4d2673');
					$('td:eq(1)', row).css('font-weight', 'bold');
					$('td:eq(1)', row).css('cursor', 'pointer');
				}

			});
			var col_order = table1.colReorder.order();
			table1.colReorder.order(col_order)
			// console.log(col_order)
			table1.on( 'column-reorder', function ( e, settings, details ) {
				$("#btn_open_save_as_modal").css("display", "");
				var order = table1.colReorder.order();
				$('#column_orders').val(order)
				$('#btn_save_smart_filter').show();
			} );
			$('.listing-filter-columns').each(function(e){
				//    console.log($(this).val(), this.checked,e)
				if(this.checked === false){
						table1.column( $(this).val() ).visible( false );
				}
			});
		}

        $("#notification_form").on('submit',function(e){
            e.preventDefault();
            $(".submit").attr("disabled", true);
            var form_cust = $('#notification_form')[0]; 
            let form1 = new FormData(form_cust);
            $.ajax({
                type: "POST",
                url: '<?php echo e(route('clients.update_notification',$row->id)); ?>',
                data: form1, 
                processData: false,
                contentType: false,
                success: function( response ) {
                    $(".submit").attr("disabled", false);
                    if(response.error == false){
                        toastr.success(response.msg);
                        setTimeout(function(){
                        },2000);
                    }else{
                        toastr.error(response.msg);
                    }
                },
                error: function(data){
                    $(".submit").attr("disabled", false);
                    var errors = data.responseJSON;
                    $.each( errors.errors, function( key, value ) {
                        var ele = "#"+key;
                        $(ele).addClass('border-danger');
                        $('<label class="text-danger">'+ value +'</label>').insertAfter(ele);
                    });
                }
            })
                
        });


   </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\cranium_new\resources\views/clients/edit.blade.php ENDPATH**/ ?>