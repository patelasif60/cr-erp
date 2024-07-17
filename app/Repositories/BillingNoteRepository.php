<?php

namespace App\Repositories;
use App\BillingNote;

/**
 * Repository class for model.
 */
class BillingNoteRepository extends BaseRepository
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
    	$count = BillingNote::where('option',$data['option'])->count();
        if($count > 0)
        {
            return false;
        }
        return BillingNote::create($data);
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
    	$count = BillingNote::where('option',$data['option'])->where('id','!=',$data['id'])->count();
        if($count > 0)
        {
            return false;
        }
        $billingNote = BillingNote::find($data['id']);
    	$billingNote->fill($data);
    	$billingNote->save();
    	return $billingNote;
	}
    public function destroy($id){
       BillingNote::destroy($id);   
    }
     public function getAll()
    {
        return BillingNote::orderBy('option','ASC')->get();
    }
}