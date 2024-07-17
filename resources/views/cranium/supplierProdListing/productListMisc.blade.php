@extends('layouts.master')

@section('main-content')
<style>
.data-table tr td:nth-child(1) {
    display: none;
}
.idclass {
    display: none;
}
.sidebar-custom {
    height: 2000px !important;
}
</style>


<div class="container">
    <h3>Supplier MISCELLANEOUS Product List</h3>
    <table class="table table-bordered data-table" id="datatable">
        <thead>
            <tr>
                <th class="idclass" id="idclass">ID</th>
                <th>ETIN</th>
                <th>Supplier ID</th>
                <th>Etailer Stock Status</th>
                <th>List Status</th>
                <th>Acquisition Cost</th>
                <th>Product Description</th>
                <th>About This Item</th>
                <th>Manufacturer</th>
                <th>Brand</th>
                <th>Unit Size</th>
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
        ajax: "{{ route('getmiscproducts') }}",
        columns: [
            {data: 'id', name: 'ID'},
            {data: 'ETIN', name: 'ETIN'},
            {data: 'supplier_ID', name: 'Supplier ID'},
            {data: 'etailer_stock_status', name: 'Etailer Stock Status'},
            {data: 'list_status', name: 'List Status'},
            {data: 'acquisition_cost', name: 'Acquisition Cost'},
            {data: 'product_description', name: 'Product Description'},
            {data: 'about_this_item', name: 'About This Item'},
            {data: 'manufacturer', name: 'Manufacturer'},
            {data: 'brand', name: 'Brand'},
            {data: 'unit_size', name: 'Unit Size'},
            {data: 'action', name: 'Action', orderable: false},
        ],
		 oLanguage: {
            "sSearch": "Filter results Via UPC:"
        },
    });
  });

	function editmiscproduct(){
		var table = null;
		var table = $('#datatable').DataTable();
		$('#datatable tbody').on( 'click', 'tr', function () {
			var row = table.row( this ).data();
			window.location="miscproductlist/"+row['id'];
		});
	}


	/*function syncwithmaster(){
		var table = null;
		var table = $('#datatable').DataTable();
		$('#datatable tbody').on( 'click', 'tr', function () {
			var row = table.row( this ).data();
			window.location="syncwithmaster/"+row['id'];
		});
	}*/

    function syncMiscWithMasterProduct(){
        var table = null;
        var table = $('#datatable').DataTable();
        $('#datatable tbody').on( 'click', 'tr', function () {
            var row = table.row( this ).data();
            window.location="syncMiscWithMasterProduct/"+row['id'];
        });
    }
		function resyncMiscWithMasterProduct(){
		if(confirm ('Item Already Sync with Master Table. Want to Re-Sync??')){
			syncMiscWithMasterProduct();
		} else
		{
			return false;
		}
	}
</script>

@endsection
