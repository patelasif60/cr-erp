<div class="side-content-wrap">
    <div class="sidebar-left open rtl-ps-none" data-perfect-scrollbar data-suppress-scroll-x="true">
        <ul class="navigation-left">
            <?php if(auth()->user()->client == ''): ?>
                <?php if(moduleacess('Dashboard')): ?>
                    <li class="nav-item <?php echo e(request()->is('dashboard/*') ? 'active' : ''); ?>" >
                        <a class="nav-item-hold" href="<?php echo e(route('home')); ?>">
                            <i class="nav-icon i-Bar-Chart"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                <?php endif; ?>
                <?php if(moduleacess('ProductManagement')): ?>
                    <li class="nav-item <?php echo e(request()->is('prodManagement/*') ? 'active' : ''); ?>" data-item="prodManagement">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Library"></i>
                            <span class="nav-text">Product Management</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                <?php endif; ?>
                <?php if(moduleacess('ProductSelectionManagement')): ?>
                    <li class="nav-item <?php echo e(request()->is('prodSelectionManagement/*') ? 'active' : ''); ?>" data-item="prodSelectionManagement">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Library"></i>
                            <span class="nav-text">Product Selection Management</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                <?php endif; ?>
                <?php if(auth()->user()->client == ''): ?>
                    <li class="nav-item <?php echo e(request()->is('orders/*') ? 'active' : ''); ?>">
                        <a class="nav-item-hold" href="<?php echo e(route('orders.index')); ?>">
                            <i class="nav-icon i-Add-Cart"></i>
                            <span class="nav-text">Orders</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                <?php endif; ?>
                <?php if(moduleacess('Clients')): ?>
                    <li class="nav-item <?php echo e(request()->is('clients/*') ? 'active' : ''); ?>">
                        <a class="nav-item-hold" href="<?php echo e(route('clients.index')); ?>">
                            <i class="nav-icon i-Love-User"></i>
                            <span class="nav-text">Clients</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                <?php endif; ?>
                <?php if(moduleacess('Suppliers')): ?>
                    <li class="nav-item <?php echo e(request()->is('suppliers/*') ? 'active' : ''); ?>">
                        <a class="nav-item-hold" href="<?php echo e(route('suppliers.index')); ?>">
                            <i class="nav-icon i-Safe-Box"></i>
                            <span class="nav-text">Suppliers</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                <?php endif; ?>  
                <?php if(auth()->user()->client == ''): ?>
                    <li class="nav-item <?php echo e(request()->is('warmanagement/*') ? 'active' : ''); ?>" data-item="warmanagement">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Tablet-Secure"></i>
                            <span class="nav-text">Warehouse Management</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                <?php endif; ?>
                <?php if(moduleacess('SupplierProductLists')): ?>
                    <!-- <li class="nav-item <?php echo e(request()->is('supplier/*') ? 'active' : ''); ?>" data-item="supplier">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Suitcase"></i>
                            <span class="nav-text">Supplier Product Lists</span>
                        </a>
                        <div class="triangle"></div>
                    </li> -->
                <?php endif; ?>
                <?php if(moduleacess('Suppliers')): ?>
                    <li class="nav-item <?php echo e(request()->is('carriers/*') ? 'active' : ''); ?>">
                        <a class="nav-item-hold" href="<?php echo e(route('carriers.index')); ?>">
                            <i class="nav-icon i-Align-Justify-All"></i>
                            <span class="nav-text">Carriers</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                <?php endif; ?>
                <?php if(moduleacess('PriceQuote')): ?>
                    <li class="nav-item">
                        <a class="nav-item-hold" href="https://hydrax-pro.com/price_quote2.php">
                            <i class="nav-icon i-Paypal"></i>
                            <span class="nav-text">Price Quote</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                <?php endif; ?>
                <?php if(Auth::user()->role != 3 && Auth::user()->role != 6): ?>           
                    <li class="nav-item <?php echo e(request()->is('settings/*') ? 'active' : ''); ?>" data-item="settings">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Settings-Window"></i>
                            <span class="nav-text">Settings</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                <?php endif; ?>
                <?php if(moduleacess('ReportManagement')): ?>
                    <li class="nav-item <?php echo e(request()->is('reportsManagement/*') ? 'active' : ''); ?>" data-item="reportsManagement">
                        <a class="nav-item-hold" href="#">
                            <i class="nav-icon i-Library"></i>
                            <span class="nav-text">Report Management</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                <?php endif; ?>
            <?php else: ?>
                <li class="nav-item <?php echo e(request()->is('dashboard/*') ? 'active' : ''); ?>" >
                    <a class="nav-item-hold" href="<?php echo e(route('home')); ?>">
                        <i class="nav-icon i-Bar-Chart"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <div class="triangle"></div>
                </li>
                <li class="nav-item <?php echo e(request()->is('clients/clients_orders*') ? 'active' : ''); ?>">
                    <a class="nav-item-hold" href="<?php echo e(route('clients.clients.orders')); ?>">
                        <i class="nav-icon i-Add-Cart"></i>
                        <span class="nav-text">Orders</span>
                    </a>
                    <div class="triangle"></div>
                </li>
                <li class="nav-item <?php echo e(request()->is('clients/clients_products*') ? 'active' : ''); ?>">
                    <a class="nav-item-hold" href="<?php echo e(route('clients.clients.products')); ?>">
                        <i class="nav-icon i-Library"></i>
                        <span class="nav-text">Products</span>
                    </a>
                    <div class="triangle"></div>
                </li>
                <li class="nav-item <?php echo e(request()->is('clients/client_warehouse_orders*') ? 'active' : ''); ?>">
                    <a class="nav-item-hold" href="<?php echo e(route('clients.clients.client_warehouse_orders')); ?>">
                        <i class="nav-icon i-Checkout-Basket"></i>
                        <span class="nav-text">Warehouse Orders</span>
                    </a>
                    <div class="triangle"></div>
                </li>
                <li class="nav-item <?php echo e(request()->is('clients/client_contacts*') ? 'active' : ''); ?>">
                    <a class="nav-item-hold" href="<?php echo e(route('clients.clients.client_contacts')); ?>">
                        <i class="nav-icon i-File-Bookmark"></i>
                        <span class="nav-text">Client Contacts</span>
                    </a>
                    <div class="triangle"></div>
                </li>
                <li class="nav-item <?php echo e(request()->is('clients/clients_documents*') ? 'active' : ''); ?>">
                    <a class="nav-item-hold" href="<?php echo e(route('clients.clients.clients_documents')); ?>">
                        <i class="nav-icon i-Folder-With-Document"></i>
                        <span class="nav-text">Client Documents</span>
                    </a>
                    <div class="triangle"></div>
                </li>
                <li class="nav-item <?php echo e(request()->is('clients/clients_information*') ? 'active' : ''); ?>">
                    <a class="nav-item-hold" href="<?php echo e(route('clients.clients.clients_information')); ?>">
                        <i class="nav-icon i-Settings-Window"></i>
                        <span class="nav-text">Client Information</span>
                    </a>
                    <div class="triangle"></div>
                </li>
                <li class="nav-item <?php echo e(request()->is('clients/clients_reports*') ? 'active' : ''); ?>">
                    <a class="nav-item-hold" href="<?php echo e(route('clients.clients.clients_reports')); ?>">
                        <i class="nav-icon i-Book"></i>
                        <span class="nav-text">Client Reports</span>
                    </a>
                    <div class="triangle"></div>
                </li>

                

                
            <?php endif; ?>

        </ul>
    </div>




    <div class="sidebar-left-secondary rtl-ps-none" data-perfect-scrollbar data-suppress-scroll-x="true">
        <!-- Submenu Dashboards -->
        <?php //echo $second_html; ?>
        <ul class="childNav" data-parent="prodManagement">
            <?php if(moduleacess('ListAllMasterProduct')): ?>
                <li class="nav-item">
                    <a class="<?php echo e(Route::currentRouteName()=='masterparoducts_approved' ? 'open' : ''); ?>"
                        href="<?php echo e(url('masterparoducts_approved')); ?>">
                        <i class="nav-icon i-Filter"></i>
                        <span class="item-name">Active Product Listings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="<?php echo e(Route::currentRouteName()=='allmasterproductlsts' ? 'open' : ''); ?>"
                        href="<?php echo e(url('allmasterproductlsts')); ?>">
                        <i class="nav-icon fas fa-list"></i>
                        <span class="item-name">Product Queues</span>
                    </a>
                </li>
			<?php endif; ?>
            <?php if(Auth::user()->role != 3): ?>
                <li class="nav-item <?php echo e(request()->is('roles/*') ? 'active' : ''); ?>">
                    <a class="nav-item-hold" href="<?php echo e(route('pricegroup.index')); ?>">
                        <i class="nav-icon i-Money"></i>
                        <span class="nav-text">Product Pricing</span>
                    </a>
                    <div class="triangle"></div>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='productinventorylist' ? 'open' : ''); ?>"
                    href="<?php echo e(route('productinventory.index')); ?>">
                    <i class="nav-icon i-Full-Basket"></i>
                    <span class="item-name">Product Inventory</span>
                </a>
            </li>
            <li class="nav-item">
                <a class=""
                    href="<?php echo e(route('kitproductinventory.index')); ?>">
                    <i class="nav-icon i-Full-Cart"></i>
                    <span class="item-name">Kit Inventory</span>
                </a>
            </li>
            <li class="nav-item">
                <a class=""
                    href="<?php echo e(route('listpackagingmatirial.index')); ?>">
                    <i class="nav-icon i-Full-Cart"></i>
                    <span class="item-name">Packaging & Materials</span>
                </a>
            </li>
            <?php if(moduleacess('MapSupplierWithCsv')): ?>
			<li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='map_supplier_with_csv' ? 'open' : ''); ?>"
                    href="<?php echo e(url('map_supplier_with_csv')); ?>">
                    <i class="nav-icon i-Split-Vertical"></i>
                    <span class="item-name">Map Suppliers with CSV file</span>
                </a>
            </li>
			<?php endif; ?>
            <?php if(moduleacess('AddUpdateProductFromCsv')): ?>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='uploaddata' ? 'open' : ''); ?>" href="<?php echo e(url('uploaddata')); ?>">
                    <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                    <span class="item-name">Add/Update Supplier Product(s) From CSV</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if(moduleacess('UploadMasterProductsSAInvProducts')): ?>
			<li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='uploadintable' ? 'open' : ''); ?>" href="<?php echo e(url('uploadintable')); ?>">
                    <i class="nav-icon i-File-Upload"></i>
                    <span class="item-name">Upload Master Products, DOT, SA Inventory Product(s)</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- <?php if(moduleacess('ParentProductWizard')): ?>
			<li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='parentproductwizard' ? 'open' : ''); ?>"
                    href="<?php echo e(url('parentproductwizard')); ?>">
                    <i class="nav-icon fas fa-hat-wizard"></i>
                    <span class="item-name">Parent Product Wizard</span>
                </a>
            </li>
            <?php endif; ?> -->
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='uploadExcelView' ? 'open' : ''); ?>"
                    href="<?php echo e(url('uploadExcelView')); ?>">
                    <i class="nav-icon fas fa-list"></i>
                    <span class="item-name">Insert CSV Data in Carrier Management & Configuration Tables</span>
                </a>
            </li>
        </ul>
        <ul class="childNav" data-parent="warmanagement">
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='masterparoducts_approved' ? 'open' : ''); ?>"
                    href="<?php echo e(route('warehousemanagment.cyclecount.index')); ?>">
                    <i class="nav-icon fas fa-list"></i>
                    <span class="item-name">Cycle Count</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='wmsconfigration' ? 'open' : ''); ?>"
                    href="<?php echo e(route('wmsconfig')); ?>">
                    <i class="nav-icon fas fa-list"></i>
                    <span class="item-name">Configuration</span>
                </a>
            </li>
            <?php if(auth()->user()->role == 1): ?>
                <li class="nav-item">
                    <a target="_blank" class="<?php echo e(Route::currentRouteName()=='wmsconfigration' ? 'open' : ''); ?>"
                        href="<?php echo e(route('zip_zone_wh')); ?>">
                        <i class="nav-icon fas fa-list"></i>
                        <span class="item-name">Zip Zone WH Transit Days</span>
                    </a>
                </li>
            <?php endif; ?>            

        </ul>
        <ul class="childNav" data-parent="prodSelectionManagement">
            <?php if(moduleacess('AllSubMenusSelectionfunctions')): ?>
            <li class="nav-item " data-item="allergens" id="demo">
                <a class="<?php echo e(Route::currentRouteName()=='allergens' ? 'open' : ''); ?>"
                href="<?php echo e(url('allergens')); ?>">
                    <i class="nav-icon fas fa-allergies"></i>
                    <span class="item-name">Allergens</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='brands' ? 'open' : ''); ?>"
                href="<?php echo e(url('brands')); ?>">
                    <i class=" nav-icon fab fa-bandcamp"></i>
                    <span class="item-name">Brands</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='categories' ? 'open' : ''); ?>" href="<?php echo e(url('categories')); ?>">

                    <i class="nav-icon fas fa-list"></i>
                    <span class="item-name">Categories</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='country' ? 'open' : ''); ?>"
                href="<?php echo e(url('country')); ?>">
                    <i class="nav-icon fa fa-globe"></i>
                    <span class="item-name">Countries</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='etailer_availability' ? 'open' : ''); ?>"
                href="<?php echo e(url('etailer_availability')); ?>">
                    <i class="nav-icon fa fa-angellist"></i>
                    <span class="item-name">eTailer Availability</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='item_form_description' ? 'open' : ''); ?>"
                href="<?php echo e(url('item_form_description')); ?>">
                    <i class="nav-icon fas fa-file-prescription"></i>
                    <span class="item-name">Item Form Description</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='image_type' ? 'open' : ''); ?>"
                href="<?php echo e(url('image_type')); ?>">
                    <i class="nav-icon fas fa-images"></i>
                    <span class="item-name">Image Type</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='manufacturer' ? 'open' : ''); ?>"
                href="<?php echo e(url('manufacturer')); ?>">
                    <i class="nav-icon fas fa-industry"></i>
                    <span class="item-name">Manufacturers</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='product_statuses' ? 'open' : ''); ?>"
                    href="<?php echo e(url('product_statuses')); ?>">
                    <i class="nav-icon i-Split-Vertical"></i>
                    <span class="item-name">Product Status</span>
                </a>
            </li>
			<li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='prop_ingredients' ? 'open' : ''); ?>"
                href="<?php echo e(url('prop_ingredients')); ?>">
                    <i class="nav-icon fab fa-product-hunt"></i>
                    <span class="item-name">Product Ingredients</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='product_tags' ? 'open' : ''); ?>"
                href="<?php echo e(url('product_tags')); ?>">
                    <i class="nav-icon i-Clown"></i>
                    <span class="item-name">Product Tags</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='product_type' ? 'open' : ''); ?>"
                href="<?php echo e(url('product_type')); ?>">
                    <i class="nav-icon fa fa-file"></i>
                    <span class="item-name">Product Type</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='materialtype' ? 'open' : ''); ?>"
                href="<?php echo e(url('materialtype')); ?>">
                    <i class="nav-icon fa fa-file"></i>
                    <span class="item-name">Material Type</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='supplier_status' ? 'open' : ''); ?>"
                href="<?php echo e(url('supplier_status')); ?>">
                    <i class="nav-icon fas fa-adjust"></i>
                    <span class="item-name">Supplier Status</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='unit_description' ? 'open' : ''); ?>"
                href="<?php echo e(url('unit_description')); ?>">
                    <i class="nav-icon fab fa-unity"></i>
                    <span class="item-name">Unit Description</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='unit_sizes' ? 'open' : ''); ?>"
                href="<?php echo e(url('unit_sizes')); ?>">
                    <i class="nav-icon fas fa-window-maximize"></i>
                    <span class="item-name">Unit Sizes</span>
                </a>
            </li>
            <li class="nav-item " data-item="kit_description" id="demo">
                <a class="<?php echo e(Route::currentRouteName()=='kit_description' ? 'open' : ''); ?>"
                href="<?php echo e(url('kit_description')); ?>">
                    <i class="nav-icon fas fa-list"></i>
                    <span class="item-name">Kit Description</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='billingnotes' ? 'open' : ''); ?>"
                href="<?php echo e(url('billingnotes')); ?>">
                    <i class="nav-icon fa fa-file"></i>
                    <span class="item-name">Billing Notes</span>
                </a>
            </li>
            <li>
                <a class="<?php echo e(Route::currentRouteName()=='locationtypes' ? 'open' : ''); ?>"
                href="<?php echo e(url('locationtypes')); ?>">
                    <i class="nav-icon fa fa-file"></i>
                    <span class="item-name">Location Types</span>
                </a>
            </li>    
            <?php endif; ?>                     
        </ul>
        <ul class="childNav" data-parent="supplier">
            <?php if(moduleacess('AllSubMenusSupplierProductTables')): ?>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='dotproductlist' ? 'open' : ''); ?>" href="<?php echo e(route('dotproductlist')); ?>">
                    <i class="nav-icon fa fa-eercast"></i>
                    <span class="item-name">DOT</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='dryerproductlist' ? 'open' : ''); ?>"
                    href="<?php echo e(route('dryerproductlist')); ?>">
                    <i class="nav-icon fa fa-grav"></i>
                    <span class="item-name">Dreyer's</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='harsheyproductlist' ? 'open' : ''); ?>" href="<?php echo e(route('harsheyproductlist')); ?>">
                    <i class="nav-icon fab fa-artstation"></i>
                    <span class="item-name">Hershey</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='keheproductlist' ? 'open' : ''); ?>" href="<?php echo e(route('keheproductlist')); ?>">
                    <i class="nav-icon fa fa-i-cursor"></i>
                    <span class="item-name">KeHE</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='marsproductlist' ? 'open' : ''); ?>"
                    href="<?php echo e(route('marsproductlist')); ?>">
                    <i class="nav-icon fas fa-mars"></i>
                    <span class="item-name">MARS</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='nestleproductlist' ? 'open' : ''); ?>"
                    href="<?php echo e(route('nestleproductlist')); ?>">
                    <i class="nav-icon fa fa-snowflake-o"></i>
                    <span class="item-name">Nestle</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='miscproductlist' ? 'open' : ''); ?>"
                    href="<?php echo e(route('miscproductlist')); ?>">
                    <i class="nav-icon fa fa-retweet"></i>
                    <span class="item-name">Miscellaneous</span>
                </a>
            </li>
        <?php endif; ?>
        </ul>
        <ul class="childNav" data-parent="reportsManagement">
            <?php if(moduleacess('MasterProductDailyReport')): ?>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='master_product_daily_report' ? 'open' : ''); ?>"
                    href="<?php echo e(url('master_product_daily_report')); ?>">
                    <i class="nav-icon fab fa-product-hunt"></i>
                    <span class="item-name">Master Product Daily Report</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if(moduleacess('NewsFeed')): ?>
			<li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='news_feed' ? 'open' : ''); ?>"
                    href="<?php echo e(url('news_feed')); ?>">
                    <i class="nav-icon i-Friendfeed"></i>
                    <span class="item-name">News Feed</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if(moduleacess('Feedback')): ?>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='feedbacks' ? 'open' : ''); ?>"
                    href="<?php echo e(url('feedbacks')); ?>">
                    <i class="nav-icon i-Split-Vertical"></i>
                    <span class="item-name">Feedbacks</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if(moduleacess('get_help')): ?>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='get_help' ? 'open' : ''); ?>"
                    href="<?php echo e(url('get_help')); ?>">
                    <i class="nav-icon i-Info-Window"></i>
                    <span class="item-name">Help</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if(moduleacess('NewRequestTypes')): ?>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='new_requests' ? 'open' : ''); ?>"
                    href="<?php echo e(url('new_requests')); ?>">
                    <i class="nav-icon fa fa-newspaper-o"></i>
                    <span class="item-name">New Request Types</span>
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='markout_report' ? 'open' : ''); ?>"
                    href="<?php echo e(url('markout_report')); ?>">
                    <i class="nav-icon fab fa-product-hunt"></i>
                    <span class="item-name">Markout Products</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='pipeline_and_metrix' ? 'open' : ''); ?>"
                    href="<?php echo e(url('pipeline_and_metrix')); ?>">
                    <i class="nav-icon fab fa-product-hunt"></i>
                    <span class="item-name">Pipeline & Productivity Metrics</span>
                </a>
            </li>
			<li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='reportbuilder' ? 'open' : ''); ?>"
                    href="<?php echo e(url('reportbuilder')); ?>">
                    <i class="nav-icon i-Library"></i>
                    <span class="item-name">Report Builder</span>
                </a>
            </li>
        </ul>
        <ul class="childNav" data-parent="settings">
            <?php if(moduleacess('Users')): ?>
                <li class="nav-item <?php echo e(request()->is('users/*') ? 'active' : ''); ?>">
                    <a class="nav-item-hold" href="<?php echo e(route('users.index')); ?>">
                        <i class="nav-icon i-MaleFemale"></i>
                        <span class="nav-text">Users</span>
                    </a>
                    <div class="triangle"></div>
                </li>           
                <li class="nav-item <?php echo e(request()->is('roles/*') ? 'active' : ''); ?>">
                    <a class="nav-item-hold" href="<?php echo e(route('roles.index')); ?>">
                        <i class="nav-icon i-MaleFemale"></i>
                        <span class="nav-text">Roles</span>
                    </a>
                    <div class="triangle"></div>
                </li>
                <li class="nav-item <?php echo e(request()->is('chanel_management/*') ? 'active' : ''); ?>">
                    <a class="nav-item-hold" href="<?php echo e(route('chanel_management.index')); ?>">
                        <i class="nav-icon i-Info-Window"></i>
                        <span class="nav-text">Channel Management</span>
                    </a>
                    <div class="triangle"></div>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="<?php echo e(Route::currentRouteName()=='time_zones' ? 'open' : ''); ?>"
                    href="<?php echo e(url('time_zones')); ?>">
                    <i class="nav-icon i-Time-Window"></i>
                    <span class="item-name">Time Zones</span>
                </a>
            </li>
            <li class="nav-item">
                <a class=""
                    href="<?php echo e(url('icecharttemplate')); ?>">
                    <i class="nav-icon fab fa-product-hunt"></i>
                    <span class="item-name">Ice Charts</span>
                </a>
            </li>
            <li class="nav-item">
                <a class=""
                    href="<?php echo e(url('gelpacktemplate')); ?>">
                    <i class="nav-icon fab fa-product-hunt"></i>
                    <span class="item-name">Gel Pack Charts</span>
                </a>
            </li>
            <li class="nav-item">
                <a class=""
                    href="<?php echo e(url('getpackagingcomponants')); ?>">
                    <i class="nav-icon fab fa-product-hunt"></i>
                    <span class="item-name">Packaging Components</span>
                </a>
            </li>
            <li class="nav-item">
                <a class=""
                    href="<?php echo e(url('misc_cost')); ?>">
                    <i class="nav-icon fab fa-product-hunt"></i>
                    <span class="item-name">Misc. Cost Values</span>
                </a>
            </li>
             <li class="nav-item <?php echo e(request()->is('users/*') ? 'active' : ''); ?>">
                <a class="nav-item-hold" href="<?php echo e(url('sku_index')); ?>">
                    <i class="nav-icon fab fa-product-hunt"></i>
                    <span class="nav-text">Order Management & Routing</span>
                </a>
                <div class="triangle"></div>
            </li>
            <?php if(moduleacess('restockproductsetting')): ?>
            <li class="nav-item">
                <a class=""
                    href="<?php echo e(url('getrestockproductsetting')); ?>">
                    <i class="nav-icon fab fa-product-hunt"></i>
                    <span class="item-name">Restock product Setting</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
        <ul class="childNav" data-parent="inventory">
            
        </ul>
    </div>
    <div class="sidebar-overlay"></div>
</div>
<!--=============== Left side End ================-->
<?php /**PATH C:\wamp64\www\cranium_new\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>