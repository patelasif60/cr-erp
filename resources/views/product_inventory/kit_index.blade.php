@extends('layouts.master')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection
@section('main-content')
<div class="breadcrumb">
    <h1>Product Kit Inventory</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row mb-4">
    <div class="col-md-12 mb-4">
        <div class="card text-left">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="warehouse" class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="ChildModal" tabindex="-1" role="dialog" aria-labelledby="MyModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
             <div class="modal-header" style="background-color:#fff;">
                <h3 id="hmodelHeader"></h3>
                <!--Close/Cross Button-->
                 <button type="button" class="close reset-text" data-dismiss="modal">&times;</button>
            </div> 
            <div class="card-body">
                <div class="table-responsive">
                    <table id="inventory_table" class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                               <th>ID</th>
                                <th>ETIN</th>
                                <th>Product Listing Name</th>
                                <th>Kit Inventory</th>
                                <th>Kit component</th>
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
@endsection

@section('page-js')
<script src="{{ asset('assets/js/validation/jquery.validate.js') }}"></script>
<script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>
<script src="{{asset('assets/js/sweetalert.script.js')}}"></script>
<script src="{{ asset('assets/js/validation/additional-methods.min.js') }}"></script>
<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script>
   $(document).ready(function () {
        var table_html = '';
        var table_html_td = '';
        var i = 1;
        var dt = $('#warehouse').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ordering: false,
            
            ajax: '{!! route('productinventory.warehouse') !!}',
            columns: [
                { data: 'warehouses', name: 'warehouses' },
                { data: 'action', name: 'action', searchable: false }
            ]
        });
   });
   function openChildModal(warehouse_id,warehouse){
        var table_html = '';
        var table_html_td = '';
        var i = 1;
        var dt = $('#inventory_table').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ordering: false,
            ajax:{
                    url: '{{ route('productinventory.productkitinventorylist') }}',
                    method:'GET',
                    data: {
                         warehouse_id:warehouse_id,
                         name:warehouse,
                    }
                },
            columns: [
               { data: 'id', name: 'id' },
                { data: 'ETIN', name: 'ETIN' },
                { data: 'product_listing_name', name: 'product_listing_name' },
                { data: 'kit_inventory', name: 'kit_inventory' },
                { data: 'parent_ETIN', name: 'parent_ETIN' }
            ]
        });
        $('#ChildModal').modal('show');
   }
</script>
@endsection