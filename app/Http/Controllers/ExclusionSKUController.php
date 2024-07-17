<?php

namespace App\Http\Controllers;

use App\Client;
use App\MasterProduct;
use App\SkuOrderExclusion;
use Illuminate\Http\Request;
use App\ClientChannelConfiguration;

class ExclusionSKUController extends Controller
{
    public function index() {
        $excluded_skus = SkuOrderExclusion::all();
        return view('sku_exclusion.index',compact('excluded_skus'));
    }

    public function create() {
        $clients = Client::all();
        $channels = ClientChannelConfiguration::all();
        return view('sku_exclusion.add_sku', compact('clients', 'channels'));        
    }

    public function store(Request $request) {

        $client = $request->client;
        $channels = $request->chanel_ids_bl;
        $bl_skus = $request->bl_sku;
        
        if (!isset($channels) || $channels === '') {
            $channels = ClientChannelConfiguration::where('client_id', $client)->pluck('id')->toArray();
        } else {
            $channels = explode(',', $channels);
        }

        if (count($channels) <= 0) {
            return response()->json([
                'error' => 1,
                'msg' => 'No channels found for the selected client.'
            ]);
        }

        $status = $this->checkSkus($bl_skus, $client);
        if (str_starts_with($status[0], 'Error')) {
            return response()->json([
                'error' => 1,
                'msg' => $status[0]
            ]);
        }

        $all_prods = $status[1];
        $count = 0;
        foreach($all_prods as $prod) {
            foreach($channels as $channel) {
                $dbEntry = SkuOrderExclusion::where('client_id', $client)
                    ->where('channel_id', $channel)
                    ->where('sku', $prod[0])->count();
                if ($dbEntry > 0) {
                    continue;
                }
                SkuOrderExclusion::create([
                    'client_id' => $client, 
                    'channel_id' => $channel,
                    'sku' => $prod[0],
                    'master_product_id' => $prod[1]
                ]);
                $count++;
            }
        }
        return response()->json([
            'error' => 0,
            'msg' => 
                $count <= 0 
                    ? "All Exclusions SKUs present for selected Client/Channel. Noting added." 
                    : "Exclusion SKUs added Successfully. Count: " . $count
        ]);
    }

    public function show($client_id, $mp_id) {
        $client = Client::where('id', $client_id)->first()->company_name;
        $channels = ClientChannelConfiguration::where('client_id', $client_id)->get();
        $assigned_channels = SkuOrderExclusion::where('client_id', $client_id)
            ->where('master_product_id', $mp_id)->get();
        $channel_ids = [];
        $sku = '';
        if (isset($assigned_channels) && count($assigned_channels) > 0) {
            foreach($assigned_channels as $channel) {
                array_push($channel_ids, $channel->channel_id);
                if ($sku === '') $sku = $channel->sku;
            }
        }
        $channel_ids = implode(',', $channel_ids);
        return view('sku_exclusion.edit_sku', compact('client', 'channel_ids', 'channels', 'sku', 'client_id', 'mp_id'));        
    }

    public function update(Request $request) {
        $client_id = $request->client_id;
        $channels = $request->chanel_ids_bl;
        $sku = $request->bl_sku_name;
        
        if (!isset($channels) || $channels === '') {
            $channels = ClientChannelConfiguration::where('client_id', $client_id)->pluck('id')->toArray();
        } else {
            $channels = explode(',', $channels);
        }  
        
        if (count($channels) <= 0) {
            return response()->json([
                'error' => 1,
                'msg' => 'No channels found for the selected client.'
            ]);
        }
        
        SkuOrderExclusion::where('client_id', $client_id)->where('sku', $sku)->delete();

        $bl_skus = $request->bl_sku;
        $status = $this->checkSkus($bl_skus, $client_id);
        if (str_starts_with($status[0], 'Error')) {
            return response()->json([
                'error' => 1,
                'msg' => $status[0]
            ]);
        }

        $all_prods = $status[1];
        $count = 0;
        foreach($all_prods as $prod) {
            foreach($channels as $channel) {
                $dbEntry = SkuOrderExclusion::where('client_id', $client_id)
                    ->where('channel_id', $channel)
                    ->where('sku', $prod[0])->count();
                if ($dbEntry > 0) {
                    continue;
                }
                SkuOrderExclusion::create([
                    'client_id' => $client_id, 
                    'channel_id' => $channel,
                    'sku' => $prod[0],
                    'master_product_id' => $prod[1]
                ]);
                $count++;
            }
        }

        return response()->json([
            'error' => 0,
            'msg' => 
                $count <= 0 
                    ? "All Exclusions SKUs present for selected Client/Channel. Noting Modified." 
                    : "Exclusion SKUs modified Successfully. Count: " . $count
        ]);
    }

    public function deleteExclusion($id) {
        SkuOrderExclusion::where('id', $id)->delete();
        return response()->json([
            'error' => 0,
            'msg' => "Exclusion SKUs deleted Successfully."
        ]);
    }

    private function checkSkus($list, $client_id) {
        $skus = explode(',', $list);
        $all_prods = [];
        foreach ($skus as $sku) {
            $sku = trim($sku);
            $mps = MasterProduct::where(function ($query) use($client_id) {
                    $query->whereRaw('FIND_IN_SET(' . $client_id. ',lobs)');
                })->where('ETIN', $sku)->orWhereRaw('FIND_IN_SET(\'' . $sku. '\', alternate_ETINs)')->first();
            if (!isset($mps)) {
                array_push($all_prods, [$sku, -1]);
            } else {
                array_push($all_prods, [$sku, $mps->id]);
            }
        }
        return ['success', $all_prods];
    }

    public function enableDne(Request $request) {
        $client_id = $request->client_id;
        $channel_id = $request->channel_id;

        if (!isset($channel_id) || $channel_id == '') {
            ClientChannelConfiguration::where('client_id', $client_id)->update(['is_dne' => 1]);
        } else {
            ClientChannelConfiguration::where('id', $channel_id)->update(['is_dne' => 1]);
        }

        return response()->json([
            'error' => 0,
            'msg' => "Channel DNE Enabled Successfully."
        ]);
    }

    public function disableDne(Request $request) {
        $client_id = $request->client_id;
        $channel_id = $request->channel_id;

        if (!isset($channel_id) || $channel_id == '') {
            ClientChannelConfiguration::where('client_id', $client_id)->update(['is_dne' => 0]);
        } else {
            ClientChannelConfiguration::where('id', $channel_id)->update(['is_dne' => 0]);
        }

        return response()->json([
            'error' => 0,
            'msg' => "Channel DNE Disabled Successfully."
        ]);
    }
}