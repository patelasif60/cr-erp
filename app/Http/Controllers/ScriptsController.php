<?php

namespace App\Http\Controllers;

use App\UpsDASZip;
use App\UpsZipZoneByWH;
use App\UpsZoneRatesAir;
use App\UpsZipZoneByGround;
use Illuminate\Http\Request;
use App\UpsZoneRatesByGround;
use App\UpsZoneRatesSurePost;
use App\Imports\UpsDASZipImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UpsZipZoneByWHImport;
use App\Imports\UPSZoneRatesAirImport;
use App\Imports\UpsZoneRatesByGroundImport;
use App\Imports\UpsZoneRatesSurePostImport;

class ScriptsController extends Controller
{
    public function index(){
        return view('cranium.upload_upc_zone_data');
    }

    public function upload_ups_zone_rates_to_table(Request $request){
        $request->validate([
            'csv_file' => 'mimes:xls,xlsx,csv'
        ]);

        if($request->table_name == 'ups_zone_rates_air'){
            if($request->file('csv_file')){
                UpsZoneRatesAir::truncate();
                Excel::import(new UPSZoneRatesAirImport, $request->file('csv_file'));
                return response()->json(['msg' => 'Data Inserted successfully!','error' => false]);
            }
        }

        if($request->table_name == 'ups_zipzone_by_wh'){
            if($request->file('csv_file')){
                UpsZipZoneByWH::truncate();
                Excel::import(new UpsZipZoneByWHImport, $request->file('csv_file'));
                return response()->json(['msg' => 'Data Inserted successfully!','error' => false]);
            }
        }

        if($request->table_name == 'ups_zipzone_by_ground'){
            if($request->file('csv_file')){
                UpsZoneRatesByGround::truncate();
                Excel::import(new UpsZoneRatesByGroundImport, $request->file('csv_file'));
                return response()->json(['msg' => 'Data Inserted successfully!','error' => false]);
            }
        }

        if($request->table_name == 'ups_das_zip'){
            if($request->file('csv_file')){
                UpsDASZip::truncate();
                Excel::import(new UpsDASZipImport, $request->file('csv_file'));
                return response()->json(['msg' => 'Data Inserted successfully!','error' => false]);
            }
        }

	}

    public function get_rates_listing($table){
        $data = DB::table($table)->orderBy('id','asc')->get();
        return view('carriers.get_rates_listing',compact('table','data'));
    }
}
