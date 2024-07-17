<?php

namespace App\Http\Controllers\Api;

use App\MasterProduct;
use App\PurchasingDetail;
use App\PurchasingSummary;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PurchasingDetailController extends Controller
{
    /*
        Method: savePurchaseDetail
        Description: Save the purchase detail
    */
    public function savePurchaseDetail(Request $request, $supplierId, $po) {

        $requests = $request->all();

        $validator = Validator::make($requests, [
            '*.supplier_id' => 'required',
            '*.po' => 'required',
            '*.upc' => 'required',
            '*.qty_ordered' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['error' => true, 'message' => $validator->errors()->all()], 422);
        }

        $purchaseSummary = PurchasingSummary::where('supplier_id', $supplierId)->where('order', $po)->first();
        if (!$purchaseSummary) {
            return response(["error" => true, 'message' => 'No purchase found for PO: ' . $po . ' and Supplier Id: ' . $supplierId], 400);
        }

        for ($i = 0; $i < sizeof($requests); $i++ ) {
            $req = $requests[$i];
            $upc = $req['upc'];
            $masterProduct = MasterProduct::where(function($q) use($upc){
                $q->where('upc', $upc);
                $q->orWhere('gtin',$upc);
                $q->orWhere('ETIN',$upc);
            })->whereNull('parent_ETIN')->first();
            if (!$masterProduct) {
                return response(['errors' => true, 'message' => 'Product not found with UPC: ' . $upc], 404);
            } else if (!$masterProduct['ETIN']) {
                return response(['errors' => true, 'message' => 'Product ETIN not found for UPC: ' . $upc], 404);
            }  else {
                $req['etin'] = $masterProduct->ETIN;
            }
            $requests[$i] = $req;
        }

        foreach ($requests as $req) {
            PurchasingDetail::create([
                'supplier_id' => $purchaseSummary->id,
                'po' => $purchaseSummary->order,
                'etin' => $req['etin'],
                'qty_ordered' => $req['qty_ordered'],
            ]);
        }

        // TODO: While inserting check the UPC is present in our record.
        return response(["error" => false, 'message' => 'Record added successfully!'], 200);
    }
}
