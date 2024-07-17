<?php

namespace App\Repositories;
use App\Supplier;
use App\PackagingMaterials;
use App\PackageKitComponents;

/**
 * Repository class for model.
 */
class SupplierRepository extends BaseRepository
{
    /**
     * create supplier
     *
     * @param $data
     *
     * @return mixed
     */
    public function create($data)
    {
    	return Supplier::create($data);
    }
    /**
     * update supplier
     *
     * @param $data
     *
     * @return mixed
     */
    public function update($data,$id)
    {
    	$supplier = Supplier::find($id);
    	$supplier->fill($data);
    	$supplier->save();
    	return $supplier;
	}
	public function getSupplier($id)
	{
		return Supplier::find($id);

	}
	public function editpackagematerial($id){
        return PackagingMaterials::find($id);
    }
    public function addPackageMaterialStore ($data){
      $count = PackagingMaterials::where('product_description',$data['product_description'])->where('supplier_id',$data['supplier_id'])->count();
      if($count > 0)
      {
      	return false;
      }		
      return PackagingMaterials::create($data);
    }
    public function updatePackageMaterial ($data,$id){
    	$count = PackagingMaterials::where('product_description',$data['product_description'])->where('supplier_id',$data['supplier_id'])->where('id','!=',$id)->count();
    	if($count > 0)
	    {
	      	return false;
	    }
    	$packageMaterial = PackagingMaterials::find($id);
        if($packageMaterial->product_temperature != $data['product_temperature']){
            $explodearray = explode('-', $data['ETIN']);
            $etinmid = NULL;
            if (count($explodearray) > 1){
                $insertmasterproduct['ETIN'] = end($explodearray);
                $etinmid = $explodearray[1];
            } else {
                $insertmasterproduct['ETIN'] = $data['ETIN'];
            }
            if($data['product_temperature'] == "Frozen"){
                $insertmasterproduct['ETIN'] = 'ETFZ-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            } else if($data['product_temperature'] == "Dry-Strong"){
                $insertmasterproduct['ETIN'] = 'ETDS-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            } else if($data['product_temperature'] == "Refrigerated"){
                $insertmasterproduct['ETIN'] = 'ETRF-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            } else if($data['product_temperature'] == "Beverages"){
                $insertmasterproduct['ETIN'] = 'ETBV-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            } else if($data['product_temperature'] == "Dry-Perishable"){
                $insertmasterproduct['ETIN'] = 'ETDP-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            } else if($data['product_temperature'] == "Dry-Fragile"){
                $insertmasterproduct['ETIN'] = 'ETDF-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            } else if($data['product_temperature'] == "Thaw & Serv"){
                $insertmasterproduct['ETIN'] = 'ETTS-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            } else if($data['product_temperature'] == "Packaging"){
                $insertmasterproduct['ETIN'] = 'ETPM-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            } else {
                $insertmasterproduct['ETIN'] = 'ETOT-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            }

            $data['ETIN'] = $insertmasterproduct['ETIN'];
        }
    	$packageMaterial->fill($data);
    	$packageMaterial->save();
        return $packageMaterial;
    }
    public function destroyPackageMaterial($id){
        
        $data = PackagingMaterials::find($id);
        $cnt = PackageKitComponents::where('components_ETIN',$data->ETIN)->count();

        if($cnt > 0){
            return false;
        }else{
            PackageKitComponents::where('ETIN',$data->ETIN)->delete();
            PackagingMaterials::destroy($id);
            return true;
        }

    }
    public function addKitPackageMaterialStore($data,$ETIN)
    {
        PackageKitComponents::create(
        	[
                'ETIN' => $ETIN,
                'components_ETIN' => $data['components_ETIN'],
                'qty' => $data['qty']
            ]
        );
    }
    public function deleteKitPackageMaterial($request){
    	PackageKitComponents::where('ETIN',$request->ETIN)->delete();
    }
    public function deleteSupplierPackgematirial($id){
        $packMatirial = PackagingMaterials::where('supplier_id',$id);
        PackageKitComponents::whereIn('ETIN',$packMatirial->pluck('ETIN')->toArray())->delete();
        $packMatirial->delete();
        return true;   
    }
}