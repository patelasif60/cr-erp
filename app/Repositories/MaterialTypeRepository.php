<?php

namespace App\Repositories;
use App\MaterialType;

/**
 * Repository class for model.
 */
class MaterialTypeRepository extends BaseRepository
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
    	$count = MaterialType::where('material_type',$data['material_type'])->count();
        if($count > 0)
        {
            return false;
        }
        return MaterialType::create($data);
    }
    /**
     * update supplier
     *
     * @param $data
     *
     * @return mixed
     */
    public function update($data)
    {
    	$count = MaterialType::where('material_type',$data['material_type'])->where('id','!=',$data['material_type_id'])->count();
        if($count > 0)
        {
            return false;
        }
        $materialType = MaterialType::find($data['material_type_id']);
    	$materialType->fill($data);
    	$materialType->save();
    	return $materialType;
	}
    public function destroy($id){
       MaterialType::destroy($id);   
    }
     public function getAll()
    {
        return MaterialType::orderBy('material_type','ASC')->get();
    }
}