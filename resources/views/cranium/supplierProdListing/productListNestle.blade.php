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
    <h3>Supplier NESTLE Product List</h3>
    <table class="table table-bordered data-table" id="datatable">
        <thead>
            <tr>
                <th class="idclass" id="idclass">ID</th>
                <th>Notes</th>
                <th>Description</th>
                <th>Material Number</th>
                <th>Pack Size</th>
                <th>Sales Org</th>
                <th>Distribution Channel</th>
                <th>Retrieving Data</th>
                <th>16 Digit Code</th>
                <th>EDI UA code</th>
                <th>CPL Pallet Specs</th>
                <th>Country of Origin</th>
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
        ajax: "{{ route('getnestleproducts') }}",
        columns: [
            {data: 'id', name: 'ID'},
            {data: 'notes', name: 'Notes'},
            {data: 'description', name: 'Description'},
            {data: 'material_number', name: 'Material Number'},
            {data: 'pack_size', name: 'Pack Size'},
            {data: 'sales_org', name: 'Sales Org'},
            {data: 'distribution_channel', name: 'Distribution Channel'},
            {data: 'retrieving_data', name: 'Retrieving Data'},
            {data: '16_digit_code', name: '16 Digit Code'},
            {data: 'EDI_UA_code', name: 'EDI UA code'},
            {data: 'CPL_pallet_specs', name: 'CPL Pallet Specs'},
            {data: 'country_of_origin', name: 'Country of Origin'},
            {data: 'action', name: 'Action', orderable: false},            
        ],
		 oLanguage: {
            "sSearch": "Filter results Via UPC:"
        },
    });    
  });
  
	/*function editnestleproduct(){
		var table = null;
		var table = $('#datatable').DataTable();
		$('#datatable tbody').on( 'click', 'tr', function () {
			var row = table.row( this ).data();
			window.location="nestleproductlist/"+row['id']; 
		});
	}
	function syncwithmaster(){
		var table = null;
		var table = $('#datatable').DataTable();
		$('#datatable tbody').on( 'click', 'tr', function () {
			var row = table.row( this ).data();
			window.location="syncwithmaster/"+row['id']; 
		});
	}*/
</script>

@endsection