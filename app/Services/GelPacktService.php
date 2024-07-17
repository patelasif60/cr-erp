<?php
namespace App\Services;

use App\Repositories\GelPacktRepository;

/**
 * supplier class to handle operator interactions.
 */

class GelPacktService
{

    public function __construct(GelPacktRepository $repository)
    {
        $this->repository = $repository;
    }
    public function getGelPackTemplate(){
        return $this->repository->getGelPackTemplate();
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