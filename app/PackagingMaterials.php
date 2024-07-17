<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackagingMaterials extends Model
{
     protected $table = 'packaging_materials';

     /**
     * Enable timestamps.
     *
     * @var array
     */
    public $timestamps = true;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function ChildPackaging(){
        return $this->hasMany(\App\PackagingcomponentsSetting::class, 'parent_packaging_material_id');
    }
    
    public function supplier(){
        return $this->belongsTo('App\Supplier', 'supplier_id', 'id');
    }
    public function client(){
        return $this->belongsTo('App\Client', 'clients_assigned', 'id');
    }

    public function GetTheRequiredPackagingComponent($Items,$OD){
        $client_id = $OD->orderSummary->client_id;
        $transitDay = $OD->transit_days;
        $warehouse = $OD->warehouse_info->id;


        $PM_ARRAY = [];
        $PM = NULL;
        if($Items){
            foreach($Items as $rowItems){
                $unit_description = $rowItems['unit_description'];
                $PM = PackagingMaterials::leftjoin('material_warehouse_td_count',function($join){
                    $join->on('material_warehouse_td_count.material_id','=','packaging_materials.id');
                })->where('packaging_materials.clients_assigned',$client_id)->where('packaging_materials.unit_desc',$unit_description)->where('material_warehouse_td_count.wh_id', $warehouse)->where('material_warehouse_td_count.transit_days', $transitDay)->select('packaging_materials.*','material_warehouse_td_count.count')->orderBy('material_warehouse_td_count.count','ASC')->where('packaging_materials.material_type_id','!=',1)->where('packaging_materials.status','Active')->get()->toArray();
                DeveloperLog([
                    'reference' => 'Getting Packaging Materieals',
                    'ref_request' => json_encode([
                        'client_id' => $client_id,
                        'unit_description' => $unit_description,
                        'transitDay' => $transitDay,
                        'warehouse' => $warehouse
                    ]),
                    'ref_response' => json_encode($PM)
                ]);

                if($PM){
                    $found = 0;
                    foreach($PM as $rowPM){
                        if($rowPM['count'] >= $rowItems['total_count']){
                            // DeveloperLog([
                            //     'reference' => 'Before '.$rowItems['total_count'],
                            //     'ref_request' => json_encode($rowPM),
                            //     'ref_response' => json_encode($PM_ARRAY)
                            // ]);
                            $PM_ARRAY[] = $rowPM;
                            // DeveloperLog([
                            //     'reference' => 'After '.$rowItems['total_count'],
                            //     'ref_request' => json_encode($rowPM),
                            //     'ref_response' => json_encode($PM_ARRAY)
                            // ]);
                            $found = 1;
                            break;
                        }
                    }

                    if($found == 0){
                        // DeveloperLog([
                        //     'reference' => 'No Material checking other way around '.$rowItems['total_count'],
                        //     'ref_request' => json_encode($PM_ARRAY),
                        //     'ref_response' => json_encode([])
                        // ]);
                        $count = $rowPM['count'];
                        $total_count = 0;
                        usort($PM, static function($a, $b){ return $b['count'] <=> $a['count'];});
                        // usort($PM, fn($a, $b) => $b['count'] <=> $a['count']);
                        foreach($PM as $DrowPM){
                            $total_count+= $DrowPM['count'];
                            // DeveloperLog([
                            //     'reference' => 'Before '.$rowItems['total_count'],
                            //     'ref_request' => json_encode($DrowPM),
                            //     'ref_response' => json_encode($PM_ARRAY)
                            // ]);
                            $PM_ARRAY[] = $DrowPM;
                            // DeveloperLog([
                            //     'reference' => 'After '.$rowItems['total_count'],
                            //     'ref_request' => json_encode($DrowPM),
                            //     'ref_response' => json_encode($PM_ARRAY)
                            // ]);
                            if($total_count>= $rowItems['total_count']){
                                break;
                            }
                        }
                    }
                    
                }
                
            }
        }

        DeveloperLog([
            'reference' => 'Final Output for Getting Materieals',
            'ref_request' => json_encode($PM),
            'ref_response' => json_encode($PM_ARRAY)
        ]);

        return $PM_ARRAY;
        
        
        // return $Object;
    }

    public function OLDGetTheRequiredPackagingComponent($masterProduct,$OD,$Qty){
        
        $unit_description = $masterProduct->unit_description;
        $item_form_description = strtolower($masterProduct->item_form_description);
        $unit_in_pack = $masterProduct->unit_in_pack;
        if ($item_form_description == 'each') {
            $total_count  = $Qty;
        } else if ($item_form_description == 'pack' || $item_form_description == 'case' || $item_form_description == 'case as each') {
            $total_count = $Qty * $unit_in_pack;
        }
        
        $client_id = $OD->orderSummary->client_id;
        
        $transitDay = $OD->transit_days;
        $warehouse = $OD->warehouse_info->id;

        $PM_ARRAY = [];
        $PM = PackagingMaterials::leftjoin('material_warehouse_td_count',function($join){
            $join->on('material_warehouse_td_count.material_id','=','packaging_materials.id');
        })->where('packaging_materials.clients_assigned',$client_id)->where('packaging_materials.unit_desc',$unit_description)->where('packaging_materials.item_form_desc',$item_form_description)->where('material_warehouse_td_count.wh_id', $warehouse)->where('material_warehouse_td_count.transit_days', $transitDay)->select('packaging_materials.*','material_warehouse_td_count.count')->get();
        if($PM){
            foreach($PM as $RPM){
                $count = $RPM->count;
                if($count >= $total_count){
                    $PM_ARRAY[] = $RPM;
                }
            }
        }
        // dump($PM->toArray());
        // dump($PM_ARRAY);
       
        $Object = array_reduce($PM_ARRAY,static function ($A,$B){
            if(isset($A->count) && $A->count < $B->count){
                return $A;
            }else{
                return $B;
            }
            
        });
        
        return $Object;
    }

    public function user_defined_reduce($A,$B){
        return $A->count < $B->count ? $A : $B;
    }
    
}
