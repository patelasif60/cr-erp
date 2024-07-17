<?php
namespace App\Services;

use App\Repositories\PriceGroupRepository;
use DB;
use Illuminate\Support\Facades\Log;

/**
 * supplier class to handle operator interactions.
 */

class PriceGroupService
{
    public function __construct(PriceGroupRepository $repository)
    {
        $this->repository = $repository;
    }
    public function getPriceGroups($request){
        return $this->repository->getPriceGroups($request);
    }
    public function store($request,$masterCost){
       return $this->repository->store($request,$masterCost);
    }
    public function edit($id){
        return $this->repository->edit($id);
    }
    public function update($request,$id,$masterCost){
        return $this->repository->update($request,$id,$masterCost);
    }

    public function delete($id){
        return $this->repository->delete($id);
    }

    public function masterProductCost($row,$formulaVal)
    {
        $costPrice = 0;
        foreach($formulaVal as $key=>$val){
            foreach($val as $formkey=>$formVal){
                Log::channel('pricegroup')->info('masterProductCost() : '.$formVal);
                if(isset($row->$formVal)){                   
                    $costPrice += floatval($row->$formVal);   
                }
            }
        }
        
        return $costPrice;
    }
    public function packagingCoolantCost($row,$formulaVal,$groupFormula){
        $costPrice = 0;
        $formulaCalculation = $groupFormula->pluck('group_formula','formula_for');
        foreach($formulaVal as $key=>$val){     
            $temp = $val[0];
            $groupCalculation = $formulaCalculation['coolant_cost'];
            if($val[0] == 'packaging_and_material_cost')
            {
                $groupCalculation = $formulaCalculation['package_and_matirial'];
                $temp = $val[0].'_'.$row->product_temperature;
            }
            if($row->product_temperature != 'Frozen'){
                $query = DB::table('misc_cost_values')->where('column_name',$temp)->select('value')->first();
                if(isset($query->value)){
                    if($groupCalculation != 'Exact'){
                        $costPrice += floatval($query->value) * floatval($groupCalculation); 
                    }else{
                        $costPrice += floatval($query->value);    
                    }   
                }
            }
            else{
                if($val[0] == 'coolant_cost')
                {
                    $amount= 0;
                    $query = DB::table('misc_cost_values')->where('column_name',$temp)->select('value')->first();
                    $query1 = DB::table('misc_cost_values')->where('column_name','packaging_and_material_cost_frozen')->select('value')->first();
                    if(isset($query->value)){
                        $amount +=  floatval($query->value);
                    }
                    if(isset($query1->value)){
                        $amount +=  floatval($query1->value);
                    }
                    //$amount +=  $query1->value;
                    //if(isset($query->value)){
                        if($groupCalculation != 'Exact'){
                            $costPrice += $amount * floatval($groupCalculation); 
                        }else{
                            $costPrice += $amount;    
                        }
                    //}
                }
                else{
                    $query = DB::table('misc_cost_values')->where('column_name',$temp)->select('value')->first();
                    $amount =  floatval($query->value);
                    if(isset($query->value)){
                        if($groupCalculation != 'Exact'){
                            $costPrice += floatval($query->value) * floatval($groupCalculation); 
                        }else{
                            $costPrice += floatval($query->value);    
                        }
                    }
                }
            }
        }
        return $costPrice;
    }
    public function additionalHandlingCost($formulaVal,$carrierId){
        $query = DB::table('misc_cost_values')->where('column_name','additional_handling')->select('value')->first();
        $query1 = DB::table('carrier_standard_fees')->where('carrier_id',$carrierId)->select('weight_gt_50_lbs_3')->first();
        if(isset($query->value)  &&  isset($query1->weight_gt_50_lbs_3))
        {
            return  floatval($query->value) * floatval($query1->weight_gt_50_lbs_3) ;    
        }
        else if(isset($query->value)  &&  !isset($query1->weight_gt_50_lbs_3))
        {
            return  floatval($query->value);    
        }
        else if(!isset($query->value)  &&  isset($query1->weight_gt_50_lbs_3))
        {
            return  floatval($query1->weight_gt_50_lbs_3);    
        }
        else{
            return 0;
        }
    }
    public function residentialSurchargeCost($formulaVal,$formulaCalculation,$carrierId){
        $costPrice = 0;
        $groupCalculation = $formulaCalculation['residential_surcharge'];
        $query1 = DB::table('carrier_standard_fees')->where('carrier_id',$carrierId)->select('residential_surcharge_ground')->first();
        if(isset($query1->residential_surcharge_ground)){
            if($groupCalculation != 'Exact'){
                $costPrice += floatval($query1->residential_surcharge_ground) * floatval($groupCalculation); 
            }else{
                $costPrice += floatval($query1->residential_surcharge_ground);    
            }
        }
        return $costPrice;
    }
    public function remoteAreaSurchargeCost($formulaVal,$formulaCalculation,$carrierId){
        $amount= 0;
        $amount1=0;
        $groupCalculation = $formulaCalculation['remote_area_surcharge'];
        $query = DB::table('misc_cost_values')->where('column_name','remote_area_surcharge')->select('value')->first();
        $query1 = DB::table('carrier_standard_fees')->where('carrier_id',$carrierId)->select('continental_us_ground')->first();
        if(isset($query->value)){
            $amount =  floatval($query->value);
        }
        if(isset($query1->continental_us_ground)){
            $amount1 =  floatval($query1->continental_us_ground);
        }
        if($groupCalculation != 'Exact'){
            $costPrice = ($amount * $amount1) * floatval($groupCalculation); 
        }else{
            $costPrice = ($amount * $amount1);    
        }
        return  $costPrice;

    }
    public function deliveryAreaSurchargeCost($formulaVal,$formulaCalculation,$carrierId){
        $amount= 0;
        $amount1=0;
        $groupCalculation = $formulaCalculation['delivary_area_surcharge'];
        $query = DB::table('misc_cost_values')->where('column_name','delivery_area_surcharge')->select('value')->first();
        $query1 = DB::table('carrier_standard_fees')->where('carrier_id',$carrierId)->select('residential_ground')->first();
        if(isset($query->value)){
            $amount =  floatval($query->value);
        }
        if(isset($query1->residential_ground)){
            $amount1 =  floatval($query1->residential_ground);
        }
        if($groupCalculation != 'Exact'){
            $costPrice = ($amount * $amount1) * floatval($groupCalculation); 
        }else{
            $costPrice = ($amount * $amount1);    
        }
        return  $costPrice;
        //return  $query->value * $query1->residential_ground;
    }
    public function extendedDeliveryAreaSurchargeCost($formulaVal,$formulaCalculation,$carrierId){
        $amount= 0;
        $amount1=0;
        $groupCalculation = $formulaCalculation['extended_delivary_area_surcharge'];
        $query = DB::table('misc_cost_values')->where('column_name','extended_delivery_area_surcharge')->select('value')->first();
        $query1 = DB::table('carrier_standard_fees')->where('carrier_id',$carrierId)->select('residential_extended_ground')->first();
        if(isset($query->value)){
            $amount =  floatval($query->value);
        }
        if(isset($query1->residential_extended_ground)){
            $amount1 =  floatval($query1->residential_extended_ground);
        }
        if($groupCalculation != 'Exact'){
            $costPrice = ($amount * $amount1) * floatval($groupCalculation); 
        }else{
            $costPrice = ($amount * $amount1);    
        }
        return  $costPrice;
       // return  $query->value * $query1->residential_extended_ground;
    }
    public function peakSurchargeCost($formulaVal,$carrierId){
        $query1 = DB::table('carrier_peak_surchrges')->where('carrier_id',$carrierId)->select('ground_residential')->first();
        if(isset($query1->ground_residential)){
            return floatval($query1->ground_residential);
        }
        return 0;
    }
    public function peakAdditionalSurchargeCost($formulaVal,$carrierId){
        $query1 = DB::table('carrier_peak_surchrges')->where('carrier_id',$carrierId)->select('additional_handling')->first();
        $query = DB::table('misc_cost_values')->where('column_name','additional_handling')->select('value')->first();
        if(isset($query->value)  &&  isset($query1->additional_handling))
        {
            return  floatval($query->value) * floatval($query1->additional_handling);    
        }
        else if(isset($query->value)  &&  !isset($query1->additional_handling))
        {
            return  floatval($query->value);    
        }
        else if(!isset($query->value)  &&  isset($query1->additional_handling))
        {
            return  floatval($query1->additional_handling);    
        }
        else{
            return 0;
        }        
        //return $query->value * $query1->additional_handling;
    }
    public function baseShippingCost($row){
        $warehouseAssigned = count(explode(',',$row->warehouses_assigned));
        $colName = 'shipping_cost_base_'.$warehouseAssigned.'_wh';
        $zone = 'zone'.$warehouseAssigned;
        if($warehouseAssigned == 1){
            $zone = 'zone8';
        }
        $query = DB::table('misc_cost_values')->where('column_name',$colName)->select('value')->first();
        $weight = $row->weight > $row->dimensional_weight ? round($row->weight) : round($row->dimensional_weight);
        
        $upsZone =DB::table('ups_zone_rates_by_ground')->where('id',$weight)->select($zone)->first();
        $value1 = isset($query->value) ? $query->value : 0 ;
        $value2 = isset($upsZone->$zone) ? $upsZone->$zone  : 0;
        //return $query->value;
        return floatval($value1) + floatval($value2); 
    }
    public function miscFeesAndCharges($formulaVal){
        $query = DB::table('misc_cost_values')->where('column_name','labor_cost')->select('value')->first();
        $query1 = DB::table('misc_cost_values')->where('column_name','overhead_expenses')->select('value')->first();
        $value1 = isset($query->value) ? $query->value : 0 ;
        $value2 = isset($query1->value) ? $query1->value  : 0;
        return floatval($value1) + floatval($value2);
        //return isset($query->value) ? $query->value : 0 + isset($query1->value) ? $query1->value : 0 ; 
    }
    public function fuelSurcharge($fuelSurcharge,$carrierId){
        $query = DB::table('carrier_dynamic_fees')->where('carrier_id',$carrierId)->where('effective_date',date('Y-m-d'))->select('ground')->first();
        $data = isset($query->ground) ? $query->ground : 1;
        return (floatval($data) * $fuelSurcharge ) /100;
    }
    public function totalShippingCost($row,$formulaVal,$formulaCalculation,$carrierId){
        $costPrice = 0;
        $costPrice += $this->baseShippingCost($row,$formulaVal);
        $costPrice += $this->additionalHandlingCost($formulaVal,$carrierId);
        $costPrice += $this->residentialSurchargeCost($formulaVal,$formulaCalculation,$carrierId);
        $costPrice += $this->remoteAreaSurchargeCost($formulaVal,$formulaCalculation,$carrierId);
        $costPrice += $this->deliveryAreaSurchargeCost($formulaVal,$formulaCalculation,$carrierId);
        $costPrice += $this->extendedDeliveryAreaSurchargeCost($formulaVal,$formulaCalculation,$carrierId);
        $costPrice += $this->peakSurchargeCost($formulaVal,$carrierId);
        $costPrice += $this->peakAdditionalSurchargeCost($formulaVal,$carrierId);
        $costPrice += $this->fuelSurcharge($costPrice,$carrierId);
        return $costPrice;
    }
    public function totalShippingCostOpt($row,$miscValue,$query,$query1,$formulaCalculation,$query2){
        //dd($row);
        $weight = $row->weight > $row->dimensional_weight ? ceil($row->weight) : ceil($row->dimensional_weight);
        $warehouseAssigned = count(explode(',',$row->warehouses_assigned));
        $costPrice = 0;
        $costPrice += $this->baseShippingCostOpt($row,$weight,$warehouseAssigned,$miscValue);
        // $costPrice += $this->additionalHandlingCostOpt($miscValue,$query1);
        // $costPrice += $this->residentialSurchargeCostOpt($formulaCalculation,$query1);
        // $costPrice += $this->remoteAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);
        // $costPrice += $this->deliveryAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);
        // $costPrice += $this->extendedDeliveryAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);
        // $costPrice += $this->peakSurchargeCostOpt($query);
        // $costPrice += $this->peakAdditionalSurchargeCostOpt($miscValue,$query);
        // $costPrice += $this->fuelSurcharge($costPrice,$query2);
        return $costPrice;
    }
    public function additionalHandlingCostOpt($miscValue,$query1){
        if(isset($miscValue['additional_handling'])  &&  isset($query1->weight_gt_50_lbs_3))
        {
            return  floatval($miscValue['additional_handling']) * floatval($query1->weight_gt_50_lbs_3);
        }
        else if(isset($miscValue['additional_handling'])  &&  !isset($query1->weight_gt_50_lbs_3))
        {
            return  floatval($miscValue['additional_handling']);    
        }
        else if(!isset($query->value)  &&  isset($query1->weight_gt_50_lbs_3))
        {
            return  floatval($query1->weight_gt_50_lbs_3);    
        }
        else{
            return 0;
        }
    }
    public function residentialSurchargeCostOpt($formulaCalculation,$query1){
        $costPrice = 0;
        $groupCalculation = $formulaCalculation['residential_surcharge'];
        if(isset($query1->residential_surcharge_ground)){
            if($groupCalculation != 'Exact'){
                $costPrice += floatval($query1->residential_surcharge_ground) * floatval($groupCalculation); 
            }else{
                $costPrice += floatval($query1->residential_surcharge_ground);    
            }
        }
        return $costPrice;
    }
    public function remoteAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1){
        $amount= 0;
        $amount1=0;
        $groupCalculation = $formulaCalculation['remote_area_surcharge'];
        if(isset($miscValue['remote_area_surcharge'])){
            $amount =  floatval($miscValue['remote_area_surcharge']);
        }
        if(isset($query1->continental_us_ground)){
            $amount1 =  floatval($query1->continental_us_ground);
        }
        if($groupCalculation != 'Exact'){
            $costPrice = ($amount * $amount1) * floatval($groupCalculation); 
        }else{
            $costPrice = ($amount * $amount1);    
        }
        return  $costPrice;

    }
    public function extendedDeliveryAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1){
        $amount= 0;
        $amount1=0;
        $groupCalculation = $formulaCalculation['extended_delivary_area_surcharge'];
        if(isset($miscValue['extended_delivery_area_surcharge'])){
            $amount =  floatval($miscValue['extended_delivery_area_surcharge']);
        }
        if(isset($query1->residential_extended_ground)){
            $amount1 =  floatval($query1->residential_extended_ground);
        }
        if($groupCalculation != 'Exact'){
            $costPrice = ($amount * $amount1) * floatval($groupCalculation); 
        }else{
            $costPrice = ($amount * $amount1);    
        }
        return  $costPrice;
    }
    public function deliveryAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1){
        $amount= 0;
        $amount1=0;
        $groupCalculation = $formulaCalculation['delivary_area_surcharge'];
        if(isset($miscValue['delivery_area_surcharge'])){
            $amount =  floatval($miscValue['delivery_area_surcharge']);
        }
        if(isset($query1->residential_ground)){
            $amount1 =  floatval($query1->residential_ground);
        }
        if($groupCalculation != 'Exact'){
            $costPrice = ($amount * $amount1) * floatval($groupCalculation); 
        }else{
            $costPrice = ($amount * $amount1);    
        }
        return  $costPrice;
    }
    public function peakSurchargeCostOpt($query1){
        if(isset($query1->ground_residential)){
            return floatval($query1->ground_residential);
        }
        return 0;
    }
    public function peakAdditionalSurchargeCostOpt($miscValue,$query1){
        if(isset($miscValue['additional_handling'])  &&  isset($query1->additional_handling))
        {
            return  floatval($miscValue['additional_handling']) * floatval($query1->additional_handling);
        }
        else if(isset($miscValue['additional_handling'])  &&  !isset($query1->additional_handling))
        {
            return  floatval($miscValue['additional_handling']);
        }
        else if(!isset($miscValue['additional_handling'])  &&  isset($query1->additional_handling))
        {
            return  floatval($query1->additional_handling);    
        }
        else{
            return 0;
        }        
        //return $query->value * $query1->additional_handling;
    }
    public function fuelSurchargeOpt($fuelSurcharge,$query){
        $data = isset($query->ground) ? $query->ground : 1;
        return (floatval($data) * $fuelSurcharge ) /100;
    }
    public function miscFeesAndChargesOpt($row,$miscValue){
        $value1 = isset($miscValue['labor_cost']) ? $miscValue['labor_cost'] : 0 ;
        $value2 = isset($miscValue['overhead_expenses']) ? $miscValue['overhead_expenses']  : 0;
        // if($row->ETIN == "ETFZ-1000-2975"){
        //             echo "misc =";
        //             print_r(floatval($value1) + floatval($value2));
        //         }
        return floatval($value1) + floatval($value2);
        //return isset($query->value) ? $query->value : 0 + isset($query1->value) ? $query1->value : 0 ; 
    }
    public function packagingCoolantCostOpt($row,$formulaVal,$groupFormula,$miscValue){
        $costPrice = 0;
        $formulaCalculation = $groupFormula->pluck('group_formula','formula_for');
        foreach($formulaVal as $key=>$val){     
            $temp = strtolower($val[0]);
            $groupCalculation = $formulaCalculation['coolant_cost'];
            
            if($val[0] == 'coolant_cost' && $row->product_temperature == 'Frozen')
            {
                $amount= 0;
                if(isset($miscValue[$temp])){
                    $amount +=  floatval($miscValue[$temp]);
                }
                
                if($groupCalculation != 'Exact'){
                    $costPrice = $amount * floatval($groupCalculation); 
                }else{
                    $costPrice += $amount;    
                }
            }
        }
        return $costPrice;
    }

    public function packagingMateriaCost($row,$formulaVal,$groupFormula,$miscValue){
        $costPrice = 0;
        $formulaCalculation = $groupFormula->pluck('group_formula','formula_for');
        foreach($formulaVal as $key=>$val){     
            $temp = strtolower($val[0]);
            $groupCalculation = $formulaCalculation['package_and_matirial'];
            if($val[0] == 'packaging_and_material_cost')
            {
                if($row->product_temperature == 'Dry-Strong' || $row->product_temperature == 'Dry-Perishable' || $row->product_temperature == 'Dry-Fragile')
                {
                    $temp = $val[0].'_dry';
                }
                else
                {
                    $temp = $val[0].'_'.strtolower($row->product_temperature);
                }

                if(isset($miscValue[$temp])){
                    if($groupCalculation != 'Exact'){
                        $costPrice += floatval($miscValue[$temp]) * floatval($groupCalculation); 
                    }else{
                        $costPrice = floatval($miscValue[$temp]);
                    }   
                }
                
            } 
        }
        return $costPrice;
    }

    public function baseShippingCostOpt($row,$weight,$warehouseAssigned,$miscValue){
        $colName = 'shipping_cost_base_'.$warehouseAssigned.'_wh';
        $zone = 'zone'.intval($miscValue[$colName]);
        $upsZone =DB::table('ups_zone_rates_by_ground')->where('id',$weight)->select($zone)->first();
        $value2 = isset($upsZone->$zone) ? $upsZone->$zone  : 0;
        // if($row->ETIN == "ETFZ-1000-2975"){
        //             echo "bas =";
        //             print_r($value2);
        // }
        return  floatval($value2); 
    }
}