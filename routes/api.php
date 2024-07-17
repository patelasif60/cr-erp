<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', '\App\Http\Controllers\Auth\ApiAuthController@login');

// Route::middleware('auth:api')->group(function () {
//Route::resource('posts', PostController::class);
Route::post('/changepassword', '\App\Http\Controllers\Auth\ApiAuthController@changePassword');
Route::get('/logout', '\App\Http\Controllers\Auth\ApiAuthController@logout');
Route::post('/getuser', 'Api\UserApiController@getUser');
Route::post('/user/update/warehouse', 'Api\UserApiController@updateWarehouse');



Route::post('/getallaisle', 'Api\AisleApiController@getAllAisle');
Route::post('/searchandgolocation', 'Api\AisleApiController@SearchAndGoLocation');
Route::post('/addaisle', 'Api\AisleApiController@createAisle');
Route::post('/getaislebywarehouseid', 'Api\AisleApiController@getAllAisleByWharehouse');
Route::get('/getaislebyid/{id}/{parent_id}', 'Api\AisleApiController@getAisleById');
Route::get('/getproducttemperaturelist', 'Api\AisleApiController@getProductTemperatureList');
Route::get('/getallwareHouse', 'Api\AisleApiController@getAllWareHouse');
Route::post('/editaisle/{id}', 'Api\AisleApiController@editAisle');
Route::delete('/deleteaisle/{id}/{delete?}', 'Api\AisleApiController@deleteAisle');
Route::get('/getprocessinggroups', 'Api\AisleApiController@getProcessingGroups');
Route::get('/ProDetailByETIN/{id}', 'Api\AisleApiController@ProDetailByETIN');
Route::post('/UpdateBayType', 'Api\AisleApiController@UpdateBayType');


Route::get('/getallbay', 'Api\MasterBayApiController@getAllBay');
Route::post('/addbay', 'Api\MasterBayApiController@createBay');
Route::post('/addPosition', 'Api\MasterBayApiController@addPosition');

Route::get('/getbaybyaisle/{id}/{parent_id}', 'Api\MasterBayApiController@getBayByAisle');
Route::get('/getbaybyid/{id}', 'Api\MasterBayApiController@getBayById');
Route::post('/editebay/{id}', 'Api\MasterBayApiController@editeBay');
Route::delete('/deletebay/{id}', 'Api\MasterBayApiController@deleteBay');
Route::get('/getbaybyaisleid/{id}/{parent_id}', 'Api\MasterBayApiController@getBayAisle');

Route::post('/createshelf', 'Api\MasterShelfApiController@createShelf');
Route::get('/getalllocationtype', 'Api\MasterShelfApiController@getAllLocationType');
Route::post('/getallshelfbybayid', 'Api\MasterShelfApiController@getAllShelfByBayId');
Route::get('/getonlyshelfbybayid/{id}/{parent_id}', 'Api\MasterShelfApiController@getOnlyShelfByBayId');
Route::post('/getslotbyshelfbay/{id}', 'Api\MasterShelfApiController@getSlotByShelfBay');
Route::post('/getallproducts', 'Api\MasterShelfApiController@getAllProducts');
Route::post('/getAllApprovedProducts', 'Api\MasterShelfApiController@getAllApprovedProducts');
Route::post('/geProtLotExp', 'Api\MasterShelfApiController@geProtLotExp');

Route::delete('/deleteproduct/{id}', 'Api\MasterShelfApiController@deleteProduct');
Route::delete('/deleteslot/{id}', 'Api\MasterShelfApiController@deleteSlot');
Route::get('/getshelfbyId/{id}', 'Api\MasterShelfApiController@getShelfById');
Route::post('/getShelfInfoByAddres', 'Api\MasterShelfApiController@getShelfInfoByAddres');
Route::post('/getProductInventoryInfo', 'Api\MasterShelfApiController@getProductInventoryInfo');



Route::post('/addproduct', 'Api\WarehouseInventoriesApiContorller@addProduct');
Route::post('/TransferProduct', 'Api\WarehouseInventoriesApiContorller@TransferProduct');
Route::post('/editproduct/{id}', 'Api\WarehouseInventoriesApiContorller@editProduct');
Route::post('/editproductlocation', 'Api\WarehouseInventoriesApiContorller@editProductLocation');
Route::post('/editproductqunitity/{id}', 'Api\WarehouseInventoriesApiContorller@editProductQuntity');
Route::get('/getallinventories', 'Api\WarehouseInventoriesApiContorller@getAllInventories');
Route::post('/getproductsbybayid', 'Api\WarehouseInventoriesApiContorller@getProductsByBayId');


Route::get('/getallreceivingdetails', 'Api\ReceivingDetailController@getAllReceiving');
Route::get('/getallreceivingdetailsbysupplier/{supplierId}', 'Api\ReceivingDetailController@getAllReceivingBySupplierId');
Route::get('/getallreceivingdetailsbysummary/{summaryId}', 'Api\ReceivingDetailController@getAllReceivingByPurchaseSummary');
Route::post('/checkdiscrepency/{summaryId}', 'Api\ReceivingDetailController@checkDiscrepency');
Route::post('/savereceivingdetail/{summaryId}', 'Api\ReceivingDetailController@saveReceiving');
Route::delete('/deletereceivingdetail/{summaryId}', 'Api\ReceivingDetailController@deleteReceivingDetailsBySummaryId');
Route::post('/updatereceivingdetail', 'Api\ReceivingDetailController@updateReceiving');
Route::delete('/deletereceivingdetailbyid/{id}', 'Api\ReceivingDetailController@deleteReceivingDetailsById');
Route::put('/completereceiving/{id}', 'Api\ReceivingDetailController@completeReceivingDetails');
Route::post('/SattleDamagedQty', 'Api\ReceivingDetailController@SattleDamagedQty');
Route::post('/savepurchasingdetail', 'Api\PurchasingDetailController@savePurchaseDetail');

Route::post('/addorupdatepurchasingsummary', 'Api\PurchasingSummaryController@addOrUpdatePurchasingSummary');
Route::post('/getallpurchasingSummary', 'Api\PurchasingSummaryController@getAllPurchasingSummary');
Route::get('/getallpurchasingSummarybysupplier/{supplierId}', 'Api\PurchasingSummaryController@getAllPurchasingSummaryBySupplier');
Route::get('/getallpurchasingSummarybyId/{id}', 'Api\PurchasingSummaryController@getAllPurchasingSummaryById');
Route::get('/getallpurchasingSummarybyWarehouseId/{whId}', 'Api\PurchasingSummaryController@getAllPurchasingSummaryByWareHouseId');

Route::post('/GetAllUniqueBol', 'Api\PurchasingSummaryController@GetAllUniqueBol');
Route::get('/GetBOlInfo/{id}', 'Api\PurchasingSummaryController@GetBOlInfo');


Route::post('/recordputaway/{summaryId}', 'Api\PutAwayController@recordPutAway');
Route::post('/saveputaway/{summaryId}', 'Api\PutAwayController@savePutAway');
Route::get('/getputawaybysummary/{summaryId}/{user_id}', 'Api\PutAwayController@getAllPutAwayBySummaryId');
Route::get('/checkputawaydiscrepency/{summaryId}', 'Api\PutAwayController@checkPutAwayDiscrepency');
Route::delete('/deleteputawaybysummary/{summaryId}', 'Api\ReceivingDetailController@deletePutAwayBySummaryId');
Route::get('/deleteputawaybyid/{id}', 'Api\PutAwayController@deletePutAwayById');
Route::post('/SCanUpcWithLocation/{bol_number}', 'Api\PutAwayController@SCanUpcWithLocation');
Route::get('/PutAwayToReady/{bol_number}', 'Api\PutAwayController@PutAwayToReady');
Route::post('/UpdatePutQty', 'Api\PutAwayController@UpdatePutQty');
Route::get('/getallreceivedItems/{id}', 'Api\ReceivingDetailController@getallreceivedItems');
Route::post('/SelectLot', 'Api\PutAwayController@SelectLot');
Route::get('/GetPutAway/{bol_number}', 'Api\PutAwayController@GetPutAway');
Route::post('/UpdateLot', 'Api\PutAwayController@UpdateLot');





Route::post('/backstock_pallet/create', 'Api\BackStockPalletController@store');
Route::post('/backstock_pallet/get_all', 'Api\BackStockPalletController@index');
Route::get('/backstock_pallet/edit/{id}', 'Api\BackStockPalletController@edit');
Route::get('/backstock_pallet/delete/{id}', 'Api\BackStockPalletController@destroy');
Route::post('/backstock_pallet/add_item/{id}', 'Api\BackStockPalletController@add_item');
Route::get('/backstock_pallet/get_pallet_items/{id}', 'Api\BackStockPalletController@get_pallet_items');
Route::post('/backstock_pallet/edit_item/', 'Api\BackStockPalletController@editItem');
Route::get('/backstock_pallet/deletebackstockpallet/{id}', 'Api\BackStockPalletController@deleteItem');
Route::put('/backstock_pallet/stocktoshelf', 'Api\BackStockPalletController@storeBackStockPalletItems');
Route::get('/backstock_pallet/getlocations/{warehouseId}/{typeId}', 'Api\BackStockPalletController@getLocation');
Route::post('/backstock_pallet/checkdiscrepency/{blockPalleteId}', 'Api\BackStockPalletController@checkDiscrepency');
Route::post('/backstock_pallet/AddItemToTransferedPallet', 'Api\BackStockPalletController@AddItemToTransferedPallet');
Route::post('/backstock_pallet/SelectLot', 'Api\BackStockPalletController@SelectLot');
Route::post('/backstock_pallet/UpdateLot', 'Api\BackStockPalletController@UpdateLot');
Route::get('/backstock_pallet/BSItemInfo/{id}', 'Api\BackStockPalletController@BSItemInfo');


Route::post('/restock/create', 'Api\RestockController@store');
Route::post('/restock/AssignProductsToRestock', 'Api\RestockController@AssignProductsToRestock');
Route::post('/restock/get_all', 'Api\RestockController@index');
Route::get('/restock/edit/{id}', 'Api\RestockController@edit');
Route::get('/restock/delete/{id}', 'Api\RestockController@destroy');
Route::post('/restock/add_item', 'Api\RestockController@add_item');
Route::post('/restock/get_added_items', 'Api\RestockController@get_added_items');
Route::post('/restock/edit_item', 'Api\RestockController@editItem');
Route::get('/restock/deleteItem/{id}', 'Api\RestockController@deleteItem');
Route::post('/restock/CompleteRestock', 'Api\RestockController@CompleteRestock');
Route::post('/restock/GetPickLocationItemsReport', 'Api\RestockController@GetPickLocationItemsReport');
Route::post('/restock/GetItemsForRestock', 'Api\RestockController@GetItemsForRestock');






Route::put("/restock/{etin}", 'Api\MasterShelfApiController@doRestock');
// Route::post("/select_warehouse_location_backstock", "Api\RestockController@getBackstockLocationProducts");
// // Route::post("/replenish_pick/{warehouseId}/{bsAddress}", "Api\RestockController@doRestock");
// Route::post("/store_in_bucket/{bsAddress}", "Api\RestockController@putInBucket");
// Route::post("/get_pick_items", "Api\RestockController@getPickLocationItems");
// Route::post("/replenish_pick", "Api\RestockController@doRestock2");

//Transfer and Markout
Route::get('/getAllWarehouses', 'Api\InventoryTransferController@getAllWarehouses');
Route::post('/getAllLocationByWarehouse', 'Api\InventoryTransferController@getAllLocationByWarehouse');
Route::post('/transaferProductQTY', 'Api\InventoryTransferController@transaferProductQTY');
//Cycle Counts
Route::post('/cycle_count_summary', 'Api\CycleCountController@cycle_count_summary');
Route::get('/cycle_count_summary_details/{id}', 'Api\CycleCountController@cycle_count_summary_details');
Route::post('/cycle_add_items', 'Api\CycleCountController@add_items');
Route::get('/complete_cycle_counts/{id}', 'Api\CycleCountController@complete_cycle_counts');
Route::get('/start_cycle_counts/{id}', 'Api\CycleCountController@start_cycle_counts');
Route::post('/NextLocation', 'Api\CycleCountController@NextLocation');



Route::post('/markoutProduct', 'Api\InventoryTransferController@markoutProduct');

Route::post('/validate-address', 'Api\UpsController@validateAddressApi');

Route::post("/all-warehouse-orders", 'Api\OrderManagementController@getAllWarehouseOrders');
Route::post("/all-shipped-orders", 'Api\OrderManagementController@getAllShippedOrders');
Route::post("/AllNonPersonPickupOrders", 'Api\OrderManagementController@getNonPersonPickupOrders');
Route::get("/nonpickuporder/PalletOrders/{id}", 'Api\OrderManagementController@PalletOrders');
Route::get("/order-details/{orderNumber}", 'Api\OrderManagementController@getOrderDetails');
Route::post("/update-order-details", 'Api\OrderManagementController@updateOrders');
Route::get("/suborder/items/{subordernumber}", 'Api\OrderManagementController@OrderItems');
Route::get("/nonpickuporder/items/{subordernumber}", 'Api\OrderManagementController@NonPickupOrderItems');
// Route::post("/createPickupOrderPallet", 'Api\OrderManagementController@createPickupOrderPallet');
Route::post("/getOrderPallets", 'Api\OrderManagementController@getOrderPallets');
Route::get("/ShipOrderPallets/{id}", 'Api\OrderManagementController@ShipOrderPallets');
Route::get("/orders/OrderDetailStatus", 'Api\OrderManagementController@OrderDetailStatus');
Route::get("/orders/UpdateOrderDetailStatus/{order_id}/{status_id}", 'Api\OrderManagementController@UpdateOrderDetailStatus');




Route::get("/all-pickers/{id}", 'Api\PickerOrderMapController@getAllPickers');
Route::get("/all-users/{warehouse_id}", 'Api\PickerOrderMapController@getAllUsers');
Route::get("/pickers-orders/{pickerId}", 'Api\PickerOrderMapController@getPickerOrders');
Route::put("/update-pickers-orders/{orderSummaryId}/{pickerId}", 'Api\PickerOrderMapController@updatePickerInOrder');

Route::get("/pick/suborders/{pickerId}", 'Api\PickerOrderDetailController@getPickSubOrders');
Route::get("/pick/suborder/batchwise/{subordernumber}", 'Api\PickerOrderDetailController@getPickSubOrdersBatchwise');
Route::post("/pick/suborder/batchwise/store", 'Api\PickerOrderDetailController@pickSubOrdersBatchwiseStore');
Route::post("/pick/suborder/orderfullfill/store", 'Api\PickerOrderDetailController@pickSubOrderFullfillStore');
Route::get("/assignOrdersToPicker/{pickerId}/{wah}", 'Api\OrderAssignmentController@assignOrdersToPicker');
Route::get("/pick/suborders/print/{pickerId}", 'Api\PickerOrderDetailController@printPickerSubOrders');
Route::get("/suborders/print/{sub_order_number}", 'Api\PickerOrderDetailController@printSubOrders');
Route::get("/nonpickup/print/{sub_order_number}", 'Api\PickerOrderDetailController@printNonPickupOrders');
Route::get("/test-orders/{pickerId}/{wah}", 'Api\OrderAssignmentController@assignOrdersToPicker');

//Pack
Route::get('/pack-orders/create_package/{sub_order_id}', 'Api\PackController@create_package');
Route::post('/pack-orders/scan_package/{package_number}/{sub_order_id}', 'Api\PackController@scan_package');
Route::post('/pack-orders/PackSlip/{sub_order}', 'Api\PackController@PackSlip');
Route::post('/pack-orders/add_products', 'Api\PackController@add_products');
Route::post('/pack-orders/save_package', 'Api\PackController@save_package');
Route::post('/pack-orders/save_package_child_component', 'Api\PackController@save_package_child_component');
Route::get('/GetPackageStatus/{sub_order_id}/{pack_id}', 'Api\PackController@GetPackageStatus');
Route::post('/pack-orders/SelectLot', 'Api\PackController@SelectLot');
Route::post('/pack-orders/UpdateLot', 'Api\PackController@UpdateLot');
Route::delete('/pack-orders/deletePackingItem/{id}', 'Api\PackController@deletePackingItem');
Route::get('/pack-orders/getOrderPackItemInfo/{id}', 'Api\PackController@getOrderPackItemInfo');


Route::get('/GetBarcodeInfo/{sub_order_id}', 'Api\ShipController@GetBarcodeInfo');
Route::post('/CreateLabel', 'Api\ShipController@CreateLabel');
Route::post('/CompleteShipment', 'Api\ShipController@CompleteShipment');



// Fedex
Route::get('/generate-token', 'Api\FedexController@generateTokenApi');
Route::post('/validate-address', 'Api\FedexController@validateAddressApi');
Route::post('/generate-label', 'Api\FedexController@generateLabelApi');
Route::post('/get-transit-days', 'Api\FedexController@getTransitDaysApi');

Route::post('/ups/validate-address', 'Api\UpsController@validateAddressApi');