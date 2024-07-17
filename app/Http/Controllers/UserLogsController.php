<?php

namespace App\Http\Controllers;

use App\userLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;



class UserLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\userLogs  $userLogs
     * @return \Illuminate\Http\Response
     */
    public function show(userLogs $userLogs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\userLogs  $userLogs
     * @return \Illuminate\Http\Response
     */
    public function edit(userLogs $userLogs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\userLogs  $userLogs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, userLogs $userLogs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\userLogs  $userLogs
     * @return \Illuminate\Http\Response
     */
    public function destroy(userLogs $userLogs)
    {
        //
    }


    public function downloadLog()
    {
        $logFilePath = storage_path('logs/incoming_order/incoming_order-2023-05-15.log');
        // dd($logFilePath);
        // if (Storage::exists($logFilePath)) {
            $headers = [
                'Content-Type' => 'text/plain',
                'Content-Disposition' => 'attachment; filename="laravel.log"',
            ];
            return response()->download($logFilePath, 'laravel.log', $headers);
        // } else {
        //     abort(404);
        // }
    }

    public function ClearLog()
    {
        $logFilePath = storage_path('logs/incoming_order/incoming_order-2023-05-15.log');
        Storage::put($logFilePath, '');
    }

}
