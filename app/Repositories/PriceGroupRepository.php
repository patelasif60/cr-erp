<?php

namespace App\Repositories;
use App\SubCost;
use App\PriceGroup;
use App\GroupFormula;
use App\PriceGroupCostBlock;

/**
 * Repository class for model.
 */
class PriceGroupRepository extends BaseRepository
{
    public function getPriceGroups($request){
        return PriceGroup::all();
    }
    public function store($request,$masterCost){
        $data = $request->all();
        $subCostArray = explode(',',$data['sub_cost_id']);
        $subcost = SubCost::whereIn('id',$subCostArray)->get();
        $seletedMasterCostArray = array_unique($subcost->pluck('master_cost_id')->toArray());

        // if(count($seletedMasterCostArray) != count($masterCost))
        // {
        //     return false;
        // }
        // if($data['group_formula'] == 'Multiply'){
        //     $data['group_formula'] = $data['parcentage'];
        // }

        $result = PriceGroup::create([
            'group_name' => $data['group_name'],
            'store_automator_id' => $data['store_automator_id'],
            'description' => $data['description'],
            'group_type' => $data['group_type'],
            'lobs' => $data['lobs'],
            'chanel_ids' => $data['chanel_ids'],
            'carrier_id' => $data['carrier_id'],
            'credit_card_fees' =>$data['credit_card_fees'],
            'marketplace_fees' => $data['marketplace_fees'],
            'weight_multiplier' =>$data['weight_multiplier'],
            'markup_price_group' =>$data['markup_price_group'],
            'markup_total_cost' =>$data['markup_total_cost'],
            'markup_product_materials_cost' =>$data['markup_product_materials_cost'],
        ]);

        if($data['group_formula']){
            foreach ($data['group_formula'] as $key => $value) {
                if($value == "Multiply"){
                    $formula = $data['parcentage'][$key];
                }else{
                    $formula = $value;
                }
                GroupFormula::create([
                    'group_id' => $result->id,
                    'formula_for' => str_replace("'",'',$key),
                    'group_formula' => $formula,
                ]);
            }
        }

        $finalArray;
        $formulaArray = [];

        foreach($subcost as $subcostkey =>$subcostVal){
            foreach(json_decode($subcostVal->cost_formula) as $formulaKey=>$formulaVal){
               //dd($formulaVal);
               // foreach($formulaVal as $key=>$val){
               //      $finalArray[$formulaKey][]= $val;
               //  }
                $formulaArray[$formulaKey] = $formulaVal;
            }
        }
        PriceGroupCostBlock::create([
            'price_group_id' => $result->id,
            'cost_block' => json_encode($formulaArray),
            'sub_cost_ids' => json_encode($subCostArray)
        ]);
        return true;
    }

    public function edit($id){
        $result['priceGroup'] =  PriceGroup::with('group_formulas')->find($id);
        $result['subCost'] =  $subcost = SubCost::whereIn('id',json_decode($result['priceGroup']->priceGroupCostBlock->sub_cost_ids))->get();
        return $result;
    }

    public function delete($id){
        PriceGroup::where('id',$id)->delete();
        PriceGroupCostBlock::where('price_group_id',$id)->delete();
    }
    public function update($request,$id,$masterCost){
        $data = $request->all();

        $subCostArray = explode(',',$data['sub_cost_id']);
        $subcost = SubCost::whereIn('id',$subCostArray)->get();
        $seletedMasterCostArray = array_unique($subcost->pluck('master_cost_id')->toArray());
        // if(count($seletedMasterCostArray) != count($masterCost))
        // {
        //     return false;
        // }

        if($data['group_formula']){
            foreach ($data['group_formula'] as $key => $value) {

                if($value == "Multiply"){
                    $formula = $data['parcentage'][$key];
                }else{
                    $formula = $value;
                }

                GroupFormula::updateOrCreate([
                    'group_id' => $id,
                    'formula_for' => $key
                ],[
                    'group_formula' => $formula,
                ]);
            }
        }
        // dd($request->all());
        $priceGroup = PriceGroup::find($id);
        $priceGroup->group_name = $request->group_name;
        $priceGroup->description = $request->description;
        $priceGroup->group_type = $request->group_type;
        $priceGroup->store_automator_id = $request->store_automator_id;
        $priceGroup->lobs = $request->lobs;
        $priceGroup->chanel_ids = $request->chanel_ids;
        $priceGroup->carrier_id = $request->carrier_id;
        $priceGroup->credit_card_fees = $request->credit_card_fees;
        $priceGroup->marketplace_fees = $request->marketplace_fees;
        $priceGroup->weight_multiplier = $request->weight_multiplier;
        $priceGroup->markup_price_group = $request->markup_price_group;
        $priceGroup->markup_total_cost = $request->markup_total_cost;
        $priceGroup->markup_product_materials_cost = $request->markup_product_materials_cost;
        $priceGroup->save();

        $finalArray;
        $formulaArray = [];
        foreach($subcost as $subcostkey =>$subcostVal){
            foreach(json_decode($subcostVal->cost_formula) as $formulaKey=>$formulaVal){
               // foreach($formulaVal as $key=>$val){
               //      $finalArray[$formulaKey][]= $val;
               //  }
               $formulaArray[$formulaKey] = $formulaVal;
            }
        }
        PriceGroupCostBlock::where('price_group_id',$id)->update(['cost_block' => json_encode($formulaArray),'sub_cost_ids' => json_encode($subCostArray)]);
        // PriceGroupCostBlock::create([
        //     'price_group_id' => $result->id,
        //     'cost_block' => json_encode($formulaArray),
        //     'sub_cost_ids' => json_encode($subCostArray)
        // ]);
        return true;
    }
}
