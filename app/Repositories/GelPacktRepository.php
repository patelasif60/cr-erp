<?php

namespace App\Repositories;
use App\GelPackTemplate;
use App\MaterialType;
use App\GelSubPack;

/**
 * Repository class for model.
 */
class GelPacktRepository extends BaseRepository
{
    public function getGelPackTemplate(){
        return GelPackTemplate::get();
    }
    public function getPackagematirial(){
        return MaterialType::where('Material_type','Shipping Box')->first();
    }
    public function store($request){
         $gelPackTemplate = GelPackTemplate::create([
             'template_name'   => $request->template_name,
             'template__description' =>$request->template__description,
        ]);
        foreach($request->chartsval as $chartsvalKey=>$chartsvalVal){
            $subChart = GelSubPack::create([
                'gel_pack_template_id'   => $gelPackTemplate->id,
                'packaging_materials_id'   => $chartsvalKey,
                '1day_block' =>$chartsvalVal[0]['block'],
                '1day_pellet' =>$chartsvalVal[0]['pellet'],
                '1day_1lb_pack' =>$chartsvalVal[0]['1lbpack'],
                '1day_2lb_pack' =>$chartsvalVal[0]['2lbpack'],
                '2day_block' =>$chartsvalVal[1]['block'],
                '2day_pellet' =>$chartsvalVal[1]['pellet'],
                '2day_1lb_pack' =>$chartsvalVal[1]['1lbpack'],
                '2day_2lb_pack' =>$chartsvalVal[1]['2lbpack'],
                '3day_block' =>$chartsvalVal[2]['block'],
                '3day_pellet' =>$chartsvalVal[2]['pellet'],
                '3day_1lb_pack' =>$chartsvalVal[2]['1lbpack'],
                '3day_2lb_pack' =>$chartsvalVal[2]['2lbpack'],
                '4day_block' =>$chartsvalVal[3]['block'],
                '4day_pellet' =>$chartsvalVal[3]['pellet'],
                '4day_1lb_pack' =>$chartsvalVal[3]['1lbpack'],
                '4day_2lb_pack' =>$chartsvalVal[3]['2lbpack'],
            ]);
        }
    }
    public function edit($id)
    {
        return GelPackTemplate::find($id);
    }
    public function update($request,$id){
        $gelPackTemplate = GelPackTemplate::find($id);
        $gelPackTemplate->fill([
            'template_name'   => $request->template_name,
            'template__description' =>$request->template__description,
        ]);
        $gelPackTemplate->save();
        GelSubPack::where('gel_pack_template_id',$id)->delete();
        foreach($request->chartsval as $chartsvalKey=>$chartsvalVal){
            $subChart = GelSubPack::create([
                'gel_pack_template_id'   => $id,
                'packaging_materials_id'   => $chartsvalKey,
                '1day_block' =>$chartsvalVal[0]['block'],
                '1day_pellet' =>$chartsvalVal[0]['pellet'],
                '1day_1lb_pack' =>$chartsvalVal[0]['1lbpack'],
                '1day_2lb_pack' =>$chartsvalVal[0]['2lbpack'],
                '2day_block' =>$chartsvalVal[1]['block'],
                '2day_pellet' =>$chartsvalVal[1]['pellet'],
                '2day_1lb_pack' =>$chartsvalVal[1]['1lbpack'],
                '2day_2lb_pack' =>$chartsvalVal[1]['2lbpack'],
                '3day_block' =>$chartsvalVal[2]['block'],
                '3day_pellet' =>$chartsvalVal[2]['pellet'],
                '3day_1lb_pack' =>$chartsvalVal[2]['1lbpack'],
                '3day_2lb_pack' =>$chartsvalVal[2]['2lbpack'],
                '4day_block' =>$chartsvalVal[3]['block'],
                '4day_pellet' =>$chartsvalVal[3]['pellet'],
                '4day_1lb_pack' =>$chartsvalVal[3]['1lbpack'],
                '4day_2lb_pack' =>$chartsvalVal[3]['2lbpack'],
            ]);
        }     
    }
}