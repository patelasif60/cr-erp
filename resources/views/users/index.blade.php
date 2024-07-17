@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Users</h1>
        <!-- <ul>
            <li><a href="">UI Kits</a></li>
            <li>Datatables</li>
        </ul> -->
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                @if(ReadWriteAccess('AddNewUser'))
                <div class="card-header text-right bg-transparent">
                    <a href="{{ route('users.create') }}" class="btn btn-primary btn-md m-1"><i class="i-Add-User text-white mr-2"></i> New User</a>
                </div>
                @endif
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="all_users" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>User Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($result)
                                    @foreach($result as $row)
                                        <tr>
                                            <td>{{ $row->name }}</td>
                                            <td>{{ $row->username }}</td>
                                            <td>{{ $row->email }}</td>
                                            <td>{{ isset($row->user_role->role) ? $row->user_role->role : 'NA' }}</td>
                                            <td>
                                                @if(ReadWriteAccess('EditUser'))
                                                    <a href="{{ route('users.edit',$row->id) }}" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Edit">
                                                        <i class="nav-icon i-Pen-2 "></i>
                                                    </a>
                                                @endif
                                                @if(ReadWriteAccess('DeleteUser'))
                                                    <form class="d-inline" action="{{ route('users.destroy',$row->id) }}" method="POST">
                                                        {{method_field('DELETE')}}
                                                        {{csrf_field()}}
                                                        <button type="submit" class="btn btn-danger mr-1" value="delete" onClick="return confirm('Are You Sure You Want To Delete This?')"><i class="nav-icon i-Close-Window "></i></button>
                                                    </form>
                                                @endif                                                                                                                                           
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
            $('#all_users').DataTable();
       });
   </script>
@endsection
