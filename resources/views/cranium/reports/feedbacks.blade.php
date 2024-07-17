@extends('layouts.master')

@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Feedbacks</h1>
        <!-- <ul>
            <li><a href="">UI Kits</a></li>
            <li>Datatables</li>
        </ul> -->
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="all_suppliers" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Feedback</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($result)
                                    @foreach($result as $row)
                                        <tr>
                                            <td>{{ $row->name }}</td>
                                            <td>{{ $row->feedback }}</td>
                                            <td>{{ $row->message }}</td>
                                            <td>
                                                {{ date('Y-m-d', strtotime($row->created_at)) }}
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
            $('#all_suppliers').DataTable();
       });
    </script>
@endsection