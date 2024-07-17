<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MasterProduct;
use App\ClientChannelConfiguration;
use DataTables;
use App\PriceGroup;
use DB;
use Illuminate\Support\Facades\Log;
use App\Services\PriceGroupService;


class ChanelManagementController extends Controller
{
	public function __construct(PriceGroupService $priceGroupService)
    {
        $this->priceGroupService = $priceGroupService;
    }

    public function index(){
        $result = ClientChannelConfiguration::leftJoin('clients',function($q){
            $q->on('clients.id','=','client_channel_configurations.client_id');
        })->select('client_channel_configurations.*','clients.company_name as client_name')->get();
        return view('chanel_management.index',compact('result'));
    }

    public function view_products($id){
		$priceGroup=DB::table('price_group')->whereRaw('FIND_IN_SET('.$id.',chanel_ids)')->get();
        return view('chanel_management.view_products',compact('id','priceGroup'));
    }

    public function chanel_products(Request $request,$id)
	{

		if ($request->ajax()) {
			$dataget = MasterProduct::with(['users'])->where(['is_approve' => 1])
				->leftJoin('users', function ($join) {
					$join->on('users.id', '=', 'master_product.inserted_by');
				})->select(['master_product.*', 'users.name as username']);


			if ($id != '') {
                $dataget->whereRaw('FIND_IN_SET('.$id.',chanel_ids)');
			}

			// $dataget->groupBy('master_product.id');

			$total = $dataget->count();
			$limit = 10;
			if (isset($input['limit'])) $limit = $input['limit'];

			$page = 1;
			if (isset($input['page'])) $page = $input['page'];

			$offset = $request->get('start');
			$limit = $request->get('length');
			$dataget->skip($offset)->take($limit);
			$data = $dataget->get();

			$formula = null;
			$groupFormula = null;
			$formulaCalculation = null;
			$priceGroup = null;
			$carrierId = null;
			$miscValue = DB::table('misc_cost_values')->pluck('value', 'column_name')->toArray();

			if ($request->price_group) {
				$priceGroup = PriceGroup::find($request->price_group);

				if(isset($priceGroup->priceGroupCostBlock->cost_block) && $priceGroup->priceGroupCostBlock->cost_block != '[]' ){
					$formula = json_decode($priceGroup->priceGroupCostBlock->cost_block);
				}
				
				$groupFormula = $priceGroup->group_formulas;
				$formulaCalculation = $groupFormula->pluck('group_formula', 'formula_for');
				$carrierId = $priceGroup->carrier_id;
			}
			$query1 = DB::table('carrier_standard_fees')->where('carrier_id', $carrierId)->select('weight_gt_50_lbs_3', 'residential_surcharge_ground', 'continental_us_ground', 'residential_extended_ground', 'residential_ground')->first();
			$query = DB::table('carrier_peak_surchrges')->where('carrier_id',$carrierId)->select('ground_residential','additional_handling')->where('effective_date', '<', date('Y-m-d'))->where('end_date', '>', date('Y-m-d'))->first();

			$query2 = DB::table('carrier_dynamic_fees')->where('carrier_id',$carrierId)->where('effective_date', '<', date('Y-m-d'))->select('ground')->orderBy('effective_date','DESC')->first();

			return Datatables::of($data)
				->addIndexColumn()
				->editColumn('is_approve', function ($data) {
					return ($data->is_approve == '0') ? "No" : "Yes";
				})
				->addColumn('group_price', function($row) use ($formula,$groupFormula,$formulaCalculation,$carrierId,$priceGroup,$miscValue,$query1,$query,$query2){
					if($formula){
						$fuelSurcharge = 0;
						$priceBlock=0;
						$costPrice = 0;
						$weight = 0;
						$warehouseAssigned = count(explode(',',$row->warehouses_assigned));

						Log::channel('pricegroup')->info('ETIN: '.$row->ETIN);
						Log::channel('pricegroup')->info('row: '.json_encode($row));
						Log::channel('pricegroup')->info('Base Cost Price: '.$costPrice);

						foreach($formula as $formulaKey => $formulaVal){
							

							if($formulaKey == 'total_product_cost')
							{
								Log::channel('pricegroup')->info('Before total_product_cost price: '.$costPrice);
								
								$costPrice += $this->priceGroupService->masterProductCost($row,$formulaVal);
								
								Log::channel('pricegroup')->info('After total_product_cost price: '.$costPrice);
							}
							if($formulaKey == 'acquisition_cost')
							{
								Log::channel('pricegroup')->info('Before acquisition_cost price: '.$costPrice);

								$costPrice += $this->priceGroupService->masterProductCost($row,$formulaVal);

								Log::channel('pricegroup')->info('After acquisition_cost price: '.$costPrice);
							}
							if($formulaKey == 'product_cost')
							{
								Log::channel('pricegroup')->info('Before product_cost price: '.$costPrice);

								$costPrice += $this->priceGroupService->masterProductCost($row,$formulaVal);

								Log::channel('pricegroup')->info('After product_cost price: '.$costPrice);
							}
							if($formulaKey == 'coolant_cost')
							{
								
								Log::channel('pricegroup')->info('Before coolant_cost price: '.$costPrice);
								
								$costPrice += $this->priceGroupService->packagingCoolantCostOpt($row,$formulaVal,$groupFormula,$miscValue);

								Log::channel('pricegroup')->info('After coolant_cost price: '.$costPrice);
								
							}
							if($formulaKey == 'packaging_and_material')
							{
								
								Log::channel('pricegroup')->info('Before packaging_and_material price: '.$costPrice);
								
								$costPrice += $this->priceGroupService->packagingMateriaCost($row,$formulaVal,$groupFormula,$miscValue);

								Log::channel('pricegroup')->info('After packaging_and_material  price: '.$costPrice);
								
							}
							if($formulaKey == 'additional_handling')
							{
								Log::channel('pricegroup')->info('Before additional_handling price: '.$costPrice);

								$costPrice += $this->priceGroupService->additionalHandlingCostOpt($miscValue,$query1);

								Log::channel('pricegroup')->info('After additional_handling price: '.$costPrice);
							}
							if($formulaKey == 'residential_surcharge')
							{
								Log::channel('pricegroup')->info('Before residential_surcharge price: '.$costPrice);

								$costPrice += $this->priceGroupService->residentialSurchargeCostOpt($formulaCalculation,$query1);

								Log::channel('pricegroup')->info('After residential_surcharge price: '.$costPrice);
							}
							if($formulaKey == 'remote_area_surcharge')
							{
								Log::channel('pricegroup')->info('Before remote_area_surcharge price: '.$costPrice);

								$costPrice += $this->priceGroupService->remoteAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

								Log::channel('pricegroup')->info('After remote_area_surcharge price: '.$costPrice);
							}
							if($formulaKey == 'delivery_area_surcharge')
							{
								Log::channel('pricegroup')->info('Before delivery_area_surcharge price: '.$costPrice);

								$costPrice += $this->priceGroupService->deliveryAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

								Log::channel('pricegroup')->info('After delivery_area_surcharge price: '.$costPrice);
							}
							if($formulaKey == 'extended_delivery_area_surcharge')
							{
								Log::channel('pricegroup')->info('Before extended_delivery_area_surcharge price: '.$costPrice);

								$costPrice += $this->priceGroupService->extendedDeliveryAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

								Log::channel('pricegroup')->info('After price: '.$costPrice);
							}
							if($formulaKey == 'peak_surcharge')
							{
								Log::channel('pricegroup')->info('Before peak_surcharge price: '.$costPrice);

								$costPrice += $this->priceGroupService->peakSurchargeCostOpt($query);

								Log::channel('pricegroup')->info('After peak_surcharge price: '.$costPrice);
							}
							if($formulaKey == 'peak_additional_surcharge')
							{
								Log::channel('pricegroup')->info('Before peak_additional_surcharge price: '.$costPrice);

								$costPrice += $this->priceGroupService->peakAdditionalSurchargeCostOpt($miscValue,$query);

								Log::channel('pricegroup')->info('After peak_additional_surcharge price: '.$costPrice);
							}
							
							if($formulaKey == 'base_shipping_cost')
							{
								Log::channel('pricegroup')->info('Before base_shipping_cost price: '.$costPrice);

								$weight = $row->weight > $row->dimensional_weight ? ceil($row->weight) : ceil($row->dimensional_weight);
								
								$costPrice += $this->priceGroupService->baseShippingCostOpt($row,$weight,$warehouseAssigned,$miscValue);

								Log::channel('pricegroup')->info('After base_shipping_cost price: '.$costPrice);
							}
							if($formulaKey == 'total_shipping')
							{
								Log::channel('pricegroup')->info('Before total_shipping price: '.$costPrice);


								$costPrice += $this->priceGroupService->totalShippingCostOpt($row,$miscValue,$query,$query1,$formulaCalculation,$query2);

								Log::channel('pricegroup')->info('After total_shipping price: '.$costPrice);
							}
							if($formulaKey == "misc_fees_and_Charges")
							{
								Log::channel('pricegroup')->info('Before misc_fees_and_Charges price: '.$costPrice);

								$costPrice += $this->priceGroupService->miscFeesAndChargesOpt($row,$miscValue);	

								Log::channel('pricegroup')->info('After misc_fees_and_Charges price: '.$costPrice);
							}
							if($formulaKey == "credit_card_fees")
							{
								Log::channel('pricegroup')->info('Before credit_card_fees price: '.$costPrice);

								$costPrice += ($priceGroup->credit_card_fees * $row->cost)/100;

								Log::channel('pricegroup')->info('After credit_card_fees price: '.$costPrice);
							}
							if($formulaKey == "marketplace_fees")
							{
								Log::channel('pricegroup')->info('Before marketplace_fees price: '.$costPrice);

								$costPrice += ($priceGroup->marketplace_fees * $row->cost)/100;

								Log::channel('pricegroup')->info('After marketplace_fees price: '.$costPrice);
							}
							if($formulaKey == "weight_multiplier")
							{
								Log::channel('pricegroup')->info('Before weight_multiplier price: '.$costPrice);

								$weight = $row->weight > $row->dimensional_weight ? ceil($row->weight) : ceil($row->dimensional_weight);
								$costPrice += $priceGroup->weight_multiplier * $weight;

								Log::channel('pricegroup')->info('After weight_multiplier price: '.$costPrice);
							}
						  }

						  foreach($formula as $formulaKey => $formulaVal){
							  if($formulaKey == "fuel_surcharge"){

								Log::channel('pricegroup')->info('Before fuel_surcharge price: '.$costPrice);

								  $fuelSurcharge += $this->priceGroupService->baseShippingCostOpt($row,$weight,$warehouseAssigned,$miscValue);

								Log::channel('pricegroup')->info('baseShippingCost fuel_surcharge price: '.$fuelSurcharge);

								  $fuelSurcharge += $this->priceGroupService->additionalHandlingCostOpt($miscValue,$query1);

								Log::channel('pricegroup')->info('additionalHandlingCostOpt fuel_surcharge price: '.$fuelSurcharge);

								  $fuelSurcharge += $this->priceGroupService->residentialSurchargeCostOpt($formulaCalculation,$query1);

								Log::channel('pricegroup')->info('residentialSurchargeCostOpt fuel_surcharge price: '.$fuelSurcharge);
								  
								  $fuelSurcharge += $this->priceGroupService->remoteAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

								Log::channel('pricegroup')->info('remoteAreaSurchargeCostOpt fuel_surcharge price: '.$fuelSurcharge);
								  
								  $fuelSurcharge += $this->priceGroupService->deliveryAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

								Log::channel('pricegroup')->info('deliveryAreaSurchargeCostOpt fuel_surcharge price: '.$fuelSurcharge);
								  
								  $fuelSurcharge += $this->priceGroupService->extendedDeliveryAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

								Log::channel('pricegroup')->info('extendedDeliveryAreaSurchargeCostOpt fuel_surcharge price: '.$fuelSurcharge);
								  
								  $fuelSurcharge += $this->priceGroupService->peakSurchargeCostOpt($query);

								Log::channel('pricegroup')->info('peakSurchargeCostOpt fuel_surcharge price: '.$fuelSurcharge);
								  
								  $fuelSurcharge += $this->priceGroupService->peakAdditionalSurchargeCostOpt($miscValue,$query);
								
								Log::channel('pricegroup')->info('peakAdditionalSurchargeCostOpt fuel_surcharge price: '.$fuelSurcharge);
								  
								  $costPrice +=  $this->priceGroupService->fuelSurchargeOpt($fuelSurcharge,$query2);

								Log::channel('pricegroup')->info('After fuel_surcharge price: '.$costPrice);
							  }
						  }

						//Total Cost
						foreach($formula as $formulaKey => $formulaVal){
							if($formulaKey == "total_cost"){
								Log::channel('pricegroup')->info('Before total_cost price: '.$costPrice);
								$costPrice += $this->priceGroupService->masterProductCost($row,$formulaVal);

								$costPrice += $this->priceGroupService->packagingCoolantCostOpt($row,$formulaVal,$groupFormula,$miscValue);

								$costPrice += $this->priceGroupService->packagingMateriaCost($row,$formulaVal,$groupFormula,$miscValue);

								$costPrice += $this->priceGroupService->additionalHandlingCostOpt($miscValue,$query1);

								$costPrice += $this->priceGroupService->residentialSurchargeCostOpt($formulaCalculation,$query1);

								$costPrice += $this->priceGroupService->remoteAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

								$costPrice += $this->priceGroupService->deliveryAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

								$costPrice += $this->priceGroupService->extendedDeliveryAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

								$costPrice += $this->priceGroupService->peakSurchargeCostOpt($query);

								$costPrice += $this->priceGroupService->peakAdditionalSurchargeCostOpt($miscValue,$query);

								$weight = $row->weight > $row->dimensional_weight ? ceil($row->weight) : ceil($row->dimensional_weight);
								$warehouseAssigned = count(explode(',',$row->warehouses_assigned));

								$costPrice += $this->priceGroupService->baseShippingCostOpt($row,$weight,$warehouseAssigned,$miscValue);

								$costPrice += $this->priceGroupService->miscFeesAndChargesOpt($row,$miscValue);

								$costPrice += ($priceGroup->credit_card_fees * $row->cost)/100;

								$costPrice += ($priceGroup->marketplace_fees * $row->cost)/100;

								$weight = $row->weight > $row->dimensional_weight ? ceil($row->weight) : ceil($row->dimensional_weight);
								$costPrice += $priceGroup->weight_multiplier * $weight;

								Log::channel('pricegroup')->info('After total_cost price: '.$costPrice);
							}
						}

						$priceBlock = $costPrice;
						$markupPrice = 0;

						  foreach($formula as $formulaKey => $formulaVal){
							  if($formulaKey == "markup_price_group")
							{
								Log::channel('pricegroup')->info('Before price block markup_price_group: '.$priceBlock);

								$markupPrice += (floatval($priceGroup->markup_price_group) * $costPrice)/100;

								Log::channel('pricegroup')->info('After price block markup_price_group: '.$priceBlock);
							}
							if($formulaKey == "markup_total_cost")
							{
								Log::channel('pricegroup')->info('Before price block markup_total_cost: '.$priceBlock);

								$markupPrice += (floatval($priceGroup->markup_total_cost) * $costPrice) / 100;

								Log::channel('pricegroup')->info('After price block markup_total_cost: '.$priceBlock);
							}
							if($formulaKey == "markup_product_materials_cost")
							{

								Log::channel('pricegroup')->info('Before price block markup_product_materials_cost: '.$priceBlock);

								$markupPrice += (floatval($priceGroup->markup_product_materials_cost) * $costPrice)/100;

								Log::channel('pricegroup')->info('After price block markup_product_materials_cost: '.$priceBlock);
							}
								
						  }
						  // Check if markup price is 0
						  $priceBlock = $markupPrice ? $markupPrice : $priceBlock;

						  Log::channel('pricegroup')->info('Final Price Cost: '.$priceBlock);

						Log::channel('pricegroup')->info('*********************************************');
						  
						return round($priceBlock,2);
					}
					return $row->cost;
				})
				->addColumn('inserted_by', function ($data) {
					$inserted_by = '';
					if ($data->inserted_by != NULL) {
						$pro = DB::table('users')->select('name')->where('id', $data->inserted_by)->first();
						if ($pro) {
							$inserted_by = $pro->name;
						}
					}
					return $inserted_by;
				})
				->addIndexColumn()
				->addColumn('action', function ($row) {
					$btn = '';
					if ($row->item_form_description == 'Kit') {
						$btn = '<a href="' . route('kits.edit', $row->id) . '"  class="edit btn btn-primary btn-sm">Edit Product</a>';
					} else {
						if (ReadWriteAccess('NewProductsPendingApprovalEditProduct')) {
							$btn = '<a href="' . route('editmasterproduct', $row->id) . '" class="edit btn btn-primary btn-sm">Edit Product</a>';
						}
					}
					return $btn;
				})
				->addColumn('approve_check', function ($row) {
					$checkbox = '<input class="form-check-input newApproveCheckBox" style="margin-left:-5px" type="checkbox" id="new_approve_' . $row->id . '" name="new_approve[]" value="' . $row->id . '">';
					return $checkbox;
				})
				->rawColumns(['action', 'approve_check', 'inserted_by'])
				->setTotalRecords($total)
				->setFilteredRecords($total)
				->skipPaging()
				->make(true);
		}
	}
}
