<?php
namespace App\Services;

use App\Repositories\ProductInventoryRepository;
use DB;
use App\MasterShelf;
use App\AisleMaster;
use App\WareHouse;
use App\OrderDetail;
use DataTables;
/**
 * supplier class to handle operator interactions.
 */

class ProductInventoryService
{
    public function __construct(ProductInventoryRepository $repository)
    {
        $this->repository = $repository;
    }
    public function getProductInventorylist($request){
        return $this->repository->getProductInventorylist($request);
    }
    public function store($request){
       return $this->repository->store($request);
    }
    public function update($request){
       return $this->repository->update($request);
    }
    public function getChildProductInventorylist($request){
        return $this->repository->getChildProductInventorylist($request);
    }
    public function eachQty($ETIN,$warehouse_id){
        //dd($ETIN);
        $aisleMaster = $this->repository->eachQty($warehouse_id);
        // if($warehouse_id == 3)
        // {
        //     dd($aisleMaster->shelfFromAisle()->where('ETIN',$ETIN)->sum('cur_qty'));
        // }
        // if($ETIN == 'ETFZ-0000-0001'){
        //     //$data = DB::table('master_product_kit_components')->where('ETIN', $results->ETIN)->get();
        //     //echo  $val->components_ETIN;
        //     //echo $request->warehouse_id;
        //     //dd($data);
        //     //dd($aisleMaster->shelfFromAisle()->where('ETIN',$ETIN)->sum('cur_qty'));
        // }
        if($aisleMaster)
        {
            return $aisleMaster->shelfFromAisle()->where('ETIN',$ETIN)->sum('cur_qty');
        }
        return 0;
    }
    public function masterShelfSum($id,$ETIN)
    {
        $AisleMaster = AisleMaster::where('warehouse_id',$id)->pluck('id')->toArray();
        $masterShelfSum = MasterShelf::where('ETIN',$ETIN)->whereIN('aisle_id',$AisleMaster)->sum('cur_qty');
        return $masterShelfSum;
    }

    public function OpenOrderQty($wh,$ETIN){
        $qty = OrderDetail::where('ETIN',$ETIN)->where('warehouse',$wh)->where('status','1')->sum('quantity_fulfilled');
        return $qty;
    }

    public function fulfilledQty($id,$ETIN)
    {
        $AisleMaster = AisleMaster::where('warehouse_id',$id)->pluck('id')->toArray();
        $masterShelfSum = MasterShelf::where('ETIN',$ETIN)->whereIN('aisle_id',$AisleMaster)->where('location_type_id',1)->sum('cur_qty');
        return $masterShelfSum;
    }

    public function OrderableQty($wh,$ETIN,$OpenOrderQty){
        $warehouse =  WareHouse::where('warehouses',$wh)->first();
        $AisleMaster = AisleMaster::where('warehouse_id',$warehouse->id)->pluck('id')->toArray();
        $masterShelfSum = MasterShelf::where('ETIN',$ETIN)->whereIN('aisle_id',$AisleMaster)->whereIn('location_type_id',[1,2])->sum('cur_qty');
        // $OpenOrderQty = $this->OpenOrderQty($wh,$ETIN);

        return $masterShelfSum - $OpenOrderQty;
    }


    public function datatableData($results)
    {
        return Datatables::of($results)
        //    ->addColumn('WI_qty', function ($results) {
        //         $warehouse =  WareHouse::where('warehouses','WI')->first();
        //         $masterShelfSum = $this->masterShelfSum($warehouse->id,$results->ETIN);
        //         return $masterShelfSum;
        //     })
        //    ->addColumn('PA_qty', function ($results) {
        //         $warehouse =  WareHouse::where('warehouses','PA')->first();
        //         $masterShelfSum = $this->masterShelfSum($warehouse->id,$results->ETIN);
        //         return $masterShelfSum;
        //     })
        //    ->addColumn('NV_qty', function ($results) {
        //         $warehouse =  WareHouse::where('warehouses','NV')->first();
        //         $masterShelfSum = $this->masterShelfSum($warehouse->id,$results->ETIN);
        //         return $masterShelfSum;
        //     })
        //    ->addColumn('OKC_qty', function ($results) {
        //         $warehouse =  WareHouse::where('warehouses','OKC')->first();
        //         $masterShelfSum = $this->masterShelfSum($warehouse->id,$results->ETIN);
        //         return $masterShelfSum;
        //     })
        //     ->addColumn('wi_each_qty', function ($results) {
        //         $warehouse =  WareHouse::where('warehouses','WI')->first();
        //         $masterShelfSum = $this->masterShelfSum($warehouse->id,$results->ETIN);
        //         $mstPro = DB::table('master_product')->where('ETIN',$results->ETIN)->first();
        //         if($mstPro){
        //             return $mstPro->unit_in_pack * $mstPro->pack_form_count * $masterShelfSum;
        //         }else{
        //             return 0;
        //         }
        //     })
        //     ->addColumn('pa_each_qty', function ($results) {
        //         $warehouse =  WareHouse::where('warehouses','PA')->first();
        //         $masterShelfSum = $this->masterShelfSum($warehouse->id,$results->ETIN);
        //         $mstPro = DB::table('master_product')->where('ETIN',$results->ETIN)->first();
        //         if($mstPro){
        //             return $mstPro->unit_in_pack * $mstPro->pack_form_count * $masterShelfSum;
        //         }else{
        //             return 0;
        //         }
        //     })
        //     ->addColumn('NV_each_qty', function ($results) {
        //         $warehouse =  WareHouse::where('warehouses','NV')->first();
        //         $masterShelfSum = $this->masterShelfSum($warehouse->id,$results->ETIN);
        //         $mstPro = DB::table('master_product')->where('ETIN',$results->ETIN)->first();
        //         if($mstPro){
        //             return $mstPro->unit_in_pack * $mstPro->pack_form_count * $masterShelfSum;
        //         }else{
        //             return 0;
        //         }
                
        //     })
        //     ->addColumn('OKC_each_qty', function ($results) {
        //         $warehouse =  WareHouse::where('warehouses','OKC')->first();
        //         $masterShelfSum = $this->masterShelfSum($warehouse->id,$results->ETIN);
        //         $mstPro = DB::table('master_product')->where('ETIN',$results->ETIN)->first();
        //         if($mstPro){
        //             return $mstPro->unit_in_pack * $mstPro->pack_form_count * $masterShelfSum;
        //         }else{
        //             return 0;
        //         }
                
        //     })
        //     ->addColumn('WI_orderable_qty', function ($results) {
        //         return $this->OrderableQty('WI',$results->ETIN);
        //     })
        //     ->addColumn('PA_orderable_qty', function ($results) {
        //         return $this->OrderableQty('PA',$results->ETIN);
        //     })
        //     ->addColumn('NV_orderable_qty', function ($results) {
        //         return $this->OrderableQty('NV',$results->ETIN);
        //     })
        //     ->addColumn('OKC_orderable_qty', function ($results) {
        //         return $this->OrderableQty('OKC',$results->ETIN);
        //     })
        //     ->addColumn('WI_open_order_qty', function ($results) {
        //         return $this->OpenOrderQty('WI',$results->ETIN);
        //     })
        //     ->addColumn('PA_open_order_qty', function ($results) {
        //         return $this->OpenOrderQty('PA',$results->ETIN);
        //     })
        //     ->addColumn('NV_open_order_qty', function ($results) {
        //         return $this->OpenOrderQty('NV',$results->ETIN);
        //     })
        //     ->addColumn('OKC_open_order_qty', function ($results) {
        //         return $this->OpenOrderQty('OKC',$results->ETIN);
        //     })
        //     ->addColumn('WI_fulfilled_qty', function ($results) {
        //         $warehouse =  WareHouse::where('warehouses','WI')->first();
        //         $masterShelfSum = $this->fulfilledQty($warehouse->id,$results->ETIN);
        //         return $masterShelfSum;
        //     })
        //    ->addColumn('PA_fulfilled_qty', function ($results) {
        //         $warehouse =  WareHouse::where('warehouses','PA')->first();
        //         $masterShelfSum = $this->fulfilledQty($warehouse->id,$results->ETIN);
        //         return $masterShelfSum;
        //     })
        //    ->addColumn('NV_fulfilled_qty', function ($results) {
        //         $warehouse =  WareHouse::where('warehouses','NV')->first();
        //         $masterShelfSum = $this->fulfilledQty($warehouse->id,$results->ETIN);
        //         return $masterShelfSum;
        //     })
        //    ->addColumn('OKC_fulfilled_qty', function ($results) {
        //         $warehouse =  WareHouse::where('warehouses','OKC')->first();
        //         $masterShelfSum = $this->fulfilledQty($warehouse->id,$results->ETIN);
        //         return $masterShelfSum;
        //     })
            ->addColumn('action', function($results)
            {
                $flag = DB::table('master_product')->where('is_approve', 1)->where('parent_ETIN',$results->ETIN)->count();

                $btn = '';
                if($flag > 0)
                {
                    $btn .= '<a href="javascript:void(0);" onClick="openChildModal(\''.$results->ETIN.'\')" class="btn btn-primary ml-2">Show Child Product</a>';
                }
                return $btn;

            })->rawColumns(['action'])->make(true);
    }
    public function childDatatableData($results,$request)
    {
        if(count($results) > 0){
            $dbData = DB::table('master_product')->where('is_approve', 1)->where('ETIN',$request->ETIN)->first();
            return Datatables::of($results)
           ->addColumn('WI_qty', function ($results)  use ($request){
                $warehouse =  WareHouse::where('warehouses','WI')->first();
                $masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                return $masterShelfSum;
            })
           ->addColumn('PA_qty', function ($results) use ($request) {
                $warehouse =  WareHouse::where('warehouses','PA')->first();
                $masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                return $masterShelfSum;
            })
           ->addColumn('NV_qty', function ($results)  use ($request){
                $warehouse =  WareHouse::where('warehouses','NV')->first();
                $masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                return $masterShelfSum;
            })
           ->addColumn('OKC_qty', function ($results) use ($request) {
                $warehouse =  WareHouse::where('warehouses','OKC')->first();
                $masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                return $masterShelfSum;
            })
            ->addColumn('wi_each_qty', function ($results) use ($request,$dbData) {
                $warehouse =  WareHouse::where('warehouses','WI')->first();
                $masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                $price= $dbData->pack_form_count * $dbData->unit_in_pack;
                return $price >0 ? ($masterShelfSum * $price) : 0; 
            })
            ->addColumn('pa_each_qty', function ($results) use ($request,$dbData) {
                $warehouse =  WareHouse::where('warehouses','PA')->first();
                $masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                $price= $dbData->pack_form_count * $dbData->unit_in_pack;
                return $price >0 ? ($masterShelfSum * $price) : 0;
            })
            ->addColumn('NV_each_qty', function ($results) use ($request,$dbData) {
                $warehouse =  WareHouse::where('warehouses','NV')->first();
                $masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                $price= $dbData->pack_form_count * $dbData->unit_in_pack;
                return $price > 0 ? ($masterShelfSum * $price) : 0;
            })
            ->addColumn('OKC_each_qty', function ($results) use ($request,$dbData) {
                $warehouse =  WareHouse::where('warehouses','OKC')->first();
                $masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                $price= $dbData->pack_form_count * $dbData->unit_in_pack;
                return $price >0 ? ($masterShelfSum * $price) : 0;
            })->rawColumns(['action'])->make(true);
        }else{
            $results = DB::table('master_product')->where('is_approve', 1)->where('ETIN',$request->ETIN)->get();
            $dbData = DB::table('master_product')->where('is_approve', 1)->where('ETIN',$results->first()->parent_ETIN)->first();
            if($dbData){
                    return Datatables::of($results)
                    ->addColumn('WI_qty', function ($results)  use ($request,$dbData){
                    $warehouse =  WareHouse::where('warehouses','WI')->first();
                    $masterShelfSum = $this->masterShelfSum($warehouse->id,$dbData->ETIN);
                    $price= $dbData->pack_form_count * $dbData->unit_in_pack;
                    $childPrice= $results->pack_form_count * $results->unit_in_pack;
                    return $price >0 ? ($masterShelfSum * $price) / ( $childPrice): 0; 
                    //$masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                    //return $masterShelfSum;
                })
                    ->addColumn('PA_qty', function ($results) use ($request,$dbData) {
                    $warehouse =  WareHouse::where('warehouses','PA')->first();
                    $masterShelfSum = $this->masterShelfSum($warehouse->id,$dbData->ETIN);
                    $price= $dbData->pack_form_count * $dbData->unit_in_pack;
                    $childPrice= $results->pack_form_count * $results->unit_in_pack;
                    return $price >0 ? ($masterShelfSum * $price) / ( $childPrice): 0;
                    //$masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                    //return $masterShelfSum;
                })
                    ->addColumn('NV_qty', function ($results)  use ($request,$dbData){
                    $warehouse =  WareHouse::where('warehouses','NV')->first();
                    $masterShelfSum = $this->masterShelfSum($warehouse->id,$dbData->ETIN);
                    $price= $dbData->pack_form_count * $dbData->unit_in_pack;
                    $childPrice= $results->pack_form_count * $results->unit_in_pack;
                    return $price >0 ? ($masterShelfSum * $price) / ( $childPrice): 0;
                    //$masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                    //return $masterShelfSum;
                })
                    ->addColumn('OKC_qty', function ($results) use ($request,$dbData) {
                    $warehouse =  WareHouse::where('warehouses','OKC')->first();
                    $masterShelfSum = $this->masterShelfSum($warehouse->id,$dbData->ETIN);
                    $price= $dbData->pack_form_count * $dbData->unit_in_pack;
                    $childPrice= $results->pack_form_count * $results->unit_in_pack;
                    return $price >0 ? ($masterShelfSum * $price) / ( $childPrice): 0;
                    //$masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                    //return $masterShelfSum;
                })
                    ->addColumn('wi_each_qty', function ($results) use ($request,$dbData) {
                    $warehouse =  WareHouse::where('warehouses','WI')->first();
                    $masterShelfSum = $this->masterShelfSum($warehouse->id,$dbData->ETIN);
                    $price= $dbData->pack_form_count * $dbData->unit_in_pack;
                    return $price >0 ? ($masterShelfSum * $price) : 0; 
                })
                    ->addColumn('pa_each_qty', function ($results) use ($request,$dbData) {
                    $warehouse =  WareHouse::where('warehouses','PA')->first();
                    $masterShelfSum = $this->masterShelfSum($warehouse->id,$dbData->ETIN);
                    $price= $dbData->pack_form_count * $dbData->unit_in_pack;
                    return $price >0 ? ($masterShelfSum * $price) : 0;
                })
                    ->addColumn('NV_each_qty', function ($results) use ($request,$dbData) {
                    $warehouse =  WareHouse::where('warehouses','NV')->first();
                    $masterShelfSum = $this->masterShelfSum($warehouse->id,$dbData->ETIN);
                    $price= $dbData->pack_form_count * $dbData->unit_in_pack;
                    return $price > 0 ? ($masterShelfSum * $price) : 0;
                })
                    ->addColumn('OKC_each_qty', function ($results) use ($request,$dbData) {
                    $warehouse =  WareHouse::where('warehouses','OKC')->first();
                    $masterShelfSum = $this->masterShelfSum($warehouse->id,$dbData->ETIN);
                    $price= $results->pack_form_count * $results->unit_in_pack;
                    return $price >0 ? ($masterShelfSum * $price) : 0;
                })->rawColumns(['action'])->make(true);
            }else{
                return Datatables::of($results)
                    ->addColumn('WI_qty', function ($results)  use ($request){
                    $warehouse =  WareHouse::where('warehouses','WI')->first();
                    $masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                    return 0;
                })
                    ->addColumn('PA_qty', function ($results) use ($request) {
                    $warehouse =  WareHouse::where('warehouses','PA')->first();
                    $masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                    return 0;
                })
                    ->addColumn('NV_qty', function ($results)  use ($request){
                    $warehouse =  WareHouse::where('warehouses','NV')->first();
                    $masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                    return 0;
                })
                    ->addColumn('OKC_qty', function ($results) use ($request) {
                    $warehouse =  WareHouse::where('warehouses','OKC')->first();
                    $masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                    return 0;
                })
                    ->addColumn('wi_each_qty', function ($results) use ($request,$dbData) {
                    $warehouse =  WareHouse::where('warehouses','WI')->first();
                    $masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                    $price= $results->pack_form_count * $results->unit_in_pack;
                    return $price >0 ? ($masterShelfSum * $price) : 0; 
                })
                    ->addColumn('pa_each_qty', function ($results) use ($request,$dbData) {
                    $warehouse =  WareHouse::where('warehouses','PA')->first();
                    $masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                    $price= $results->pack_form_count * $results->unit_in_pack;
                    return $price >0 ? ($masterShelfSum * $price) : 0;
                })
                    ->addColumn('NV_each_qty', function ($results) use ($request,$dbData) {
                    $warehouse =  WareHouse::where('warehouses','NV')->first();
                    $masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                    $price= $results->pack_form_count * $results->unit_in_pack;
                    return $price > 0 ? ($masterShelfSum * $price) : 0;
                })
                    ->addColumn('OKC_each_qty', function ($results) use ($request,$dbData) {
                    $warehouse =  WareHouse::where('warehouses','OKC')->first();
                    $masterShelfSum = $this->masterShelfSum($warehouse->id,$request->ETIN);
                    $price= $results->pack_form_count * $results->unit_in_pack;
                    return $price >0 ? ($masterShelfSum * $price) : 0;
                })->rawColumns(['action'])->make(true);
            }
            
        }
        //if($request->ETIN == $result->ETIN)
    }
}