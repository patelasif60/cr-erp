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


<div class="">
    <h3>Supplier KEHE Product List</h3>
    <table class="table table-bordered data-table" id="datatable">
        <thead>
            <tr>
                <th class="idclass" id="idclass">ID</th>
                <th>ETIN</th>
                <th>Cust Number</th>
                <th>Cust Name</th>
                <th>Item Number</th>
                <th>UPC</th>
                <th>Brand</th>
                <th>Description</th>
                <th>Size</th>
                <th>UOM</th>
                <th>Quantity</th>
                <th>Reg WHLSL</th>
                <th>WHLSL Minus Vol</th>
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
        ajax: "{{ route('getkeheproducts') }}",
        columns: [
            {data: 'id', name: 'ID'},
            {data: 'ETIN', name: 'ETIN'},
            {data: 'cust_number', name: 'Cust Number'},
            {data: 'cust_name', name: 'Cust Name'},
            {data: 'item_number', name: 'Item Number'},
            {data: 'UPC', name: 'UPC'},
            {data: 'BRAND', name: 'Brand'},
            {data: 'DESCRIPTION', name: 'Description'},
            {data: 'SIZE', name: 'Size'},
            {data: 'UOM', name: 'UOM'},
            {data: 'QUANTITY', name: 'Quantity'},
            {data: 'REG_WHLSL', name: 'Reg WHLSL'},
            {data: 'WHLSL_MINUS_VOL', name: 'WHLSL Minus Vol'},
            {data: 'action', name: 'Action', orderable: false},
        ],
		 oLanguage: {
            "sSearch": "Filter results Via ETIN:"
        },
    });
  });


	function syncKeheWithMasterProduct(){
		var table = null;
		var table = $('#datatable').DataTable();
		$('#datatable tbody').on( 'click', 'tr', function () {
			var row = table.row( this ).data();
			window.location="syncKeheWithMasterProduct/"+row['id'];
		});
	}
	function resyncKeheWithMasterProduct(){
		if(confirm ('Item Already Sync with Master Table. Want to Re-Sync??')){
			syncKeheWithMasterProduct();
		} else
		{
			return false;
		}
	}
</script>

@endsection
