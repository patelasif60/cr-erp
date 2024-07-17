@extends('layouts.master')

@section('page-css')
    
    <style>
        .error{
            color:red;
        }
    </style>
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Roles</h1>
        <!-- <ul>
            <li><a href="">UI Kits</a></li>
            <li>Datatables</li>
        </ul> -->
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <!-- <div class="card-header text-right bg-transparent">
                 
                </div> -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="CountryList" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @if($roles)
                                @foreach($roles as $row)
                                    <tr>
                                        <td>{{$row->role}}</td>
                                        <td><a class="btn btn-sm btn-flat bg-navy" href="{{ route('roles.RolePermissions',[$row->id,0]) }}">Give Permissions</a></td>
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
    
    
@endsection