@extends('layouts.master')

@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
    <style>
        .error{
            color:red;
        }
    </style>
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Product Tags</h1>
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
                    @if(ReadWriteAccess('AllSubMenusSelectionfunctions'))
                    <a href="#" class="btn btn-primary btn-md m-1" onClick="GetModel('{{route('product_tags.create')}}')"><i class="i-Add text-white mr-2"></i>Add Product Tags</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="CountryList" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Product Tags</th>
                                    <th>Flag</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="MyModal" data-backdrop="static">
    </div>
    <!-- end of col -->
@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script>
       $(document).ready(function () {
            // $('#all_suppliers').DataTable();

            var table_html = '';
            var table_html_td = '';
            var i = 1;
            var dt = $('#CountryList').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                
                ajax: '{!! route('datatable.ProductTagsList') !!}',
                columns: [
                    { data: 'tag', name: 'tag' },
                    { data: 'status', name: 'status' },
                    { data: 'command', name: 'command', searchable: false }
                ]
            });
       });
       
        function GetModel(url){
            $.ajax({
                url:url,
                method:'GET',
                success:function(res){
                    $("#MyModal").html(res);
                    $("#MyModal").modal();
                }
            });
        }

    </script>
@endsection