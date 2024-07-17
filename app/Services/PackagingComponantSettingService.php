<?php
namespace App\Services;

use App\PackagingMaterials;
use App\CustomClientOuterBox;
use Illuminate\Support\Facades\DB;
use App\Repositories\PackagingComponantSettingRepository;

/**
 * supplier class to handle operator interactions.
 */

class PackagingComponantSettingService
{

    public function __construct(PackagingComponantSettingRepository $repository)
    {
        $this->repository = $repository;
    }
    public function getPackagematirial($request){
        $packagingComponantSetting = $this->repository->getPackagematirial();
        if($request->type == 'settings'){
            if($request->ids)
            {
                $selectedPackage = (array) json_decode($request->ids);
                $arr = array_keys($selectedPackage);

                $ccob = CustomClientOuterBox::where('box_id', $arr[0])->first();
                $box_ids = [];
                if (isset($ccob)) {
                    $bids = DB::select('select box_id from custom_client_outer_boxes ccob 
                            left join packaging_materials pm on ccob.box_id = pm.id 
                            where ccob.client_id = '.$ccob->client_id.' and pm.material_type_id <> 1');
                    if (isset($bids) && count($bids) > 0) {
                        foreach($bids as $bid) { array_push($box_ids, $bid->box_id); }
                    }
                }

                return  
                isset($box_ids) && count($box_ids) > 0
                ? PackagingMaterials::whereIn('id', $box_ids)->groupBy('product_description')->get()
                : PackagingMaterials::whereIn('material_type_id',$packagingComponantSetting->pluck('id')->toArray())
                    ->whereNotIn('id',$arr)->groupBy('product_description')->get();
                //return $packagingComponantSetting->first()->packagingMaterials()->whereNotIn('id',$arr)->get(); 
            }
        }
        return $packagingComponantSetting;
    }
    public function getPackagematirialSetting($id){
        return $this->repository->getPackagematirialSetting($id);
    }
    public function getTempComponents($request){
       return $this->repository->getTempComponents($request);
    }
    public function update($request,$id)
    {
        $this->repository->update($request,$id);
    }
}