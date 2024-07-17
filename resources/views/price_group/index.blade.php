@extends('layouts.master')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection
@section('main-content')
<div class="breadcrumb">
    <h1>Price Groups</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row mb-4">
    <div class="col-md-12 mb-4">
        <div class="card text-left">
        	<div class="card-header text-right bg-transparent">
                @if(ReadWriteAccess('AllSubMenusSelectionfunctions'))
                <a href="{{route('pricegroup.create')}}" class="btn btn-primary btn-md m-1"><i class="i-Add text-white mr-2"></i>Add Pricing Group</a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="components_table" class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Price Group</th>
                                <th>Discription</th>
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
@endsection

@section('page-js')
<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script>
   $(document).ready(function () {
        PriceGroup();
   });

   function PriceGroup(){
        var dt = $('#components_table').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ordering: false,
            
            ajax: '{!! route('pricegroup.pricegrouplist') !!}',
            columns: [
                { data: 'group_name', name: 'group_name' },
                { data: 'description', name: 'description' },
                { data: 'action', name: 'action', searchable: false }
            ]
        });
   }

   function deletePriceGroup(url){
        if(confirm('are you sure')){
            $.ajax({
                url:url,
                method:'GET',
                dataType:'JSON',
                success:function(data){
                    PriceGroup();
                }
            })
        }
   }
</script>
@endsection