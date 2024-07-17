<?php
namespace App\Http\Controllers;
use App\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function getrestockproductsetting(){
        $settings = Settings::first();
        return view('cranium.settings.index', compact('settings'));
    }
    public function restockproductsettingstore(Request $request){
        $input = $request->all();
        $result = Settings::first()->fill($input);
        $result->save();
        if($result){
            $data_info = [
                'msg' => 'Success',
                'error' => 0
            ];
        }else{
            $data_info = [
                'msg' => 'Something wend wrong',
                'error' => 1
            ];
        }
        return response()->json($data_info);
    }
}