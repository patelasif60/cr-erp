@extends('layouts.master')

@section('main-content')
<style>
#datatable_filter{
    text-align: right;
}
#datatable_processing{
    width: 165px;
    text-align: center;
    margin-left: 40%;
}
</style>

<div class="container">
    <h3>Supplier DRYER Product List</h3>
    <table class="table table-bordered data-table" id="datatable">
        <thead>
            <tr>
                <th>Brand Name</th>
                <th>Sub Brand</th>
                <th>IM GR</th>
                <th>Pack Description</th>
                <th>Fanc Name</th>
                <th>Std ID</th>
                <th>Flavor Declaration</th>
                <th>Globe</th>
                <th>UPC</th>
                <th>Consumer Code</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

@endsection

@section('bottom-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>


<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript">
  $(function () {

    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('getdryerproducts') }}",
        columns: [
            {data: 'brand_name', name: 'Brand Name'},
            {data: 'sub_brand', name: 'Sub Brand'},
            {data: 'im_gr', name: 'IM GR'},
            {data: 'pack_description', name: 'Pack Description'},
            {data: 'fanc_name', name: 'Fanc Name'},
            {data: 'std_ID', name: 'Std ID'},
            {data: 'flavor_declaration', name: 'Flavor Declaration'},
            {data: 'globe', name: 'Globe'},
            {data: 'UPC', name: 'UPC'},
            {data: 'consumer_code', name: 'Consumer Code'},
            {data: 'action', name: 'Action', orderable: false},
        ],
        columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false
                },
            ],
		 oLanguage: {
            "sSearch": "Filter results Via UPC:"
        },
    });
  });


	function syncDryersWithMasterProduct(id){
        window.location="syncDryersWithMasterProduct/"+id;
	}
    function resyncDryersWithMasterProduct(id){
		if(confirm ('Item Already Sync with Master Table. Want to Re-Sync??')){
			syncDryersWithMasterProduct(id);
		} else
		{
			return false;
		}
    }
</script>

@endsection
