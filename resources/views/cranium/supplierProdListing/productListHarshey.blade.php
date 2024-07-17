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
    <h3>Supplier Harshey Product List</h3>
    <table class="table table-bordered data-table" id="datatable">
        <thead>
            <tr>
                <th class="idclass" id="idclass">ID</th>
                <th>ETIN</th>
                <th>Promoted Product Groups</th>
                <th>Brand</th>
                <th>Item No</th>
                <th>Description</th>
                <th>Pkg</th>
                <th>UPC</th>
                <th>Price 2 1000 5 999 lbs</th>
                <th>Price 3 6000 24 999 lbs</th>
                <th>Price 4 25000 lbs</th>
                <th>Net Wt</th>
                <th>Net Wt UOM</th>
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
        ajax: "{{ route('gethersheyproducts') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'ETIN', name: 'ETIN'},
            {data: 'promoted_product_groups', name: 'promoted_product_groups'},
            {data: 'brand', name: 'brand'},
            {data: 'item_no', name: 'item_no'},
            {data: 'description', name: 'description'},
            {data: 'pkg', name: 'pkg'},
            {data: 'UPC', name: 'UPC'},
            {data: 'price_sch_2_1000_5_999_lbs', name: 'price_sch_2_1000_5_999_lbs'},
            {data: 'price_sch_3_6000_24_999_lbs', name: 'price_sch_3_6000_24_999_lbs'},
            {data: 'price_sch_4_25000_lbs', name: 'price_sch_4_25000_lbs'},
            {data: 'net_wt', name: 'net_wt'},
            {data: 'net_wt_UOM', name: 'net_wt_UOM'},
            {data: 'action', name: 'Action', orderable: false},
        ],
		 oLanguage: {
            "sSearch": "Filter results Via UPC:"
        },
    });
  });


	function syncHarsheyWithMasterProduct(id){
        window.location="syncHarsheyWithMasterProduct/"+id;
	}
    function resyncHarsheyWithMasterProduct(id){
		if(confirm ('Item Already Sync with Master Table. Want to Re-Sync??')){
			syncHarsheyWithMasterProduct(id);
		} else
		{
			return false;
		}
    }
</script>

@endsection
