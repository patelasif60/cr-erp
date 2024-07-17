<?php

namespace App\Repositories;
use App\MaterialType;
use App\PackagingcomponentsSetting;

/**
 * Repository class for model.
 */
class PackagingComponantSettingRepository extends BaseRepository
{
    public function getPackagematirial(){
        // return MaterialType::where('Material_type','!=','Shipping Box')->get();
        return MaterialType::get();
    }
    public function getPackagematirialSetting($id){
        return PackagingcomponentsSetting::where('parent_packaging_material_id',$id)->get();
    }
    public function getTempComponents($request){
        return PackagingcomponentsSetting::where('parent_packaging_material_id',$request->parentId)->where('product_temperature_id',$request->tempId)->get();
    }
    public function update($request,$id)
    {
        PackagingcomponentsSetting::where('parent_packaging_material_id',$id)->where('product_temperature_id',$request->temp_id)->delete();
        //dd($request->components);
        foreach($request->components as $key =>$val){
            PackagingcomponentsSetting::create([
                'parent_packaging_material_id'   => $id,
                'product_temperature_id'   => $request->temp_id,
                'child_packaging_materials_id' =>$key,
                'qty' =>$val,
            ]);    
        }
    }
}