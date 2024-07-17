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
    <h3>Supplier MARS Product List</h3>
    <div class="table-responsive">

        <table class="display table table-striped table-bordered data-table" id="datatable">
            <thead>
                <tr>
                    <th class="idclass" id="idclass">ID</th>
                    <th>ETIN</th>
                    <th>Brand</th>
                    <th>Pack Type</th>
                    <th>Product</th>
                    <th>Item No</th>                
                    <th>GTIN 14 Digit UPC</th>
                    <th>Price & Weight</th>      
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        
    </div>
    
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
        ajax: "{{ route('getmarsproducts') }}",
        columns: [
            {data: 'id', name: 'ID'},
            {data: 'ETIN', name: 'ETIN'},
            {data: 'brand', name: 'Brand'},
            {data: 'pack_type', name: 'Pack Type'},
            {data: 'product', name: 'Product'},
            {data: 'ITEM_NO', name: 'Item No'},
            {data: 'GTIN_14_digit_case_UPC', name: 'GTIN 14 Digit UPC'},
            {data: 'PRICE_AND_WEIGHT_SCHEDULE_2_4_PALLETS', name: 'Price & Weight Schedule 2 4 Pallets'},
            {data: 'action', name: 'Action', orderable: false},            
        ],
		 oLanguage: {
            "sSearch": "Filter results Via ETIN:"
        },
    });    
  });
  
	/*function editmarsproduct(){
		var table = null;
		var table = $('#datatable').DataTable();
		$('#datatable tbody').on( 'click', 'tr', function () {
			var row = table.row( this ).data();
			window.location="marsproductlist/"+row['id']; 
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
    function syncMarsWithMasterProduct(){
        var table = null;
        var table = $('#datatable').DataTable();
        $('#datatable tbody').on( 'click', 'tr', function () {
            var row = table.row( this ).data();
            window.location="syncMarsWithMasterProduct/"+row['id']; 
        });
    }
		function resyncMarsWithMasterProduct(){
		if(confirm ('Item Already Sync with Master Table. Want to Re-Sync??')){
			syncMarsWithMasterProduct();
		} else
		{
			return false;
		}
	}
</script>

@endsection