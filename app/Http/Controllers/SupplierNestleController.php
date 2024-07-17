<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DataTables;
use App\SupplierNestle;
use Auth;
class SupplierNestleController extends Controller
{
		public function __construct()
	{
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '1024M');
	}
	public function getnestleproducts(Request $request)
    {
		
        if ($request->ajax()) {
            $data = DB::table('supplier_nestle')->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
						$btn = '';
							if(Auth::user()->role<=2){
							
                           $btn = '<a href="javascript:void(0)" onclick="syncdotproduct()" id="syncdotproduct" class="edit btn btn-primary btn-sm">Sync</a>';
                           //$btn = '<a href="javascript:void(0)" onclick="syncwithmaster()" id="syncwithmaster" class="edit btn btn-primary btn-sm">Sync</a>';
							}
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
	public function getnestlemap(Request $request){
		if ($request->ajax()) {
            $data = DB::table('csv_header')->where('map_type','supplier_nestle')->get();
			$dataCount = $data->count();
			if($dataCount > 0){
				$json = json_decode(($data[0]->map_data),TRUE);
				$jsonArr = [];
				foreach ($json as $k => $v) {
					if($v != null){
						$jsonArr[$k] = $v;
					}
				}
			} else {
				$jsonArr['Mapping is not available for - '] = '<b>Supplier Nestle.</b>';
			}
		return $jsonArr;
		}
	}
}
