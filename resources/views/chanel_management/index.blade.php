@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Channel Management</h1>
        <!-- <ul>
            <li><a href="">UI Kits</a></li>
            <li>Datatables</li>
        </ul> -->
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header text-right bg-transparent">
                    
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="all_clients" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Channel Type</th>
                                    <th>Channel Name</th>
                                    <th>Channel URL</th>
                                    <th>Client/Owner</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($result)
                                    @foreach($result as $row)
                                        <tr>
                                            <td>{{ $row->channel_type }}</td>
                                            <td>{{ $row->channel }}</td>
                                            <td>{{ $row->store_url }}</td>
                                            <td>{{ $row->client_name }}</td>
                                            <td>
                                                <a href="{{ route('chanel_management.view_products',$row->id) }}" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="View Product">
                                                    View Products
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end of col -->
@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script>
       $(document).ready(function () {
            $('#all_clients').DataTable();
       });
   </script>
@endsection
