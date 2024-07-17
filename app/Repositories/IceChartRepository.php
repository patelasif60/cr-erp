<?php

namespace App\Repositories;
use App\IceChartTemplate;
use App\MaterialType;
use App\IceSubChart;

/**
 * Repository class for model.
 */
class IceChartRepository extends BaseRepository
{
    public function getIceChartTemplate(){
        return IceChartTemplate::get();
    }
    public function getPackagematirial(){
        return MaterialType::where('Material_type','Shipping Box')->first();
    }
    public function store($request){
         $iceChartTemplate = IceChartTemplate::create([
             'template_name'   => $request->template_name,
             'template__description' =>$request->template__description,
        ]);
        foreach($request->chartsval as $chartsvalKey=>$chartsvalVal){
            $iceSubChart = IceSubChart::create([
                'ice_chart_template_id'   => $iceChartTemplate->id,
                'packaging_materials_id'   => $chartsvalKey,
                '1day_block' =>$chartsvalVal[0]['block'],
                '1day_pellet' =>$chartsvalVal[0]['pellet'],
                '2day_block' =>$chartsvalVal[1]['block'],
                '2day_pellet' =>$chartsvalVal[1]['pellet'],
                '3day_block' =>$chartsvalVal[2]['block'],
                '3day_pellet' =>$chartsvalVal[2]['pellet'],
                '4day_block' =>$chartsvalVal[3]['block'],
                '4day_pellet' =>$chartsvalVal[3]['pellet'],
            ]);
        }
    }
    public function edit($id)
    {
        return IceChartTemplate::find($id);
    }
    public function update($request,$id){
        $iceChartTemplate = IceChartTemplate::find($id);
        $iceChartTemplate->fill([
            'template_name'   => $request->template_name,
            'template__description' =>$request->template__description,
        ]);
        $iceChartTemplate->save();
        IceSubChart::where('ice_chart_template_id',$id)->delete();
        foreach($request->chartsval as $chartsvalKey=>$chartsvalVal){
            $iceSubChart = IceSubChart::create([
                'ice_chart_template_id'   => $id,
                'packaging_materials_id'   => $chartsvalKey,
                '1day_block' =>$chartsvalVal[0]['block'],
                '1day_pellet' =>$chartsvalVal[0]['pellet'],
                '2day_block' =>$chartsvalVal[1]['block'],
                '2day_pellet' =>$chartsvalVal[1]['pellet'],
                '3day_block' =>$chartsvalVal[2]['block'],
                '3day_pellet' =>$chartsvalVal[2]['pellet'],
                '4day_block' =>$chartsvalVal[3]['block'],
                '4day_pellet' =>$chartsvalVal[3]['pellet'],
            ]);
        }     
    }
}