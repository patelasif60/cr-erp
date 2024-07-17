@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Clients</h1>
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
                    @if(ReadWriteAccess('AddNewClient'))
                    <a href="{{ route('clients.create') }}" class="btn btn-primary btn-md m-1"><i class="i-Add-User text-white mr-2"></i> New Client</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="all_clients" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Client Company Name</th>
                                    <th>Business Relationship</th>
                                    <th>Account Manager</th>
                                    <th>Sales manager</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($result)
                                    @foreach($result as $row)
                                        <tr>
                                            <td>{{ $row->company_name }}</td>
                                            <td>{{ $row->business_relationship }}</td>
                                            <td>{{ $row->account_manager }}</td>
                                            <td>{{ $row->sales_manager }}</td>
                                            @if ($row->is_enable == 1)
                                                <td>Active</td>
                                            @elseif ($row->is_enable == 2)                                
                                                <td>On Hold</td>
                                            @elseif ($row->is_enable == 3)                                
                                                <td>Discontinued</td>
                                            @endif                                            
                                            <td>
                                                @if(ReadWriteAccess('EditClient'))
                                                <a href="{{ route('clients.edit',$row->id) }}" class="btn btn-warning"  data-toggle="tooltip" data-placement="top" title="Edit">
                                                    <i class="nav-icon i-Pen-2 "></i>
                                                </a>
                                                @endif
                                                @if(ReadWriteAccess('DeleteClient'))
                                                <form class="d-inline" action="{{ route('clients.destroy',$row->id) }}" method="POST">
                                                {{method_field('DELETE')}}
                                                {{csrf_field()}}
                                                <button type="submit" class="btn btn-danger mr-1" value="delete" onClick="return confirm('Are You Sure You Want To Delete This?')">
                                                    <i class="nav-icon i-Close-Window "></i>
                                                </button>
                                                @endif
                                                {{-- @if ($row->is_enable == 1)
                                                    <a href="{{ route('clients.update_status',[$row->id, 0]) }}" class="btn btn-danger"  data-toggle="tooltip" data-placement="top" title="De-Activate">
                                                        <i class="nav-icon i-Power-2"></i>
                                                    </a>
                                                @else
                                                <a href="{{ route('clients.update_status',[$row->id, 1]) }}" class="btn btn-success"  data-toggle="tooltip" data-placement="top" title="Activate">
                                                    <i class="nav-icon i-Repeat"></i>
                                                </a>
                                                @endif                                                 --}}
                                            </form>

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
