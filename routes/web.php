<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ImageUploadController;
use App\MasterProduct;
use App\OrderSummary;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/MarkAsRead/{id}','HomeController@MarkAsRead')->name('MarkAsRead');
	Route::get('logout', 'Auth\LoginController@logout');
    Route::get('/notifications', 'NotificationController@index')->name('notifications');
    Route::post('/SaveChat', 'NotificationController@SaveChat')->name('SaveChat');
    Route::post('/GetChat', 'NotificationController@GetChat')->name('GetChat');


    ## Supplier DOT table
    Route::get('/map_supplier_dot_with_master_product', 'SupplierDotController@map_supplier_dot_with_master_product')->name('map_supplier_dot_with_master_product');
    Route::post('/save_supplier_dot_with_master_product', 'SupplierDotController@save_supplier_dot_with_master_product');

    # Map Headers with CSV----------
    Route::get('/map_supplier_with_csv', 'SupplierDotController@map_supplier_with_csv')->name('map_supplier_with_csv');
    Route::post('/import_map_supplier_with_csv', 'SupplierDotController@import_map_supplier_with_csv')->name('import_map_supplier_with_csv');
    Route::post('/save_supplier_with_csv', 'SupplierDotController@save_supplier_with_csv')->name('save_supplier_with_csv');

    ## Upload CSV data---------
    Route::get('/uploaddata', function () {
        if(moduleacess('AddUpdateProductFromCsv') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        return view('cranium.uploaddata');
    });
	Route::get('/uploadintable', function () {
        if(moduleacess('UploadMasterProductsSAInvProducts') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        return view('cranium.uploadintable');
    });
    Route::post('/upload_csv_to_table', 'MasterProductController@upload_csv_to_table')->name('upload_csv_to_table');
    Route::post('/upload_client_product', 'MasterProductController@upload_client_product')->name('upload_client_product');
    Route::post('/upload_csv_to_other_tables', 'MasterProductController@upload_csv_to_other_tables')->name('upload_csv_to_other_tables');
    Route::post('/update_data_using_csv', 'MasterProductController@update_data_using_csv')->name('update_data_using_csv');

	 ## Add Master Product Manually---------
    Route::get('/addnewmasterproduct', 'MasterProductController@addnewmasterview')->name('addnewmasterproductview');
    Route::put('/addmasterproduct', 'MasterProductController@insertnewmaster')->name('addmasterproduct');
    Route::get('/updateflag/{id}', 'MasterProductController@updateflag')->name('updateflag');
    Route::get('/deletemasterproduct/{id}', 'MasterProductController@deletemasterproduct')->name('deletemasterproduct');
    Route::get('/deletemasterproductdraft/{id}', 'MasterProductController@deletemasterproductdraft')->name('deletemasterproductdraft');


    //NewRequests
    Route::post('/new_manufacturers_request', 'MasterProductController@new_manufacturers_request')->name('new_manufacturers_request');
    Route::post('/new_brand_request', 'MasterProductController@new_brand_request')->name('new_brand_request');
    Route::post('/new_product_type_request', 'MasterProductController@new_product_type_request')->name('new_product_type_request');
    Route::post('/new_product_type_kit_request', 'MasterProductController@new_product_type_kit_request')->name('new_product_type_kit_request');
    Route::post('/new_unit_description_request', 'MasterProductController@new_unit_description_request')->name('new_unit_description_request');

    //remove image
    Route::get('remove-image/{id}', 'MasterProductController@removeImage')->name('remove_image');

    ## Supplier Product listing views
    Route::get('/dotproductlist', function () {
        if(moduleacess('SupplierProductLists') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('cranium.supplierProdListing.productListDot');
    })->name('dotproductlist');
    Route::get('/dryerproductlist', function () {
        if(moduleacess('SupplierProductLists') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('cranium.supplierProdListing.productListDryer');
    })->name('dryerproductlist');
    Route::get('/harsheyproductlist', function () {
        if(moduleacess('SupplierProductLists') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('cranium.supplierProdListing.productListHarshey');
    })->name('harsheyproductlist');
    Route::get('/keheproductlist', function () {
        if(moduleacess('SupplierProductLists') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('cranium.supplierProdListing.productListKehe');
    })->name('keheproductlist');
    Route::get('/marsproductlist', function () {
        if(moduleacess('SupplierProductLists') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('cranium.supplierProdListing.productListMars');
    })->name('marsproductlist');
    Route::get('/nestleproductlist', function () {
        if(moduleacess('SupplierProductLists') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('cranium.supplierProdListing.productListNestle');
    })->name('nestleproductlist');
    Route::get('/miscproductlist', function () {
        if(moduleacess('SupplierProductLists') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('cranium.supplierProdListing.productListMisc');
    })->name('miscproductlist');


    ## Supplier Product listing Ajax
    Route::get('/getdotproducts', 'SupplierDotController@getdotproducts')->name('getdotproducts');
    Route::get('/getdryerproducts', 'SupplierDryersController@getdryerproducts')->name('getdryerproducts');
    Route::get('/gethersheyproducts', 'SupplierHersheyController@gethersheyproducts')->name('gethersheyproducts');
    Route::get('/getkeheproducts', 'SupplierKeheController@getkeheproducts')->name('getkeheproducts');
    Route::get('/getmarsproducts', 'SupplierMarsController@getmarsproducts')->name('getmarsproducts');
    Route::get('/getnestleproducts', 'SupplierNestleController@getnestleproducts')->name('getnestleproducts');
    Route::get('/getmiscproducts', 'SupplierMiscellaneousController@getmiscproducts')->name('getmiscproducts');

	## Get Supplier Mappling for Insert/Update CSV Page
	Route::get('/getdotmap', 'SupplierDotController@getdotmap')->name('getdotmap');
	Route::get('/getkehemap', 'SupplierKeheController@getkehemap')->name('getkehemap');
	Route::get('/getmarsmap', 'SupplierMarsController@getmarsmap')->name('getmarsmap');
	Route::get('/getdryerssmap', 'SupplierDryersController@getdryerssmap')->name('getdryerssmap');
	Route::get('/getharsleymap', 'SupplierHersheyController@getharsleymap')->name('getharsleymap');
	Route::get('/getnestlemap', 'SupplierNestleController@getnestlemap')->name('getnestlemap');
	Route::get('/getmiscmap', 'SupplierMiscellaneousController@getmiscmap')->name('getmiscmap');

	## Master Product listing Ajax
    Route::get('/getactivemasterproducts', 'MasterProductController@getactivemasterproducts')->name('getactivemasterproducts');
	Route::get('/getmasterproducts', 'MasterProductController@getmasterproducts')->name('getmasterproducts');
    Route::get('/getmasterproductsbyclient/{id}', 'MasterProductController@getmasterproductsbyclient')->name('getmasterproductsbyclient');

	Route::post('/getmasterproducts2', 'MasterProductController@getmasterproducts2')->name('getmasterproducts2');
	Route::post('/exportMasterProducts', 'MasterProductController@exportMasterProducts')->name('exportMasterProducts');
	Route::get('/deletepdf', 'MasterProductController@deletepdf')->name('deletepdf');

	Route::get('/getnotapprovedmasterproducts', 'MasterProductController@getnotapprovedmasterproducts')->name('getnotapprovedmasterproducts');
	Route::get('/geteditedmasterproducts', 'MasterProductController@geteditedmasterproducts')->name('geteditedmasterproducts');
    Route::get('/getaddedmasterproducts', 'MasterProductController@getaddedmasterproducts')->name('getaddedmasterproducts');

    //Product History
    Route::get('/getProductHistory/{id}', 'MasterProductController@getProductHistory')->name('getProductHistory');
    Route::get('/product-history/{id}', 'MasterProductController@viewProductHistory')->name('product-history.view');

    //listing mass approve
    Route::POST('/approveNewProducts', 'MasterProductController@approveNewProducts')->name('approveNewProducts');
    Route::POST('/approveEditProducts', 'MasterProductController@approveEditProducts')->name('approveEditProducts');

	// Route::get('/allmasterproductlsts', function () {
    //     return view('cranium.allmasterproductlists');
	// 	})->name('allmasterproductlsts');

	// Route::get('/allmasterproductlsts', function () {
    //     return view('cranium.allmasterproductlists');
	// 	})->name('allmasterproductlsts');
    Route::get('/allmasterproductlsts', 'MasterProductController@allmasterproductlsts')->name('allmasterproductlsts');
    Route::get('/allmasterproductlsts2', 'MasterProductController@allmasterproductlsts2')->name('allmasterproductlsts2');
    Route::get('/allmasterproductlsts2/{filter_id}', 'MasterProductController@allmasterproductlsts2')->name('allmasterproductlsts22');




    Route::post('/getOptimizedMasterproducts', 'MasterProductsOptimizedController@getOptimizedMasterproducts')->name('getOptimizedMasterproducts');
    Route::post('/getOptimizedMasterproductsFilter', 'MasterProductsOptimizedController@masterproductsFilter1')->name('getOptimizedMasterproductsFilter');
   
 Route::get('masterparoducts_approved/{id?}','MasterProductsOptimizedController@allOptimizedMasterproducts')->name('allOptimizedMasterproducts');

	## Master Product Edit Page
	Route::get('/editmasterproduct/{id}/{tab?}', 'MasterProductController@editmasterview')->name('editmasterproduct');
    Route::get('/imagetext/{id}', 'MasterProductController@imagetext')->name('imagetext');
    Route::post('/imagetext_update/{id}', 'MasterProductController@imagetext_update')->name('imagetext_update');
    Route::get('/editmasterrequestview/{id}', 'MasterProductController@editmasterrequestview')->name('editmasterrequestview');
	Route::get('/reeditmasterproduct/{id}', 'MasterProductController@reeditmasterview')->name('reeditmasterproduct');
    Route::get('/ApproveOrRejectProductRequest/{ETIN}/{status}', 'MasterProductController@ApproveOrRejectProductRequest')->name('ApproveOrRejectProductRequest');
    Route::PUT('/ApproveProductRequest/{ETIN}/{status}', 'MasterProductController@ApproveProductRequest')->name('ApproveProductRequest');

	## Master Product Page ADD-Child
	Route::get('/childproduct/{id}', 'MasterProductChildController@addchildview')->name('childproduct');
	Route::put('/addchildproduct', 'MasterProductChildController@insertchild')->name('addchildproduct');

	## Master Product Page ADD-Duplicate
	Route::get('/duplicateproduct/{id}', 'DuplicateMasterProductController@duplicateproduct')->name('duplicateproduct');
	Route::put('/insertduplicateproduct', 'DuplicateMasterProductController@insertduplicateproduct')->name('insertduplicateproduct');
	Route::get('/checkupdatefields', 'DuplicateMasterProductController@checkupdatefields')->name('checkupdatefields');

    # Save as Draft Parent product
    Route::put('/saveAsDraft', 'MasterProductController@saveAsDraft')->name('saveAsDraft');

    # Save as Draft child product
    Route::put('/saveAsDraftChild', 'MasterProductChildController@saveAsDraftChild')->name('saveAsDraftChild');

    Route::put('/updateRequest', 'MasterProductController@updateRequest')->name('updateRequest');

	## Master Product Update
	Route::put('/updatemasterproduct', 'MasterProductController@updatemaster')->name('updatemasterproduct');
	Route::put('/reupdatemasterproduct', 'MasterProductController@reupdatemaster')->name('reupdatemasterproduct');


    Route::get('ProductWizardAjax', 'MasterProductController@ProductWizardAjax')->name('ProductWizardAjax');
    Route::post('getClientChanels', 'MasterProductController@getClientChanels')->name('getClientChanels');
    

	##Get Categories
	Route::get('getsubcategories/{id}','MasterProductController@getsubcategories')->name('getsubcategories');
	Route::get('getsubcategories1/{id}','MasterProductController@getsubcategories1')->name('getsubcategories1');
	Route::get('getsubcategories2/{id}','MasterProductController@getsubcategories2')->name('getsubcategories2');
	Route::get('getsubcategories3/{id}','MasterProductController@getsubcategories3')->name('getsubcategories3');

	Route::get('getbrand/{name}','MasterProductController@getbrand')->name('getbrand');
    Route::get('getmanufacturer/{name}','MasterProductController@getmanufacturer')->name('getmanufacturer');

    ## Sync Suuplier Products with Master Products
    Route::get('/syncDotWithMasterProduct/{id}', 'SupplierDotController@syncDotWithMasterProduct')->name('syncDotWithMasterProduct');
    Route::get('/syncMarsWithMasterProduct/{id}', 'SupplierMarsController@syncMarsWithMasterProduct')->name('syncMarsWithMasterProduct');
    Route::get('/syncMiscWithMasterProduct/{id}', 'SupplierMiscellaneousController@syncMiscWithMasterProduct')->name('syncMiscWithMasterProduct');
    Route::get('/syncKeheWithMasterProduct/{id}', 'SupplierKeheController@syncKeheWithMasterProduct')->name('syncKeheWithMasterProduct');
    Route::get('/syncDryersWithMasterProduct/{id}', 'SupplierDryersController@syncDryersWithMasterProduct')->name('syncDryersWithMasterProduct');
    Route::get('/syncHarsheyWithMasterProduct/{id}', 'SupplierHersheyController@syncHarsheyWithMasterProduct')->name('syncHarsheyWithMasterProduct');

    Route::post('/update_password/{id}', 'UserController@update_password')->name('users.update_password');
    Route::post('/update_user_notification/{id}', 'UserController@update_notification')->name('users.update_user_notification');

    Route::get('/categories/GetCategoryHeirarchy', 'CategoriesController@getcategoryheirarchy')->name('categories.GetCategoryHeirarchy');
    Route::get('/categories/CategoryFromTopToBottom', 'CategoriesController@CategoryFromTopToBottom')->name('categories.CategoryFromTopToBottom');
    Route::get('/categories/sub_category_1/{id}', 'CategoriesController@sub_category_1')->name('categories.sub_category_1');
    Route::post('/categories/sub_category_1_store', 'CategoriesController@sub_category_1_store')->name('categories.sub_category_1_store');
    Route::get('/categories/sub_category_1_edit/{id}', 'CategoriesController@sub_category_1_edit')->name('categories.sub_category_1_edit');
    Route::post('/categories/sub_category_1_update', 'CategoriesController@sub_category_1_update')->name('categories.sub_category_1_update');

    Route::get('/categories/sub_category_2/{id}', 'CategoriesController@sub_category_2')->name('categories.sub_category_2');
    Route::post('/categories/sub_category_2_store', 'CategoriesController@sub_category_2_store')->name('categories.sub_category_2_store');
    Route::get('/categories/sub_category_2_edit/{id}', 'CategoriesController@sub_category_2_edit')->name('categories.sub_category_2_edit');
    Route::post('/categories/sub_category_2_update', 'CategoriesController@sub_category_2_update')->name('categories.sub_category_2_update');

    Route::get('/categories/sub_category_3/{id}', 'CategoriesController@sub_category_3')->name('categories.sub_category_3');
    Route::post('/categories/sub_category_3_store', 'CategoriesController@sub_category_3_store')->name('categories.sub_category_3_store');
    Route::get('/categories/sub_category_3_edit/{id}', 'CategoriesController@sub_category_3_edit')->name('categories.sub_category_3_edit');
    Route::post('/categories/sub_category_3_update', 'CategoriesController@sub_category_3_update')->name('categories.sub_category_3_update');

    Route::get('/categories/add_category/{id?}/{level}', 'CategoriesController@add_category')->name('categories.add_category');


    // Client Management -- channels
    Route::GET('channelList/{client_id}','ClientController@channelList')->name('clients.datatable.channelList');
    Route::GET('createClientChannel/{client_id}','ClientController@createChannel')->name('clients.createChannel');
    Route::POST('storeClientChannel','ClientController@storeChannel')->name('clients.storeChannel');
    Route::GET('editClientChannel/{id}','ClientController@editChannel')->name('clients.editChannel');
    Route::POST('updateClientChannel/{id}','ClientController@updateChannel')->name('clients.updateChannel');
    Route::GET('deleteClientChannel/{id}','ClientController@deleteChannel')->name('clients.deleteChannel');
    Route::GET('get_product_warehouse_qty/{id}','ClientController@get_product_warehouse_qty')->name('clients.get_product_warehouse_qty');
    Route::GET('update_status/{id}/{cl_id}','ClientController@change_channel_status')->name('clients.update_status');
    // Client Management -- Events
    Route::GET('eventList/{client_id}','ClientController@eventList')->name('clients.datatable.eventList');
    Route::GET('createEvent/{client_id}','ClientController@createEvent')->name('clients.createEvent');
    Route::POST('storeEvent','ClientController@storeEvent')->name('clients.storeEvent');
    Route::GET('editEvent/{id}','ClientController@editEvent')->name('clients.editEvent');
    Route::POST('updateEvent/{id}','ClientController@updateEvent')->name('clients.updateEvent');
    Route::GET('deleteEvent/{id}','ClientController@deleteEvent')->name('clients.deleteEvent');
    // Client Management -- Account Notes
    Route::GET('noteList/{client_id}','ClientController@noteList')->name('clients.datatable.noteList');
    Route::GET('createNote/{client_id}','ClientController@createNote')->name('clients.createNote');
    Route::POST('storeNote','ClientController@storeNote')->name('clients.storeNote');
    Route::GET('editNote/{id}','ClientController@editNote')->name('clients.editNote');
    Route::POST('updateNote/{id}','ClientController@updateNote')->name('clients.updateNote');
    Route::GET('deleteNote/{id}','ClientController@deleteNote')->name('clients.deleteNote');
    Route::post('/update_notification/{id}', 'ClientController@update_notification')->name('clients.update_notification');
    Route::post('/WareHouseOrders/{id}', 'ClientController@WareHouseOrders')->name('clients.WareHouseOrders');
    Route::GET('DeleteWarehouseOrder/{id}/{all}/Delete','ClientController@DeleteWarehouseOrder')->name('clients.DeleteWarehouseOrder');
    
    



    // Client Management -- Warehouse & Fulfillment
    Route::GET('warehouseAndFulfillmentList/{client_id}','ClientController@warehouseAndFulfillmentList')->name('clients.datatable.warehouseAndFulfillmentList');
    Route::GET('createWarehouseAndFulfillment/{client_id}','ClientController@createWarehouseAndFulfillment')->name('clients.createWarehouseAndFulfillment');
    Route::POST('storeWarehouseAndFulfillment','ClientController@storeWarehouseAndFulfillment')->name('clients.storeWarehouseAndFulfillment');
    Route::GET('editWarehouseAndFulfillment/{id}','ClientController@editWarehouseAndFulfillment')->name('clients.editWarehouseAndFulfillment');
    Route::POST('updateWarehouseAndFulfillment/{id}','ClientController@updateWarehouseAndFulfillment')->name('clients.updateWarehouseAndFulfillment');
    Route::GET('deleteWarehouseAndFulfillment/{id}','ClientController@deleteWarehouseAndFulfillment')->name('clients.deleteWarehouseAndFulfillment');

    // Client Management -- Billing Tasks & Events
    Route::GET('billing-eventList/{client_id}','ClientController@billingeventList')->name('clients.datatable.billingeventList');
    Route::GET('createBillingEvent/{client_id}','ClientController@createBillingEvent')->name('clients.createBillingEvent');
    Route::POST('storeBillingEvent','ClientController@storeBillingEvent')->name('clients.storeBillingEvent');
    Route::GET('editBillingEvent/{id}','ClientController@editBillingEvent')->name('clients.editBillingEvent');
    Route::POST('updateBillingEvent/{id}','ClientController@updateBillingEvent')->name('clients.updateBillingEvent');
    Route::GET('deleteBillingEvent/{id}','ClientController@deleteBillingEvent')->name('clients.deleteBillingEvent');

    // Client Management -- Billing Account Notes
    Route::GET('billing-noteList/{client_id}','ClientController@billingnoteList')->name('clients.datatable.billingNoteList');
    Route::GET('createBillingNote/{client_id}','ClientController@createBillingNote')->name('clients.createBillingNote');
    Route::POST('storeBillingNote','ClientController@storeBillingNote')->name('clients.storeBillingNote');
    Route::GET('editBillingNote/{id}','ClientController@editBillingNote')->name('clients.editBillingNote');
    Route::POST('updateBillingNote/{id}','ClientController@updateBillingNote')->name('clients.updateBillingNote');
    Route::GET('deleteBillingNote/{id}','ClientController@deleteBillingNote')->name('clients.deleteBillingNote');
    Route::GET('downloadBillingtDocument/{id}','ClientController@downloadBillingDocument')->name('clients.billingDocument.download');

    // Client management -- Contacts
    Route::GET('clientContactList/{client_id}','ClientController@contactList')->name('clients.datatable.clientContactList');
    Route::GET('createClientContact/{client_id}','ClientController@createContact')->name('clients.createContact');
    Route::POST('storeClientContact','ClientController@storeContact')->name('clients.storeContact');
    Route::GET('editClientContact/{id}','ClientController@editContact')->name('clients.editContact');
    Route::POST('updateClientContact/{id}','ClientController@updateContact')->name('clients.updateContact');
    Route::GET('deleteClientContact/{id}','ClientController@deleteContact')->name('clients.deleteContact');
    Route::GET('setClientPrimaryContact','ClientController@setPrimaryContact')->name('clients.setPrimaryContact');

    // Client management -- Upload Documents
    Route::GET('documentList/{client_id}','ClientController@documentList')->name('clients.datatable.documentList');
    Route::GET('createClientDocument/{client_id}','ClientController@createDocument')->name('clients.createDocument');
    Route::POST('storeClientDocument','ClientController@storeDocument')->name('clients.storeDocument');
    Route::GET('downloadClientDocument/{id}','ClientController@downloadDocument')->name('clients.document.download');
    Route::GET('deleteClientDocument/{id}','ClientController@deleteDocument')->name('clients.deleteDocument');

     // Client management -- Links
     Route::GET('linkList/{client_id}','ClientController@linkList')->name('clients.datatable.linkList');
     Route::GET('createLink/{client_id}','ClientController@createLink')->name('clients.createLink');
     Route::POST('storeLink','ClientController@storeLink')->name('clients.storeLink');
     Route::GET('editLink/{id}','ClientController@editLink')->name('clients.editLink');
     Route::POST('updateLink/{id}','ClientController@updateLink')->name('clients.updateLink');
     Route::GET('deleteLink/{id}','ClientController@deleteLink')->name('clients.deleteLink');
    // Client management
    Route::GET('updateWarehouseAssigned','ClientController@updateWarehouseAssigned')->name('clients.updateWarehouseAssigned');
    Route::POST('updateClientManagementDetails/{client_id}','ClientController@updateClientManagementDetails')->name('clients.updateClientManagementDetails');
    Route::GET('upload_bulk_product/{client_id}','ClientController@upload_bulk_product')->name('upload_bulk_product');
    Route::GET('map_client_product_file/{client_id}','ClientController@map_client_product_file')->name('map_client_product_file');


    Route::GET('clients/clients_orders','ClientController@client_orders')->name('clients.clients.orders');
    Route::GET('clients/clients_products','ClientController@client_products_management')->name('clients.clients.products');
    Route::GET('clients/client_warehouse_orders','ClientController@client_warehouse_orders')->name('clients.clients.client_warehouse_orders');
    Route::GET('clients/client_contacts','ClientController@client_contacts')->name('clients.clients.client_contacts');
    Route::GET('clients/clients_documents','ClientController@clients_documents')->name('clients.clients.clients_documents');
    Route::GET('clients/clients_information','ClientController@clients_information')->name('clients.clients.clients_information');
    Route::GET('clients/clients_reports','ClientController@clients_reports')->name('clients.clients.clients_reports');
    
    
    
    
    // Supplier
    Route::POST('suppliers/updateConfig/{id}','SupplierController@updateSupplierConfig')->name('suppliers.updateSupplierConfig');
    Route::get('/getmasterproductsbysupplier/{supplier_id?}', 'SupplierController@getmasterproductsbysupplier')->name('getmasterproductsbysupplier');
    Route::get('suppliers/upload_bulk_product/{supplier_id}', 'SupplierController@upload_bulk_product')->name('suppliers.upload_bulk_product');
    Route::post('suppliers/upload_supplier_product', 'SupplierController@upload_supplier_product')->name('suppliers.upload_supplier_product');

    Route::get('/getpackagingbysupplier/{supplier_id?}', 'SupplierController@getPackagingBySupplier')->name('getpackagingbysupplier');
    Route::get('/addpackagematerial/{id}', 'SupplierController@createPackageMaterial')->name('addpackagematerial');
    Route::post('/addpackagematerialstore', 'SupplierController@addPackageMaterialStore')->name('addpackagematerialstore');
    Route::get('/editpackagematerial/{id}', 'SupplierController@editpackagematerial')->name('editpackagematerial');
    Route::post('/updatepackagematerial/{id}', 'SupplierController@updatePackageMaterial')->name('updatepackagematerial');
    Route::delete('/deletepackagematerial', 'SupplierController@destroyPackageMaterial')->name('destroypackagematerial');

    Route::get('/listpackagingmatirial', 'SupplierController@listPackagingMatirial')->name('listpackagingmatirial.index');
    Route::get('/editpackagemateriallist/{id}', 'SupplierController@editpackagemateriallist')->name('editpackagemateriallist');

    Route::get('/packagekitcreate/create/{id}', 'PackageKitController@create')->name('packagekitcreate');
    Route::get('/packagekitcreate/edit/{id}', 'PackageKitController@edit')->name('packagekitedit');
    Route::get('/packagekitcreate/packagekiteditlistedit/{id}', 'PackageKitController@packagekiteditlist')->name('packagekiteditlist');

    Route::get('/cyclecount', 'WarehouseManagmentController@cycleCountIndex')->name('warehousemanagment.cyclecount.index');
    Route::get('datatable/cyclecount_list', 'WarehouseManagmentController@cycleCountList')->name('datatable.cyclecountlist');
    Route::get('/cyclecount/create', 'WarehouseManagmentController@cycleCountcreate')->name('warehousemanagment.cyclecount.create');
    Route::get('/cyclecount/edit/{row_id}', 'WarehouseManagmentController@cycleCountEdit')->name('warehousemanagment.cyclecount.edit');
    Route::get('/cyclecount/approval/{row_id}', 'WarehouseManagmentController@cycleCountApproval')->name('warehousemanagment.cyclecount.approval');
    Route::get('/cyclecount/complatelist/{row_id}', 'WarehouseManagmentController@cycleComplatelist')->name('warehousemanagment.cyclecount.complatelist');
    Route::post('/cyclecount/awaitapprovedproducts', 'WarehouseManagmentController@awaitapprovedproducts')->name('awaitapprovedproducts');
    Route::post('/cyclecount/cyclecomplatelist', 'WarehouseManagmentController@cyclecomplatelistDatatable')->name('cyclecomplatelist');
    Route::post('/cyclecount/approveawaitcyclecountproduct', 'WarehouseManagmentController@approveawaitcyclecountproduct')->name('approveawaitcyclecountproduct');
    Route::POST('/approvecyclecount', 'WarehouseManagmentController@approveNewProducts')->name('approvecyclecount');
    Route::post('/getactivemasterproducts', 'WarehouseManagmentController@getactivemasterproducts')->name('getactivemasterproducts');
    Route::post('/productsfilterwarehouse', 'WarehouseManagmentController@getfillterProduct')->name('productsfilterwarehouse');
    Route::post('/locationcycle/', 'WarehouseManagmentController@locationCycle')->name('locationcycle');
    Route::post('/locationcyclenew/', 'WarehouseManagmentController@locationcyclenew')->name('locationcyclenew');
    Route::post('/locationapprovedproducts', 'WarehouseManagmentController@getLocationApprovedProducts')->name('locationapprovedproducts');
    Route::get('/getwarehousesvg/', 'WarehouseManagmentController@getWarehouseSvg')->name('getwarehousesvg');
    Route::post('/getlocaionwarehousewise/', 'WarehouseManagmentController@getlocaionwarehousewise')->name('getlocaionwarehousewise');
    Route::post('/updatesummary', 'WarehouseManagmentController@updateSummary')->name('update_summary');
    Route::post('/changeuser', 'WarehouseManagmentController@changeuser')->name('changeuser');
    Route::post('/cyclecount/delete/{row_id}', 'WarehouseManagmentController@deleteSummary')->name('warehousemanagment.cyclecount.delete');

    Route::get('/icecharttemplate', 'IceChartController@index')->name('icechart.index');
    Route::get('/icecharttemplate/create', 'IceChartController@create')->name('icechart.create');
    Route::post('/icecharttemplate/store', 'IceChartController@store')->name('icechart.store');
    Route::get('/icecharttemplate/edit/{id}', 'IceChartController@edit')->name('icechart.edit');
    Route::post('/icecharttemplate/update/{id}', 'IceChartController@update')->name('icechart.update');
    Route::post('/icecharttemplate/updatewarehousetemplate', 'IceChartController@updateWarehouseTemplate')->name('icechart.updatewarehousetemplate');
    Route::get('/icecharttemplate/exportwarehousetemplate/{id}/{type}', 'IceChartController@exportWarehouseTemplate')->name('icechart.exportWarehouseTemplate');

    Route::get('/gelpacktemplate', 'GelPackController@index')->name('gelpack.index');
    Route::get('/gelpacktemplate/create', 'GelPackController@create')->name('gelpack.create');
    Route::post('/gelpacktemplate/store', 'GelPackController@store')->name('gelpack.store');
    Route::get('/gelpacktemplate/edit/{id}', 'GelPackController@edit')->name('gelpack.edit');
    Route::post('/gelpacktemplate/update/{id}', 'GelPackController@update')->name('gelpack.update');
    Route::post('/gelpacktemplate/updatewarehousetemplate', 'GelPackController@updateWarehouseTemplate')->name('gelpack.updatewarehousetemplate');

    Route::get('/getrestockproductsetting', 'SettingController@getrestockproductsetting')->name('setting.getrestockproductsetting');
    Route::post('/getrestockproductsetting', 'SettingController@restockproductsettingstore')->name('setting.getrestockproductsettingstore');

    Route::get('/getpackagingcomponants', 'PackagingComponantSettingController@index')->name('packagingcomponant.index');
    Route::get('datatable/packagingcompnents', 'PackagingComponantSettingController@getPackagingComponents')->name('packagingcomponant.packagingcompnentslist');
    Route::get('packagingcomponants/edit/{id}','PackagingComponantSettingController@editPackagingComponantSetting')->name('packagingcomponant.edit');
    Route::get('gettempcomponents','PackagingComponantSettingController@getTempComponents')->name('packagingcomponant.gettempcomponents');
    Route::post('/updatetempcomponents/update/{id}', 'PackagingComponantSettingController@update')->name('packagingcomponants.update');
    Route::get('/new_custom_outer', 'PackagingComponantSettingController@new_custom_outer')->name('packagingcomponants.new_custom_outer');
    Route::get('/edit_custom_outer/{map_id}', 'PackagingComponantSettingController@edit_custom_outer')->name('packagingcomponants.edit_custom_outer');
    Route::get('/get_client_channels_and_product/{client_id}', 'PackagingComponantSettingController@get_client_channels_and_product')->name('packagingcomponants.get_client_channels_and_product');
    Route::post('/store_custom_outer', 'PackagingComponantSettingController@store_custom_outer')->name('packagingcomponants.store_custom_outer');

    Route::get('/dragdemo', 'PriceGroupController@drag')->name('drag');
    Route::get('/pricegrouplist', 'PriceGroupController@index')->name('pricegroup.index');
    Route::get('datatable/pricegrouplist', 'PriceGroupController@getPriceGroups')->name('pricegroup.pricegrouplist');
    Route::get('/pricegroup/create', 'PriceGroupController@create')->name('pricegroup.create');
    Route::post('/pricegroup/store', 'PriceGroupController@store')->name('pricegroup.store');
    Route::get('/pricegroup/edit/{id}', 'PriceGroupController@edit')->name('pricegroup.edit');
    Route::post('/pricegroup/update/{id}', 'PriceGroupController@update')->name('pricegroup.update');
    Route::get('/pricegroup/delete/{id}', 'PriceGroupController@delete')->name('pricegroup.delete');

    Route::get('/kitproductinventorylist', 'ProductInventoryController@kitIndex')->name('kitproductinventory.index');
    Route::get('datatable/productkitinventorylist', 'ProductInventoryController@getProductKitInventorylist')->name('productinventory.productkitinventorylist');
    Route::get('/warehouselist', 'ProductInventoryController@warehouselist')->name('productinventory.warehouse');

    Route::get('/productinventorylist', 'ProductInventoryController@index')->name('productinventory.index');
    Route::get('datatable/productinventorylist', 'ProductInventoryController@getProductInventorylist')->name('productinventory.productinventorylist');
    Route::get('datatable/childproductinventorylist', 'ProductInventoryController@getChildProductInventorylist')->name('productinventory.chilproductinventorylist');
    Route::post('/productinventory', 'ProductInventoryController@store')->name('productinventory.store');
    Route::post('/editproductinventory', 'ProductInventoryController@editProductInventory')->name('productinventory.edit');
    Route::post('/updateproductinventory', 'ProductInventoryController@update')->name('productinventory.update');
    Route::get('/exportInv', 'ProductInventoryController@exportInv')->name('productinventory.exportInv');
    // Route::GET('misc-cost-values','MiscCostValuesController@misc_cost_values')->name('misc_cost_values');
    // Route::GET('add-new-misc-cost','MiscCostValuesController@addnewmisccost')->name('addnewmisccost');

    Route::get('/materialtype', 'MaterialTypeController@index')->name('materialtype.index');
    Route::post('/materialtype', 'MaterialTypeController@store')->name('materialtype.store');
    Route::post('/updatematerialtype', 'MaterialTypeController@update')->name('materialtype.update');
    Route::delete('/destroy', 'MaterialTypeController@destroy')->name('materialtype.destroy');

    Route::get('/billingnotes', 'BillingNoteController@index')->name('billingnotes.index');
    Route::post('/billingnotes', 'BillingNoteController@store')->name('billingnotes.store');
    Route::post('/updatebillingnotes', 'BillingNoteController@update')->name('billingnotes.update');
    Route::delete('/billingnotes/destroy', 'BillingNoteController@destroy')->name('billingnotes.destroy');
    Route::get('datatable/billingnotes_list', 'BillingNoteController@billingnotesList')->name('datatable.billingnotesList');

    // Supplier management -- Contacts
    Route::GET('supplierContactList/{supplier_id}','SupplierController@contactList')->name('suppliers.datatable.contactList');
    Route::GET('createSupplierContact/{supplier_id}','SupplierController@createContact')->name('suppliers.createSupplierContact');
    Route::POST('storeSupplierContact','SupplierController@storeContact')->name('suppliers.storeContact');
    Route::GET('editSupplierContact/{id}','SupplierController@editContact')->name('suppliers.editContact');
    Route::POST('updateSupplierContact/{id}','SupplierController@updateContact')->name('suppliers.updateContact');
    Route::GET('deleteSupplierContact/{id}','SupplierController@deleteContact')->name('suppliers.deleteContact');
    Route::GET('setPrimaryContactSupplier','SupplierController@setPrimaryContact')->name('suppliers.setPrimaryContact');
    // Supplier management -- Contacts
    Route::GET('supplierNoteList/{supplier_id}','SupplierController@noteList')->name('suppliers.datatable.noteList');
    Route::GET('createSupplierNote/{supplier_id}','SupplierController@createNote')->name('suppliers.createNote');
    Route::POST('storeSupplierNote','SupplierController@storeNote')->name('suppliers.storeNote');
    Route::GET('editSupplierNote/{id}','SupplierController@editNote')->name('suppliers.editNote');
    Route::POST('updateSupplierNote/{id}','SupplierController@updateNote')->name('suppliers.updateNote');
    Route::GET('deleteSupplierNote/{id}','SupplierController@deleteNote')->name('suppliers.deleteNote');
    // Supplier management -- Upload Documents
    Route::GET('supplierDocumentList/{supplier_id}','SupplierController@documentList')->name('suppliers.datatable.documentList');
    Route::GET('createSupplierDocument/{supplier_id}','SupplierController@createDocument')->name('suppliers.createDocument');
    Route::POST('storeSupplierDocument','SupplierController@storeDocument')->name('suppliers.storeDocument');
    Route::GET('downloadSupplierDocument/{id}','SupplierController@downloadDocument')->name('suppliers.document.download');
    Route::GET('deleteSupplierDocument/{id}','SupplierController@deleteDocument')->name('suppliers.deleteDocument');

    Route::GET('map_supplier_product_file/{sup_id}','SupplierController@map_supplier_product_file')->name('map_supplier_product_file');

    Route::post('MapSupplierProduct','SupplierController@MapSupplierProduct')->name('suppliers.MapSupplierProduct');


    Route::post('saveSupplierImportHeaders','SupplierController@saveSupplierImportHeaders')->name('suppliers.saveSupplierImportHeaders');
    Route::get('delete_supplier_product_header/{id}','SupplierController@delete_supplier_product_header')->name('suppliers.delete_supplier_product_header');

     // Supplier management -- Links
     Route::GET('supplierLinkList/{supplier_id}','SupplierController@linkList')->name('suppliers.datatable.linkList');
     Route::GET('createSupplierLink/{supplier_id}','SupplierController@createLink')->name('suppliers.createLink');
     Route::POST('storeSupplierLink','SupplierController@storeLink')->name('suppliers.storeLink');
     Route::GET('editSupplierLink/{id}','SupplierController@editLink')->name('suppliers.editLink');
     Route::POST('updateSupplierLink/{id}','SupplierController@updateLink')->name('suppliers.updateLink');
     Route::GET('deleteSupplierLink/{id}','SupplierController@deleteLink')->name('suppliers.deleteLink');

     // Carrier Management
     Route::GET('carrierFeeList/{carrier_id}','CarriersController@carrierFeeList')->name('carriers.datatable.carrierFeeList');
     Route::post('carriers/updateConfig','CarriersController@updateConfig')->name('carriers.updateConfig');
     Route::get('carriers/dynamic-fees/create/{carrier_id}','CarriersController@createFee')->name('carriers.createFee');
     Route::post('carriers/dynamic-fees/store','CarriersController@storeFee')->name('carriers.storeFee');
     Route::get('carriers/dynamic-fees/edit/{id}','CarriersController@editFee')->name('carriers.editFee');
     Route::post('carriers/dynamic-fees/update/{id}','CarriersController@updateFee')->name('carriers.updateFee');
     Route::GET('carriers/dynamic-fees/delete/{id}','CarriersController@destroyFee')->name('carriers.destroyFee');
     Route::GET('carriers/dynamic-fees/setPrimaryFee','CarriersController@setPrimaryFee')->name('carriers.setPrimaryFee');

     Route::GET('carrierSurchargeList/{carrier_id}','CarriersController@carrierSurchargeList')->name('carriers.datatable.surchargeList');
     Route::get('carriers/surcharge/create/{carrier_id}','CarriersController@createSurcharge')->name('carriers.createSurcharge');
     Route::post('carriers/surcharge/store','CarriersController@storeSurcharge')->name('carriers.storeSurcharge');
     Route::get('carriers/surcharge/edit/{id}','CarriersController@editSurcharge')->name('carriers.editSurcharge');
     Route::post('carriers/surcharge/update/{id}','CarriersController@updateSurcharge')->name('carriers.updateSurcharge');
     Route::get('carriers/surcharge/delete/{id}','CarriersController@destroySurcharge')->name('carriers.destroySurcharge');

     Route::GET('CarrierAccounts','CarriersController@CarrierAccounts')->name('carriers.datatable.CarrierAccounts');
     Route::GET('carrieraccount/{id}','CarriersController@createCarrierAccount')->name('carriers.createCarrierAccount');
     Route::POST('carrieraccount/store','CarriersController@createCarrierAccountStore')->name('carriers.createCarrierAccountStore');
     Route::delete('/carrieraccount/destroy', 'CarriersController@carrierAccountDestroy')->name('carrieraccount.destroy');
    Route::GET('carrierOrderAssignment/{id}','CarriersController@carrierOrderAssignment')->name('carriers.carrierOrderAssignment');
    Route::post('storeAssignedOrderAccounts','CarriersController@storeAssignedOrderAccounts')->name('carriers.storeAssignedOrderAccounts');
    Route::get('GetDefaultOrderAccountAssignments','CarriersController@GetDefaultOrderAccountAssignments')->name('carriers.GetDefaultOrderAccountAssignments');
    Route::GET('CarrierAccountsAssignments','CarriersController@CarrierAccountsAssignments')->name('carriers.datatable.CarrierAccountsAssignments');
     Route::delete('/carrieraccountassigment/destroy', 'CarriersController@deleteCarrierAccountAssigment')->name('carrieraccount.deleteCarrierAccountAssigment');
     Route::GET('carrierallserviceconf','CarriersController@CarrierAllServiceConf')->name('carriers.datatable.CarrierAllServiceConf');
    Route::GET('carrierserviceconf/{id}','CarriersController@carrierServiceConf')->name('carriers.carrierServiceConf');
    Route::GET('addcarrierserviceconf','CarriersController@addcarrierServiceConf')->name('carriers.addcarrierServiceConf');
    Route::post('storecarriershipping','CarriersController@storeCarriershipping')->name('carriers.storeCarriershipping');
    Route::GET('carrierorderautomaticupgrades/{id}','CarriersController@carrierOrderAutomaticUpgrades')->name('carriers.carrierorderautomaticupgrades');
    Route::post('getClientChanelsCarrier', 'CarriersController@getClientChanels')->name('getClientChanelsCarrier');
    Route::GET('getautomaticupgrades','CarriersController@GetAutomaticupgrades')->name('carriers.datatable.GetAutomaticupgrades');
    Route::post('storeorderupgrades','CarriersController@storeOrderUpgrades')->name('carriers.storeOrderUpgrades');
    Route::GET('getdropdown','CarriersController@getDropdown')->name('carriers.getDropdown');
    Route::delete('/orderUpgrade/destroy', 'CarriersController@deleteOrderUpgrade')->name('carrieraccount.deleteOrderUpgrade');

     // Carrier Management -- Contacts
    Route::GET('carrierContactList/{carrier_id}','CarriersController@contactList')->name('carriers.datatable.contactList');
    Route::GET('createCarrierContact/{carrier_id}','CarriersController@createContact')->name('carriers.createContact');
    Route::POST('storeCarrierContact','CarriersController@storeContact')->name('carriers.storeContact');
    Route::GET('editCarrierContact/{id}','CarriersController@editContact')->name('carriers.editContact');
    Route::POST('updateCarrierContact/{id}','CarriersController@updateContact')->name('carriers.updateContact');
    Route::GET('deleteCarrierContact/{id}','CarriersController@deleteContact')->name('carriers.deleteContact');
    Route::GET('setCarrierPrimaryContact','CarriersController@setPrimaryContact')->name('carriers.setPrimaryContact');
    // Account Notes
    Route::GET('carrierNoteList/{carrier_id}','CarriersController@noteList')->name('carriers.datatable.noteList');
    Route::GET('createCarrierNote/{carrier_id}','CarriersController@createNote')->name('carriers.createNote');
    Route::POST('storeCarrierNote','CarriersController@storeNote')->name('carriers.storeNote');
    Route::GET('editCarrierNote/{id}','CarriersController@editNote')->name('carriers.editNote');
    Route::POST('updateCarrierNote/{id}','CarriersController@updateNote')->name('carriers.updateNote');
    Route::GET('deleteCarrierNote/{id}','CarriersController@deleteNote')->name('carriers.deleteNote');
    //Upload Documents
    Route::GET('carrierDocumentList/{carrier_id}','CarriersController@documentList')->name('carriers.datatable.documentList');
    Route::GET('createCarrierDocument/{carrier_id}','CarriersController@createDocument')->name('carriers.createDocument');
    Route::POST('storeCarrierDocument','CarriersController@storeDocument')->name('carriers.storeDocument');
    Route::GET('downloadCarrierDocument/{id}','CarriersController@downloadDocument')->name('carriers.document.download');
    Route::GET('deleteCarrierDocument/{id}','CarriersController@deleteDocument')->name('carriers.deleteDocument');
    // Carrier management -- Links
    Route::GET('carrierLinkList/{carrier_id}','CarriersController@linkList')->name('carriers.datatable.linkList');
    Route::GET('createCarrierLink/{carrier_id}','CarriersController@createLink')->name('carriers.createLink');
    Route::POST('storeCarrierLink','CarriersController@storeLink')->name('carriers.storeLink');
    Route::GET('editCarrierLink/{id}','CarriersController@editLink')->name('carriers.editLink');
    Route::POST('updateCarrierLink/{id}','CarriersController@updateLink')->name('carriers.updateLink');
    Route::GET('deleteCarrierLink/{id}','CarriersController@deleteLink')->name('carriers.deleteLink');
    Route::get('/locationtypes', 'LocationTypeController@index')->name('locationtypes.index');
    Route::post('/locationtypes', 'LocationTypeController@store')->name('locationtypes.store');
    Route::post('/updatelocationtypes', 'LocationTypeController@update')->name('locationtypes.update');
    Route::delete('/locationtypes/destroy', 'LocationTypeController@destroy')->name('locationtypes.destroy');
    Route::get('datatable/locationtypes_list', 'LocationTypeController@locationtypesList')->name('datatable.locationtypesList');



    //slack
    Route::get('/SlackDirectApproveProduct/{id}','MasterProductController@SlackDirectApproveProduct')->name('SlackDirectApproveProduct');


    Route::post('product_listing_filters/save_listing_order', 'ProductListingFilterController@save_listing_order')->name('product_listing_filters.save_listing_order');
    
    

    

    //smart Filters
    Route::post('smart_filters/update_smart_filter', 'SmartFiltersController@update_smart_filter')->name('smart_filters.update_smart_filter');
    
    Route::get('product_listing_filters/create/{type}', 'ProductListingFilterController@create')->name('product_listing_filters.create');
    Route::post('product_listing_filters/store', 'ProductListingFilterController@store')->name('product_listing_filters.store');
    Route::get('product_listing_filters/{id}/edit', 'ProductListingFilterController@edit')->name('product_listing_filters.edit');
    Route::patch('product_listing_filters/update/{id}', 'ProductListingFilterController@update')->name('product_listing_filters.update');
    Route::delete('product_listing_filters/destroy/{id}', 'ProductListingFilterController@destroy')->name('product_listing_filters.destroy');
    Route::get('product_listing_filters/{id?}', 'ProductListingFilterController@index')->name('product_listing_filters.index');


    //Routes - Fzl
    Route::resources([
        'suppliers' => 'SupplierController',
        'carriers' => 'CarriersController',
        'clients' => 'ClientController',
        'users' => 'UserController',
        'categories' => 'CategoriesController',
        'product_temperature' => 'ProductTemperatureController',
        'country' => 'CountryOfOriginController',
        'time_zones' => 'TimeZoneController',
        'unit_description' => 'UnitDescriptionController',
        'product_tags' => 'ProductTagController',
        'smart_filters' => 'SmartFiltersController',
        'unit_sizes' => 'UnitSizeController',
        'prop_ingredients' => 'PropIngredientsController',
        'manufacturer' => 'ManufacturerController',
        'product_type' => 'ProductTypeController',
        'supplier_status' => 'SupplierStatusController',
        'brands' => 'BrandController',
        'etailer_availability' => 'ETailerController',
        'item_form_description' => 'ItemFormDescController',
        'image_type' => 'ImageTypeController',
        'allergens' => 'AllergensController',
        'kit_description' => 'KitDescriptionController',
        'parentproductwizard' => 'ParentProductWizardController',
        'product_statuses' => 'ProductStatusController',
        'kits' => 'MasterKitProductsController',
        'modules' => 'ModulesController',
        'roles' => 'RolesController',
        'ticket' => 'ProductTicketController',
        'purchase_order' => 'PurchaseOrderController',
        'misc_cost' => 'MiscCostValuesController',
    ]);
    

    //PurchaseOrderController
    Route::get('purchase_order/create/{id}/{type}', 'PurchaseOrderController@create')->name('purchase_order.create_purchase_order');
    Route::get('purchase_order/edit/{id}/{sumId}/{type}', 'PurchaseOrderController@edit')->name('purchase_order.edit_purchase_order');
    Route::get('datatable/PurchaseOrderProducts', 'PurchaseOrderController@PurchaseOrderProducts')->name('datatable.PurchaseOrderProducts');
    Route::post('/purchase_order/saveAsDraft', 'PurchaseOrderController@saveDraftPo')->name('purchase_order.save_draft');
    Route::post('/purchase_order/submit_po', 'PurchaseOrderController@submitPurchaseOrder')->name('purchase_order.submit_po');
    Route::get('datatable/SavedPurchaseOrderProducts', 'PurchaseOrderController@SavedPurchaseOrderProducts')->name('datatable.SavedPurchaseOrderProducts');
    Route::get('datatable/purchaseSummary/{supplierId}', 'PurchaseOrderController@getPurchaseSummary')->name('datatable.GetPurchaseSummary');
    Route::get('datatable/purchaseSummary/{supplierId}', 'PurchaseOrderController@getPurchaseSummary')->name('datatable.GetPurchaseSummary');
    Route::get('datatable/SavedPurchaseOrderProductsForAsn', 'PurchaseOrderController@SavedPurchaseOrderProductsForAsn')->name('datatable.SavedPurchaseOrderProductsForAsn');
    Route::get('purchase_order/editasnbol/{id}/{sumId}/{type}', 'PurchaseOrderController@editAsnBol')->name('purchase_order.edit_asn_bol');
    Route::post('/purchase_order/submit_asn_bol', 'PurchaseOrderController@saveAsnBol')->name('purchase_order.submit_asn_bol');
    Route::get('purchase_order/{order}/get_lot_and_exp', 'PurchaseOrderController@get_lot_and_exp')->name('purchase_order.get_lot_and_exp');
    Route::post('purchase_order/SaveLotAndExp/{order}', 'PurchaseOrderController@SaveLotAndExp')->name('purchase_order.SaveLotAndExp');


    //Product tickets
    Route::get('datatable/product_ticket_list/{id}/{type}', 'ProductTicketController@ProductTicketsList')->name('datatable.ProductTicketsList');
    Route::get('ticket/get_chat/{id}', 'ProductTicketController@get_chat')->name('ticket.get_chat');
    Route::POST('ticket/message/store', 'ProductTicketController@save_message')->name('ticket.message.store');
    Route::get('ticket/close_ticket/{id}', 'ProductTicketController@close_ticket')->name('ticket.close_ticket');
    Route::get('ticket/reopen_ticket/{id}', 'ProductTicketController@reopen_ticket')->name('ticket.reopen_ticket');

    //allergens
    Route::get('datatable/allergens_list', 'AllergensController@allergenslist')->name('datatable.allergensList');

    //KitDescriptionController
    Route::get('datatable/KitDescriptionList', 'KitDescriptionController@KitDescriptionList')->name('datatable.KitDescriptionList');


    //item_form_description
    Route::get('datatable/image_type_list', 'ImageTypeController@imagetypelist')->name('datatable.imageTypeList');

    //item_form_description
    Route::get('datatable/item_form_description_list', 'ItemFormDescController@itemformdesclist')->name('datatable.itemFormDescList');

    //manufacturer
    Route::get('datatable/manufacture_list', 'ManufacturerController@manufacturerlist')->name('datatable.manufacturerList');

    //product type
    Route::get('datatable/product_type_list', 'ProductTypeController@producttypelist')->name('datatable.productTypeList');

    //supplier status
    Route::get('datatable/supplier_status_list', 'SupplierStatusController@supplierstatuslist')->name('datatable.supplierStatusList');

    //brands
    Route::get('datatable/brand_list', 'BrandController@brandlist')->name('datatable.brandList');

    //etailer_availability
    Route::get('datatable/etailer_list', 'ETailerController@etailerlist')->name('datatable.etailerList');

    ##pull data from s3 bucket
    Route::get('s3_bucket_connect', 'MasterProductController@s3_bucket_connect')->name('s3_bucket_connect');

    #materialType
    Route::get('datatable/material_type_list', 'MaterialTypeController@materialTypeList')->name('datatable.materialtypelist');

    #misc cost value
    Route::get('datatable/getmisccostvalues', 'MiscCostValuesController@getmisccostvalues')->name('datatable.getmisccostvalues');
    Route::GET('misc_cost/deleteCost/{id}','MiscCostValuesController@deleteCost')->name('misc_cost.deleteCost');

    ##Reports
    Route::get('master_product_daily_report', 'ReportsController@master_product_daily_report')->name('master_product_daily_report');
    Route::get('markout_report', 'ReportsController@markout_report')->name('markout_report');
    Route::get('markout_datatables', 'ReportsController@markout_datatables')->name('datatable.markout_datatables');
    Route::get('markout_export', 'ReportsController@markout_export')->name('markout_export');
    

    ## Product Selection
    Route::get('/datatable/ProductTemperatureList/', 'ProductTemperatureController@producttemperaturelist')->name('datatable.ProductTemperatureList');
    Route::get('/datatable/CountryList/', 'CountryOfOriginController@countrylist')->name('datatable.CountryList');
    Route::get('/datatable/TimeZoneList/', 'TimeZoneController@TimeZonelist')->name('datatable.TimeZoneList');
    Route::get('/datatable/UnitDescriptionList/', 'UnitDescriptionController@unitdescriptionlist')->name('datatable.UnitDescriptionList');
    Route::get('/datatable/ProductTagsList/', 'ProductTagController@producttagslist')->name('datatable.ProductTagsList');

    Route::get('/datatable/UnitSizeList/', 'UnitSizeController@unitsizelist')->name('datatable.UnitSizeList');
    Route::get('/datatable/PropIngredientsList/', 'PropIngredientsController@propingredientslist')->name('datatable.PropIngredientsList');

    ## Feedback
    Route::get('feedbacks', 'ReportsController@feedbacks')->name('feedbacks');
    Route::post('/submit_feedback', 'HomeController@submit_feedback')->name('submit_feedback');

    Route::post('/submit_help', 'HomeController@submit_help')->name('submit_help');
    Route::get('/get_help', 'HomeController@get_help')->name('get_help');
    Route::get('/download_help_csv/{type}', 'HomeController@download_help_csv')->name('download_help_csv');
    Route::get('/get_help_details/{help_id}', 'HomeController@get_help_details')->name('get_help_details');
    Route::put('/resolve_help/{help_id}', 'HomeController@resolve_help')->name('resolve_help');

    #Product Selection New Requests
    Route::get('new_requests', 'ReportsController@new_requests')->name('new_requests');


    ## News Feed
	Route::get('/news_feed', function () {
        if(ReadWriteAccess('NewsFeed') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('cranium.newsfeed.news_feed');
    });
    Route::put('insertfeed', 'NewsFeedController@insertfeed')->name('insertfeed');
	Route::get('/getnewsfeed', 'NewsFeedController@getnewsfeed')->name('getnewsfeed');
	Route::get('/news_feed/{id}', 'NewsFeedController@editnewsfeed')->name('editnewsfeed');

	// Parent Product Wizard
    Route::post('add_parentproductwizard', 'ParentProductWizardController@add')->name('add_parentproductwizard');
    Route::put('/addmasterproductwizard', 'MasterProductController@insertnewmasterwizard')->name('addmasterproductwizard');

    // Product Selection
    Route::get('datatable/productstatusList', 'ProductStatusController@productstatusList')->name('datatable.productstatusList');



    ## Add New Kit Product  ---------
    Route::get('/GetAllParentApprovedProducts', 'MasterKitProductsController@GetAllParentApprovedProducts')->name('GetAllParentApprovedProducts');
    Route::post('/GetSelectedProductForKit', 'MasterKitProductsController@GetSelectedProductForKit')->name('GetSelectedProductForKit');
    Route::get('kits/edit_request/{id}', 'MasterKitProductsController@edit_request')->name('kits.edit_request');
    Route::post('kits/update_request/{id}', 'MasterKitProductsController@update_request')->name('kits.update_request');
    Route::post('kits/ApproveKitRequest/{id}', 'MasterKitProductsController@ApproveKitRequest')->name('kits.ApproveKitRequest');
    Route::get('/ApproveKit/{id}', 'MasterKitProductsController@ApproveKit')->name('ApproveKit');
    Route::get('/ETINAutoComplete', 'MasterKitProductsController@ETINAutoComplete')->name('ETINAutoComplete');

    Route::post('modules/menu_save', 'ModulesController@menu_save')->name('modules.menu_save');
    Route::post('modules/menu_delete', 'ModulesController@menu_delete')->name('modules.menu_delete');

    Route::get('roles/RolePermissions/{id}/{status}', 'RolesController@RolePermissions')->name('roles.RolePermissions');
    Route::post('roles/display_access', 'RolesController@display_access')->name('roles.display_access');

    Route::post('roles/save_menus_order', 'rolescontroller@save_menus_order')->name('roles.save_menus_order');
    Route::post('roles/save_functions_order', 'rolescontroller@save_functions_order')->name('roles.save_functions_order');
    Route::post('roles/save_notifications_order', 'rolescontroller@save_notifications_order')->name('roles.save_notifications_order');
    Route::post('roles/save_wms_order', 'rolescontroller@save_wms_order')->name('roles.save_wms_order');


    // Scripts to insert UPC Zone Data
    Route::GET('get_rates_listing/{table_name}','ScriptsController@get_rates_listing')->name('get_rates_listing');
    Route::GET('uploadExcelView','ScriptsController@index')->name('uploadExcelView');
    Route::POST('/upload_ups_zone_rates_to_table', 'ScriptsController@upload_ups_zone_rates_to_table')->name('upload_ups_zone_rates_to_table');

    //Scripts to update Master Product Columns
    Route::get('script/update_mp_brand_column', 'MasterProductUpdateScriptsController@update_mp_brand_column')->name('script.update_mp_brand_column');
    Route::get('script/update_mp_supplier_column', 'MasterProductUpdateScriptsController@update_mp_supplier_column')->name('script.update_mp_supplier_column');
    Route::get('script/update_mp_item_form_desc_column', 'MasterProductUpdateScriptsController@update_mp_item_form_desc_column')->name('script.update_mp_item_form_desc_column');
    Route::get('script/update_mp_product_type_column', 'MasterProductUpdateScriptsController@update_mp_product_type_column')->name('script.update_mp_product_type_column');
    Route::get('script/update_mp_unit_desc_column', 'MasterProductUpdateScriptsController@update_mp_unit_desc_column')->name('script.update_mp_unit_desc_column');
    Route::get('script/update_mp_etailer_availability_column', 'MasterProductUpdateScriptsController@update_mp_etailer_availability_column')->name('script.update_mp_etailer_availability_column');
    Route::get('script/update_mp_product_temperature_column', 'MasterProductUpdateScriptsController@update_mp_product_temperature_column')->name('script.update_mp_product_temperature_column');
    Route::get('script/update_mp_manufacturer_column', 'MasterProductUpdateScriptsController@update_mp_manufacturer_column')->name('script.update_mp_manufacturer_column');
    Route::get('script/update_mp_supplier_status_column', 'MasterProductUpdateScriptsController@update_mp_supplier_status_column')->name('script.update_mp_supplier_status_column');
    Route::get('script/update_mp_country_of_origin_column', 'MasterProductUpdateScriptsController@update_mp_country_of_origin_column')->name('script.update_mp_country_of_origin_column');
    Route::get('script/update_mp_image_type_column', 'MasterProductUpdateScriptsController@update_mp_image_type_column')->name('script.update_mp_image_type_column');
    Route::get('script/update_mp_product_tags_column', 'MasterProductUpdateScriptsController@update_mp_product_tags_column')->name('script.update_mp_product_tags_column');
    Route::get('script/update_mp_lobs_column', 'MasterProductUpdateScriptsController@update_mp_lobs_column')->name('script.update_mp_lobs_column');
    Route::get('script/update_mp_prop_65_ingredient_column', 'MasterProductUpdateScriptsController@update_mp_prop_65_ingredient_column')->name('script.update_mp_prop_65_ingredient_column');
    Route::get('script/update_mp_allergens_column', 'MasterProductUpdateScriptsController@update_mp_allergens_column')->name('script.update_mp_allergens_column');

    //Master Product Queue
    Route::get('script/update_mpq_allergens_column', 'MasterProductUpdateScriptsController@update_mpq_allergens_column')->name('script.update_mpq_allergens_column');
    Route::get('script/update_mpq_country_of_origin_column', 'MasterProductUpdateScriptsController@update_mpq_country_of_origin_column')->name('script.update_mpq_country_of_origin_column');
    Route::get('script/update_mpq_prop_65_ingredient_column', 'MasterProductUpdateScriptsController@update_mpq_prop_65_ingredient_column')->name('script.update_mp_brand_column');
    Route::get('script/update_mpq_etailer_availability_column', 'MasterProductUpdateScriptsController@update_mpq_etailer_availability_column')->name('script.update_mpq_etailer_availability_column');
    Route::get('script/update_mpq_supplier_status_column', 'MasterProductUpdateScriptsController@update_mpq_supplier_status_column')->name('script.update_mpq_supplier_status_column');
    Route::get('script/update_mpq_product_tags_column', 'MasterProductUpdateScriptsController@update_mpq_product_tags_column')->name('script.update_mpq_product_tags_column');
    Route::get('script/update_mpq_lobs_column', 'MasterProductUpdateScriptsController@update_mpq_lobs_column')->name('script.update_mpq_lobs_column');
    Route::get('script/create_categories', 'MasterProductUpdateScriptsController@create_categories')->name('script.create_categories');
    Route::get('script/create_sub_category_1', 'MasterProductUpdateScriptsController@create_sub_category_1')->name('script.create_sub_category_1');
    Route::get('script/create_sub_category_2', 'MasterProductUpdateScriptsController@create_sub_category_2')->name('script.create_sub_category_2');
    Route::get('script/create_sub_category_3', 'MasterProductUpdateScriptsController@create_sub_category_3')->name('script.create_sub_category_3');

    //
    Route::get('script/update_mp_product_categories_column_transfer', 'MasterProductUpdateScriptsController@update_mp_product_categories_column_transfer')->name('script.update_mp_product_categories_column_transfer');
    Route::get('script/update_mp_product_subcategories_1_column_transfer', 'MasterProductUpdateScriptsController@update_mp_product_subcategories_1_column_transfer')->name('script.update_mp_product_subcategories_1_column_transfer');
    Route::get('script/update_mp_product_subcategories_2_column_transfer', 'MasterProductUpdateScriptsController@update_mp_product_subcategories_2_column_transfer')->name('script.update_mp_product_subcategories_2_column_transfer');
    Route::get('script/update_mp_product_subcategories_3_column_transfer', 'MasterProductUpdateScriptsController@update_mp_product_subcategories_3_column_transfer')->name('script.update_mp_product_subcategories_3_column_transfer');

    Route::get('script/update_mpq_product_categories_column_transfer', 'MasterProductUpdateScriptsController@update_mpq_product_categories_column_transfer')->name('script.update_mpq_product_categories_column_transfer');
    Route::get('script/update_mpq_product_subcategories_1_column_transfer', 'MasterProductUpdateScriptsController@update_mpq_product_subcategories_1_column_transfer')->name('script.update_mpq_product_subcategories_1_column_transfer');
    Route::get('script/update_mpq_product_subcategories_2_column_transfer', 'MasterProductUpdateScriptsController@update_mpq_product_subcategories_2_column_transfer')->name('script.update_mpq_product_subcategories_2_column_transfer');
    Route::get('script/update_mpq_product_subcategories_3_column_transfer', 'MasterProductUpdateScriptsController@update_mpq_product_subcategories_3_column_transfer')->name('script.update_mpq_product_subcategories_3_column_transfer');

    // API
    Route::get('/storeTimeZones', 'TimeZoneController@storeTimeZones')->name('storeTimeZones');

    Route::post('MapClilentProduct','ClientController@MapClilentProduct')->name('clients.MapClilentProduct');
    Route::post('saveClientImportHeaders','ClientController@saveClientImportHeaders')->name('clients.saveClientImportHeaders');
    Route::get('dalete_client_product_header/{id}','ClientController@dalete_client_product_header')->name('clients.dalete_client_product_header');

    Route::get('chanel_management','ChanelManagementController@index')->name('chanel_management.index');
    Route::get('chanel_management/view_products/{id}','ChanelManagementController@view_products')->name('chanel_management.view_products');
    Route::get('chanel_management/chanel_products/{id}', 'ChanelManagementController@chanel_products')->name('chanel_management.chanel_products');


    Route::get('summery_orders/{id?}','OrdersController@index')->name('orders.index');
    Route::get('summery_orders/create/new/{id?}','OrdersController@create')->name('orders.create');
    Route::post('summery_orders/store/order/{id?}','OrdersController@store')->name('orders.store');
    Route::get('summery_orders/{id}/edit/{clinet_id?}','OrdersController@edit')->name('orders.edit');
    Route::post('summery_orders/{id}/update/{clinet_id?}','OrdersController@update')->name('orders.update');
    Route::post('/getOptimizedorders', 'OrdersController@getOptimizedorders')->name('getOptimizedorders');
    Route::post('/getOptimizedordersFilter', 'OrdersController@getOptimizedordersFilter')->name('getOptimizedordersFilter');
    Route::get('summery_orders/{id}/view','OrdersController@view')->name('orders.view');
    Route::post('summery_orders/update_qty','OrdersController@update_qty')->name('orders.update_qty');
    Route::get('summery_orders/add_product/{order_number}','OrdersController@add_product')->name('orders.add_product');
    Route::post('summery_orders/store_product','OrdersController@store_product')->name('orders.store_product');
    Route::get('summery_orders/product_wh_count/{etin}','OrdersController@product_wh_count')->name('orders.product_wh_count');
    Route::get('summery_orders/view_sub_order/{order_number}','OrdersController@view_sub_order')->name('orders.view_sub_order');
    Route::post('summery_orders/sub_order/change_status','OrdersController@changeStatus')->name('orders.change_status');
    Route::post('summery_orders/sub_order/change_shipping_customer_details','OrdersController@changeShippingAndCustomerDetails')->name('orders.change_shipping_customer_details');
    Route::post('summery_orders/sub_order/update_sub_order_ship_details','OrdersController@updateSubOrderShipDetails')->name('orders.update_sub_order_ship_details');
    Route::post('summery_orders/sub_order/split_order','OrdersController@splitOrders')->name('orders.split_order');
    Route::post('summery_orders/sub_order/merge_order','OrdersController@mergeOrders')->name('orders.merge_order');
    Route::get('summery_orders/sub_order/all/{order_number}','OrdersController@sub_order')->name('orders.sub_orders');
    Route::post('summery_orders/sub_order/reship_order','OrdersController@reShipOrder')->name('orders.reship_order');
    Route::post('summery_orders/sub_order/reship_order_page','OrdersController@viewReshipOptionsPage')->name('orders.reship_order_page');
    Route::get('summery_orders/bulk_upload_order/index/{client?}','OrdersController@bulk_order_page')->name('orders.bulk_upload_order');
    Route::post('summery_orders/bulk_upload_order/process_bulk_upload','OrdersController@process_bulk_upload')->name('orders.process_bulk_upload');
    Route::post('/getOptimizedorders2', 'OrdersController@getOptimizedorders2')->name('getOptimizedorders2');
    Route::post('/getOptimizedordersFilter2', 'OrdersController@getOptimizedordersFilter2')->name('getOptimizedordersFilter2');
    Route::post('summery_orders/bulk_upload_order/ship_manual','OrdersController@shipManualOrders')->name('orders.ship_manual');
    Route::get('summery_orders/FilterOrderProducts/{order_id}/{id?}','OrdersController@FilterOrderProducts')->name('orders.FilterOrderProducts');
    Route::post('summery_orders/getOptimizedMasterproductsForOrder', 'OrdersController@getOptimizedMasterproductsForOrder')->name('getOptimizedMasterproductsForOrder');
    Route::post('summery_orders/getOptimizedMasterproductsFilterForOrder', 'OrdersController@masterproductsFilterForOrder')->name('getOptimizedMasterproductsFilterForOrder');
    Route::get('summery_orders/ViewTrackingDetails/{order_id}','OrdersController@ViewTrackingDetails')->name('orders.ViewTrackingDetails');
    Route::get('summery_orders/OrderHistory/{order_id}','OrdersController@OrderHistory')->name('orders.OrderHistory');
    Route::post('summery_orders/SaveProducts', 'OrdersController@SaveProducts')->name('orders.SaveProducts');
    Route::post('summery_orders/show_error', 'OrdersController@showError')->name('orders.show_error');
    Route::get('summery_orders/update_sub_order_status/{id}/{status}', 'OrdersController@update_sub_order_status')->name('orders.update_sub_order_status');
    Route::post('summery_orders/UpdateOrderDetailStatus', 'OrdersController@UpdateOrderDetailStatus')->name('orders.UpdateOrderDetailStatus');
    Route::post('summery_orders/cancel_order', 'OrdersController@cancel_order')->name('orders.cancel_order'); //cancel an order 
    Route::get('summery_orders/delete_sub_order_items/{id}', 'OrdersController@delete_sub_order_items')->name('orders.delete_sub_order_items');
    Route::get('summery_orders/update_sub_order_wh/{sub_order_number}', 'OrdersController@update_sub_order_wh')->name('orders.update_sub_order_wh');
    Route::post('summery_orders/UpdateOrderDetailWh', 'OrdersController@UpdateOrderDetailWh')->name('orders.UpdateOrderDetailWh');


    Route::get('getProductsAutoComplete','OrdersController@getProductsAutoComplete')->name('getProductsAutoComplete');
    

    Route::get('/wmsconfigration', 'WmsConfigController@index')->name('wmsconfig');
    Route::get('/getorderprocessdata', 'WmsConfigController@getOrderProcessdata')->name('getorderprocessdata');
    Route::get('/edit_processing_group/{id}', 'WmsConfigController@edit_processing_group')->name('edit_processing_group');
    Route::post('/update_processing_group/{id}', 'WmsConfigController@update_processing_group')->name('update_processing_group');
    Route::get('/getpickerconf', 'WmsConfigController@getPickerConf')->name('getpickerconf');
    Route::get('/edit_picker_conf/{id}', 'WmsConfigController@editPickerConf')->name('edit_picker_conf');
    Route::post('/update_picker_conf/{id}', 'WmsConfigController@updatePickerConf')->name('update_picker_conf');
    Route::get('/getpickPackData', 'WmsConfigController@getpickPackData')->name('getpickPackData');
    Route::get('/add_pick_pack_method', 'WmsConfigController@add_pick_pack_method')->name('add_pick_pack_method');
    Route::post('/store_pick_pack_method', 'WmsConfigController@store_pick_pack_method')->name('store_pick_pack_method');
    Route::get('/edit_pick_pack_method/{id}', 'WmsConfigController@edit_pick_pack_method')->name('edit_pick_pack_method');
    Route::post('/update_pick_pack_method/{id}', 'WmsConfigController@update_pick_pack_method')->name('update_pick_pack_method');
    Route::post('/shipping_eligibility/', 'WmsConfigController@shipping_eligibility')->name('shipping_eligibility');
    Route::post('/update_shipping_eligiblity/', 'WmsConfigController@updateShippingEligiblity')->name('update_shipping_eligiblity');
    Route::get('/delete_pick_pack_method/{id}', 'WmsConfigController@delete_pick_pack_method')->name('delete_pick_pack_method');
    Route::get('/getSupplierData', 'WmsConfigController@getSupplierData')->name('getSupplierData');
    Route::post('/update_supplier_exp_lot/{id}', 'WmsConfigController@update_supplier_exp_lot')->name('update_supplier_exp_lot');
    Route::get('/get_hot_route_list', 'HotRouteController@get_hot_route_list')->name('get_hot_route_list');
    Route::post('/save_route', 'HotRouteController@save_route')->name('save_route');
    Route::delete('/delete_route/{id}', 'HotRouteController@delete_route')->name('delete_route');
    Route::get('/get_route_by_id/{id}', 'HotRouteController@get_route_by_id')->name('get_route_by_id');

    //Get Client data for wms config
    Route::get('/getClientData', 'WmsConfigController@getClientData')->name('getClientData');
    Route::post('/update_client_exp_lot/{id}', 'WmsConfigController@update_client_exp_lot')->name('update_client_exp_lot');

    // Report Section
    Route::get('/report-index', 'ReportSectionController@index');
    Route::post('/get-open-order-report', 'ReportSectionController@getOpenOrderReport');
    Route::post('/get-shipped-order-report', 'ReportSectionController@getShippedItemsReport');
    Route::post('/get-inventory-report', 'ReportSectionController@getInventoryReport');
    Route::post('/get-ood-report', 'ReportSectionController@getOODReport');
    Route::post('/get-shipped-items-report', 'ReportSectionController@getShippedItemsReport');
    Route::get('/reports-filter/{report_type}', 'ReportSectionController@getFilters');
    Route::post('/save-filters', 'ReportSectionController@saveFilters');

    Route::get('/reportbuilder', 'ReportBuilderController@index');
    Route::post('/reportbuilder', 'ReportBuilderController@generateReport')->name('report-genrate');
    Route::get('/reportschedule','ReportBuilderController@setReportSchedule')->name('report-schedule');
    Route::post('/reportschedule', 'ReportBuilderController@setReportScheduleSave')->name('report-schedule-submit');

    Route::get('/pipeline_and_metrix', 'PipelineAndMatrixController@index')->name('pipeline_and_metrix.index');
    Route::post('/GetTotalOrderChart', 'PipelineAndMatrixController@GetTotalOrderChart')->name('pipeline_and_metrix.GetTotalOrderChart');
    Route::post('/GetTotalOrderModel', 'PipelineAndMatrixController@GetTotalOrderModel')->name('pipeline_and_metrix.GetTotalOrderModel');
    Route::post('/GetTotalOrderChartByUser', 'PipelineAndMatrixController@GetTotalOrderChartByUser')->name('pipeline_and_metrix.GetTotalOrderChartByUser');
    Route::post('/GetTotalOrderChartByWarehouse', 'PipelineAndMatrixController@GetTotalOrderChartByWarehouse')->name('pipeline_and_metrix.GetTotalOrderChartByWarehouse');
    Route::post('/GetTotalOrderChartByTransitDays', 'PipelineAndMatrixController@GetClientOrdersByTransitDays')->name('pipeline_and_metrix.GetTotalOrderChartByTransitDays');
    Route::post('/GetTransitDaysModal', 'PipelineAndMatrixController@GetTransitDayModal')->name('pipeline_and_metrix.GetTransitDayModal');
    Route::post('/GetTransitDaysOrderModal', 'PipelineAndMatrixController@GetTransitDayOrderModal')->name('pipeline_and_metrix.GetTransitDayOrderModal');
    Route::post('/GetTotalOrderChartByOrderStatus', 'PipelineAndMatrixController@GetClientOrdersByOrderStatus')->name('pipeline_and_metrix.GetClientOrdersByOrderStatus');
    Route::post('/GetClientOrdersByOrderStatusModal', 'PipelineAndMatrixController@GetClientOrdersByOrderStatusModal')->name('pipeline_and_metrix.GetClientOrdersByOrderStatusModal');
    Route::post('/GetTotalOrderChartByWarehouseModel', 'PipelineAndMatrixController@GetWarehouseOrderModel')->name('pipeline_and_metrix.GetTotalOrderChartByWarehouseModel');
    Route::post('/GetTransitDayTable', 'PipelineAndMatrixController@GetTransitDayTable')->name('pipeline_and_metrix.GetTransitDayTable');
    Route::post('/GetOrderStatusTable', 'PipelineAndMatrixController@GetOrderStatusTable')->name('pipeline_and_metrix.GetOrderStatusTable');
    Route::post('/GetOrderModal', 'PipelineAndMatrixController@GetOrderModal')->name('pipeline_and_metrix.GetOrderModal');
    Route::post('/GetClientOrdersTableByShipDay', 'PipelineAndMatrixController@GetClientOrdersTableByShipDay')->name('pipeline_and_metrix.GetClientOrdersTableByShipDay');
    Route::post('/GetClientOrdersModalByShipDay', 'PipelineAndMatrixController@GetClientOrdersModalByShipDay')->name('pipeline_and_metrix.GetClientOrdersModalByShipDay');
    Route::post('/GetClientOrdersGraphByShipDay', 'PipelineAndMatrixController@GetClientOrdersGraphByShipDay')->name('pipeline_and_metrix.GetClientOrdersGraphByShipDay');
    Route::post('/TotalOrderCSVDownload', 'PipelineAndMatrixController@TotalOrderCSVDownload')->name('pipeline_and_metrix.TotalOrderCSVDownload');
    
    // Exclusion SKU Routes
    Route::get('/sku_index', 'ExclusionSKUController@index')->name('sku.index');
    Route::get('/new_exclusion', 'ExclusionSKUController@create')->name('sku.create');
    Route::post('/save_exclusion', 'ExclusionSKUController@store')->name('sku.store');
    Route::get('/edit_exclusion/{client_id}/{mp_id}', 'ExclusionSKUController@show')->name('sku.edit');
    Route::post('/update_exclusion', 'ExclusionSKUController@update')->name('sku.update');
    Route::delete('/delete_exclusion/{id}', 'ExclusionSKUController@deleteExclusion')->name('sku.delete');
    Route::post('/enable_dne', 'ExclusionSKUController@enableDne')->name('sku.enable_dne');
    Route::post('/disable_dne', 'ExclusionSKUController@disableDne')->name('sku.disable_dne');

    Route::get('/search_products_orders/{search_text}', 'HomeController@search_product_order')->name('search_products_orders');

    Route::view('/zip_zone_wh', 'carriers.zip_zone_wh')->name('zip_zone_wh');
    Route::get('/edit_zip_zone_wh/{id}', 'CarriersController@edit_zip_zone_wh')->name('edit_zip_zone_wh');
    Route::post('/update_transit_day', 'CarriersController@update_transit_day')->name('update_transit_day');
});

Route::get('clear_cache',function(){
    Artisan::call('optimize:clear');
    dd('done');
});

// Order processing commands

Route::get('import_SA_orders',function(){
    Artisan::call('command:import_sa_inventory');
    dd('done');
});

Route::get('incoming_order_processing',function(){
    Artisan::call('command:incoming_order_processing');
    dd('done');
});


Route::get('/downloadLog','UserLogsController@downloadLog');
Route::get('/ClearLog','UserLogsController@ClearLog');
