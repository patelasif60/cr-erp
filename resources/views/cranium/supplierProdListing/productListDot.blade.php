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
    padding: 10px;
}
.table {
    width: 100%;
    margin-bottom: 0rem;
    background-color: transparent;
}
</style>

<div class="container">
    <h3>Supplier DOT Product List</h3>
    <table class="table table-bordered" id="datatable">
        <thead>
            <tr>
                {{-- <th>ID</th> --}}
                <th>ETIN</th>
                <th>Corporate Supplier</th>
                <th>Product Line</th>
                <th>Brand</th>
                <th>Dot Item</th>
                <th>Manufacturer Item</th>
                <th>UPC</th>
                <th>Item Description</th>
                <th>Diet Type</th>
                <th>Class Of Trade</th>
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

    var table = $('#datatable').DataTable({
        destroy: true,
        responsive: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX: true,
        ajax: "{{ route('getdotproducts') }}",
        columns: [
            // {data: 'id', name: 'ID'},
            {data: 'ETIN', name: 'ETIN'},
            {data: 'corporate_supplier', name: 'corporate_supplier'},
            {data: 'product_line', name: 'product_line'},
            {data: 'brand', name: 'brand'},
            {data: 'dot_item', name: 'dot_item'},
            {data: 'manufacturer_item', name: 'manufacturer_item'},
            {data: 'UPC', name: 'UPC',searchable:true},
            {data: 'item_description', name: 'item_description'},
            {data: 'diet_type', name: 'diet_type'},
            {data: 'class_of_Trade', name: 'class_of_Trade'},
            {data: 'action', name: 'action', orderable: false},
        ],
        oLanguage: {
            "sSearch": "Filter results Via UPC:"
        },
    });
  });


  function syncdotproduct(id){
        window.location="/syncDotWithMasterProduct/"+id;
	}
	function resyncdotproduct(id){
		if(confirm ('Item Already Sync with Master Table. Want to Re-Sync??')){
			syncdotproduct(id);
		} else
		{
			return false;
		}
    }
</script>

@endsection
