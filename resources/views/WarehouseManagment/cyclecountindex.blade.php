@extends('layouts.master')

@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
    <style>
        .error{
            color:red;
        }
    </style>
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Cycle Count Summary</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header text-right bg-transparent">
                    @if(ReadWriteAccess('AllSubMenusSelectionfunctions'))
                    <a href="{{route('warehousemanagment.cyclecount.create')}}" class="btn btn-primary btn-md m-1"><i class="i-Add text-white mr-2"></i>Add cycle scheduled</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="cycle_count_datatable" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Warehouse Assigned</th>
                                    <th>Requested Date</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Count By</th>
                                    <th>Status</th>
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
    <!-- end of col -->
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
            var dt = $('#cycle_count_datatable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                
                ajax: '{!! route('datatable.cyclecountlist') !!}',
                columns: [
                    { data: 'warehouse', name: 'warehouse' },
                    { data: 'scheduled_date', name: 'scheduled_date' },
                    { data: 'start_date_time', name: 'complate_date_time' },
                    { data: 'complate_date_time', name: 'complate_date_time' },
                    { data: 'c_type', name: 'c_type' },
                    { data: 'status', name: 'status' },
                    {data:'action', name: 'action'}
                ]
            });
       });

    function deleteSummary(ccSumId) {
        swal({
            title: 'Are you sure?',
            text: "This information will be permanently deleted!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#50C878',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Proceed'
        }).then(function(result) {
            if(result) {
                $.ajax({
                    type: "POST",
                    url: '/cyclecount/delete/' + ccSumId,
                    success: function( response ) {
                        location.reload()
                    }
                })
            }
        });
    }

    </script>
@endsection