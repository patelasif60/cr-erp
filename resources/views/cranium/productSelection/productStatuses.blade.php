@extends('layouts.master')

@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Product Status</h1>
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
                    <a href="#" class="btn btn-primary btn-md m-1" data-toggle="modal" data-target="#addModal"><i class="i-Add text-white mr-2"></i> New Status</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="all_suppliers" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($statuses)
                                    @foreach($statuses as $row)
                                        <tr>
                                            <td>{{ $row->product_status }}</td>
                                            <td>
                                                <a href="#" class="btn btn-primary btn-md m-1" data-toggle="modal" data-target="#edit_modal_{{$row->id}}"><i class="i-Add text-white i-Pen-2"></i> Edit Status</a>
                                            </td>
                                        </tr>

                                        <!--Edit Status Modal-->
                                        <div id="edit_modal_{{$row->id}}" class="modal fade" role="dialog">       
                                            <div class="modal-dialog">
                                                <!--Modal Content-->
                                                <div class="modal-content">
                                                    <!-- Modal Header-->
                                                    <div class="modal-header" style="background-color:#fff;">
                                                        <h3>Edit Status</h3>
                                                        <!--Close/Cross Button--> <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
                                                    </div> 
                                                    <form action="edit_status/{{$row->id}}" method="POST" >
                                                        <input type="hidden" name="id" value="{{ $row['id'] }}">
                                                        @csrf
                                                        <div class="modal-body text-center">
                                                            <input type="text" class="form-control" name="product_status" placesholder="Add new status" id="product_status" style="width:100%;" value="{{$row->product_status}}"/> 
                                                        </div> 
                                                            
                                                        <div class="modal-footer"> 
                                                            <button ty;e="submit" class="btn btn-primary" id="add-status">Edit</button> 
                                                            <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
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

    <!--Add Status Modal-->
    <div id="addModal" class="modal fade" role="dialog">       
        <div class="modal-dialog">
            <!--Modal Content-->
            <div class="modal-content">
                <!-- Modal Header-->
                <div class="modal-header" style="background-color:#fff;">
                    <h3>Add New Status</h3>
                    <!--Close/Cross Button--> <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
                </div> 
                <form action="{{url('add_product_status')}}" method="POST" >
                    @csrf
                    <div class="modal-body text-center">
                        <input type="text" class="form-control" name="product_status" placesholder="Add new status" id="product_status" style="width:100%;"/> 
                        {!! $errors->first('product_status', '<label class="error">:message</label>') !!}
                    </div> 
                        
                    <div class="modal-footer"> 
                        <button ty;e="submit" class="btn btn-primary" id="add-status">Add</button> 
                        <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script>
       $(document).ready(function () {
            $('#all_suppliers').DataTable();
       });
    </script>
@endsection