@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Carrier Management & Configuration</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>
    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                @if($table == 'ups_das_zip')
                    <div class="card-header">
                        <h3 class="w-100 float-left card-title m-0">
                            UPS DAS Zip Codes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="ups_das_zip" class="display table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th width="50%">DAS ZIP</th>
                                        <th width="50%">DAS EXT ZIP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($data)
                                        @foreach($data as $row)
                                            <tr>
                                                <td>{{ $row->das_zip }}</td>
                                                <td>{{ $row->das_ext_zip }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                @if($table == 'ups_zone_rates_by_ground')
                    <div class="card-header">
                        <h3 class="w-100 float-left card-title m-0">
                            UPS Ground Rates By Zone
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="ups_zone_rates_by_ground" class="display table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Zones/Lbs</th>
                                        <th>zone 2</th>
                                        <th>zone 3</th>
                                        <th>zone 4</th>
                                        <th>zone 5</th>
                                        <th>zone 6</th>
                                        <th>zone 7</th>
                                        <th>zone 8</th>
                                        <th>zone 44</th>
                                        <th>zone 45</th>
                                        <th>zone 46</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($data)
                                        @foreach($data as $key1 =>  $row)
                                            <tr>
                                                <td>{{ $key1+1 }}</td>
                                                <td>{{ $row->zone2 }}</td>
                                                <td>{{ $row->zone3 }}</td>
                                                <td>{{ $row->zone4 }}</td>
                                                <td>{{ $row->zone5 }}</td>
                                                <td>{{ $row->zone6 }}</td>
                                                <td>{{ $row->zone7 }}</td>
                                                <td>{{ $row->zone8 }}</td>
                                                <td>{{ $row->zone44 }}</td>
                                                <td>{{ $row->zone45 }}</td>
                                                <td>{{ $row->zone46 }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                @if($table == 'ups_zone_rates_air')
                    <div class="card-header">
                        <h3 class="w-100 float-left card-title m-0">
                            UPS Air Rates By Zone
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="ups_zone_rates_air" class="display table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Zones/Lbs</th>
                                        <th>zone 2</th>
                                        <th>zone 3</th>
                                        <th>zone 4</th>
                                        <th>zone 5</th>
                                        <th>zone 6</th>
                                        <th>zone 7</th>
                                        <th>zone 8</th>
                                        <th>zone 44</th>
                                        <th>zone 45</th>
                                        <th>zone 46</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($data)
                                        @foreach($data as $key => $row)
                                            <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td>{{ $row->zone2 }}</td>
                                                <td>{{ $row->zone3 }}</td>
                                                <td>{{ $row->zone4 }}</td>
                                                <td>{{ $row->zone5 }}</td>
                                                <td>{{ $row->zone6 }}</td>
                                                <td>{{ $row->zone7 }}</td>
                                                <td>{{ $row->zone8 }}</td>
                                                <td>{{ $row->zone44 }}</td>
                                                <td>{{ $row->zone45 }}</td>
                                                <td>{{ $row->zone46 }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                @if($table == 'fedex_zone_rates_by_ground')
                    <div class="card-header">
                        <h3 class="w-100 float-left card-title m-0">
                            Fedex Ground Rates By Zone
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="fedex_zone_rates_by_ground" class="display table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Zones/Lbs</th>
                                        <th>zone 2</th>
                                        <th>zone 3</th>
                                        <th>zone 4</th>
                                        <th>zone 5</th>
                                        <th>zone 6</th>
                                        <th>zone 7</th>
                                        <th>zone 8</th>
                                        <th>zone 9</th>
                                        <th>zone 14</th>
                                        <th>zone 17</th>
                                        <th>zone 22</th>
                                        <th>zone 23</th>
                                        <th>zone 25</th>
                                        <th>zone 92</th>
                                        <th>zone 96</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($data)
                                        @foreach($data as $key1 =>  $row)
                                            <tr>
                                                <td>{{ $key1+1 }}</td>
                                                <td>{{ $row->zone2 }}</td>
                                                <td>{{ $row->zone3 }}</td>
                                                <td>{{ $row->zone4 }}</td>
                                                <td>{{ $row->zone5 }}</td>
                                                <td>{{ $row->zone6 }}</td>
                                                <td>{{ $row->zone7 }}</td>
                                                <td>{{ $row->zone8 }}</td>
                                                <td>{{ $row->zone9 }}</td>
                                                <td>{{ $row->zone14 }}</td>
                                                <td>{{ $row->zone17 }}</td>
                                                <td>{{ $row->zone22 }}</td>
                                                <td>{{ $row->zone23 }}</td>
                                                <td>{{ $row->zone25 }}</td>
                                                <td>{{ $row->zone92 }}</td>
                                                <td>{{ $row->zone96 }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                @if($table == 'fedex_zone_rates_air')
                    <div class="card-header">
                        <h3 class="w-100 float-left card-title m-0">
                            Fedex Air Rates By Zone
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="fedex_zone_rates_air" class="display table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Zones/Lbs</th>
                                        <th>zone 2</th>
                                        <th>zone 3</th>
                                        <th>zone 4</th>
                                        <th>zone 5</th>
                                        <th>zone 6</th>
                                        <th>zone 7</th>
                                        <th>zone 8</th>
                                        <th>zone 9</th>
                                        <th>zone 13</th>
                                        <th>zone 14</th>
                                        <th>zone 15</th>
                                        <th>zone 16</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($data)
                                        @foreach($data as $key => $row)
                                            <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td>{{ $row->zone2 }}</td>
                                                <td>{{ $row->zone3 }}</td>
                                                <td>{{ $row->zone4 }}</td>
                                                <td>{{ $row->zone5 }}</td>
                                                <td>{{ $row->zone6 }}</td>
                                                <td>{{ $row->zone7 }}</td>
                                                <td>{{ $row->zone8 }}</td>
                                                <td>{{ $row->zone9 }}</td>
                                                <td>{{ $row->zone13 }}</td>
                                                <td>{{ $row->zone14 }}</td>
                                                <td>{{ $row->zone15 }}</td>
                                                <td>{{ $row->zone16 }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- end of col -->
@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script>
       $(document).ready(function () {
            @if($table == 'ups_zone_rates_by_ground')
                $('#ups_zone_rates_by_ground').DataTable();
            @endif
            @if($table == 'ups_zone_rates_air')
                $('#ups_zone_rates_air').DataTable();
            @endif
            @if($table == 'ups_das_zip')
                $('#ups_das_zip').DataTable();
            @endif
            @if($table == 'fedex_zone_rates_air')
                $('#fedex_zone_rates_air').DataTable();
            @endif
            @if($table == 'fedex_zone_rates_by_ground')
                $('#fedex_zone_rates_by_ground').DataTable();
            @endif
       });
   </script>
@endsection
