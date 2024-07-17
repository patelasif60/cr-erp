@extends('layouts.master')

@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Markout Products</h1>        
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="markout_table" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ETIN</th>
                                    <th>Product Listing Name</th>
                                    <th>Qty</th>
                                    <th>Reason</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                        <a href="{{ route('markout_export') }}" class="btn btn-primary">Export</a>
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
            var table_html = '';
            var table_html_td = '';
            var i = 1;
            var dt = $('#markout_table').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                
                ajax: '{!! route('datatable.markout_datatables') !!}',
                columns: [
                    { data: 'ETIN', name: 'ETIN' },
                    { data: 'product.product_listing_name', name: 'product.product_listing_name',"defaultContent": "" },
                    { data: 'qty', name: 'qty' },
                    { data: 'reason', name: 'reason' },
                    { data: 'address', name: 'address' }
                ]
            });
       });
    </script>
@endsection