<div class="row mb-4">
    <div class="col-md-12 mb-4">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">
                <a href="javascript:void(0);" onclick="getModal('{{ route('sku.create') }}')" class="btn btn-success btn-icon m-1" style=" float: right;">
                    <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; New Exclusion
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="excluded_sku_table" class="table table-bordered text-center min-w-full" style="width:100%">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th scope="col">SKU</th>
                                <th scope="col">Client</th>
                                <th scope="col">Channel</th>
                                <th scope="col">Action</th>          
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($excluded_skus))
                                @foreach ($excluded_skus as $sku)
                                    <tr>
                                        <td>{{ $sku->sku }}</td>
                                        <td>{{ $sku->client->company_name }}</td>
                                        <td>{{ $sku->channel->channel }}</td>
                                        <td>
                                            <a class="btn btn-warning text-white" data-toggle="tooltip" data-placement="top" title="Edit" onclick="getModal('{{ route('sku.edit', [$sku->client_id, $sku->master_product_id]) }}')">Edit</a>
                                            <a class="btn btn-danger text-white" data-toggle="tooltip" data-placement="top" title="Delete" onclick="deleteSku({{ $sku->id }})">Delete</a>
                                        </td>                    
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="4">No Record Found</td></tr>
                            @endif        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#excluded_sku_table').DataTable();
    });

    function deleteSku(skuId) {
        /*swal({
            title: 'Are you sure?',
            text: "This information will be permanently deleted!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then(function(result) {
            if(result) {
                
            }
        });*/
        $.ajax({
            type: "DELETE",
            url: '/delete_exclusion/' + skuId,
            processData: false,
            contentType: false,
            success: function( response ) {
                if(response.error == 0){
                    toastr.success(response.msg);
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);                        
                }else{
                    $(".submit").attr("disabled", false);
                    toastr.error(response.msg);
                }
            },
            error: function(data){
                $(".submit").attr("disabled", false);
                var errors = data.responseJSON;
                $("#error_container").html('');
                $.each( errors.errors, function( key, value ) {
                    var ele = "#"+key;
                    $(ele).addClass('error_border');
                    $('<label class="error">'+ value +'</label>').insertAfter(ele);
                    $("#error_container").append("<p class='bg-danger mb-1 text-white p-1'>"+ value +"</p>");
                    toastr.error(value);
                });
            }
        })       
    }

</script>