<?php

namespace App\Services;
use App\PackagingMaterials;
use App\Supplier;

use App\Repositories\SupplierRepository;

/**
 * supplier class to handle operator interactions.
 */

class SupplierService
{

    public function __construct(SupplierRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Handle logic to create a supplier.
     *
     * @param $data
     *
     * @return mixed
     */
    public function create($data)
    {
        //dd($data['warehouses_assigned']);
        $data['warehouse'] = $data['warehouses_assigned'] ? implode(',',$data['warehouses_assigned']) : '';
        $data['time_zone_id'] = $data['time_zone_id'] ?? 0;
        $data['account_manager'] = $data['account_manager'] ?? '';
        $data['sales_manager'] = $data['sales_manager'] ?? '';
        return $this->repository->create($data);
    }
    /**
     * Handle logic to update a supplier.
     *
     * @param $data
     *
     * @return mixed
    */
    public function update($request,$id)
    {
        $request->warehouse = $request->warehouses_assigned ? implode(',',$request->warehouses_assigned) : '';
        $request->account_manager = $request->account_manager ?? '';
        $request->sales_manager = $request->sales_manager ?? '';
        return $this->repository->update($request->all(),$id);
    }

    public function getPackagingBySupplier($id,$request){
        $supplier = $this->repository->getSupplier($id);
        if($request->type == 'kit'){
            if($request->ids)
            {
                $selectedPackage = (array) json_decode($request->ids);
                $selectedPackage[$request->packageId] = 0;
                $arr = array_keys($selectedPackage);
                return $supplier->packagingMaterials()->whereNotIn('id',$arr)->get();
            }
        }
        if($request->type == 'packaginglist'){
            $supplier= Supplier::where('supplier_product_package_type','Package')->get()->pluck('id')->toArray();
            return PackagingMaterials::where('status','Active')->whereIn('supplier_id',$supplier)->get();
        }
        return $supplier->packagingMaterials()->get();

    }
    public function editpackagematerial($id){
       return $this->repository->editpackagematerial($id);
    }
    public function addPackageMaterialStore ($data){
        $data['warehouses_assigned'] = isset($data['warehouse']) ? implode(',',$data['warehouse']) : '';
        $data['channel_ids'] = isset($data['channel_ids']) ? implode(',',$data['channel_ids']) : '';
        $data['product_ids'] = isset($data['product_ids']) ? implode(',',$data['product_ids']) : '';
        $data['has_barcode'] = isset($data['has_barcode']) && $data['has_barcode'] == 'on' ? 1 : 0;
        $data['scannable_barcode'] = isset($data['has_barcode']) && $data['has_barcode'] == 1 ? $data['scannable_barcode'] : NULL;
        return $this->repository->addPackageMaterialStore($data);
    }
    public function updatePackageMaterial ($data,$id){
        $data['warehouses_assigned'] = isset($data['warehouse']) ? implode(',',$data['warehouse']) : '';
        $data['channel_ids'] = isset($data['channel_ids']) ? implode(',',$data['channel_ids']) : '';
        $data['product_ids'] = isset($data['product_ids']) ? implode(',',$data['product_ids']) : '';
        $data['has_barcode'] = isset($data['has_barcode']) && $data['has_barcode'] == 'on' ? 1 : 0;
        $data['scannable_barcode'] = isset($data['has_barcode']) && $data['has_barcode'] == 1 ? $data['scannable_barcode'] : NULL;
        return $this->repository->updatePackageMaterial($data,$id);
    }
    public function destroyPackageMaterial($id){
    return $this->repository->destroyPackageMaterial($id);   
    }
    public function deleteSupplierPackgematirial($id){
        return $this->repository->deleteSupplierPackgematirial($id);   
    }
    public function addKitPackageMaterialStore($request)
    {
        if(isset($request->kit_components)){
            foreach($request->kit_components as $row_kit_components){
                if($row_kit_components['components_ETIN'] != '' && $row_kit_components['qty'] != ''){
                    $this->repository->addKitPackageMaterialStore($row_kit_components,$request->ETIN);
                }
            }
        }
    }
    public function updateKitPackageMaterial($request){
        $this->repository->deleteKitPackageMaterial($request);
        $this->addKitPackageMaterialStore($request);
    }
}   