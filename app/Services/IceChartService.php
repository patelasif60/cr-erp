<?php
namespace App\Services;

use App\Repositories\IceChartRepository;

/**
 * supplier class to handle operator interactions.
 */

class IceChartService
{

    public function __construct(IceChartRepository $repository)
    {
        $this->repository = $repository;
    }
    public function getIceChartTemplate(){
        return $this->repository->getIceChartTemplate();
    }
    public function getPackagematirial(){
        $result = $this->repository->getPackagematirial();
        if($result)
        return $result->packagingMaterials()->groupBy('product_description')->pluck('id','product_description')->toArray();
        else
        return array();
    }
    public function store($request){
        $this->repository->store($request);
    }
    public function edit($id){
    	return $this->repository->edit($id);	
    }
    public function update($request,$id){
    	return $this->repository->update($request,$id);
    }
}