@extends('layouts.master')

@section('page-css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.css" rel="stylesheet">  
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
    <style>
        .error{
            color:red;
        }
    </style>
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Product Listing Filter</h1>
        
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header text-right bg-transparent">
                    <a href="#" class="btn btn-primary btn-md m-1" onClick="GetModelFilter('{{route('product_listing_filters.create',$type)}}')"><i class="i-Add text-white mr-2"></i>Add Product Filter List</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="ProductListingFilterList" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Label</th>
                                    <th>Column</th>
                                    <th>Text or Select</th>
                                    <th>Is Default</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody  id="listing_body">
                               @if($result)
                                @foreach($result as $row)
                                    <tr id="{{$row->id}}">
                                        <td>{{ $row->label_name }}</td>
                                        <td>{{ $row->column_name }}</td>
                                        <td>{{ $row->text_or_select }}</td>
                                        <td><?php if($row->is_default == 1) echo "Yes"; else echo 'No';?></td>
                                        <td>
                                            <a onClick="GetModelFilter('{{route('product_listing_filters.edit',$row->id)}}')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a>

                                            <form class="d-inline" action="{{ route('product_listing_filters.destroy',$row->id) }}" method="POST">
                                                {{method_field('DELETE')}}
                                                {{csrf_field()}}
                                                <button type="submit" class="btn btn-danger mr-1 btn-sm" value="delete" onClick="return confirm('Are You Sure You Want To Delete This?')">Delete</button>
                                                
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                               @endif
                            </tbody>
                        </table>
                        <button class="btn btn-primary" id="save_listing_order" onclick="save_listing_order()">Save Listing Order</button>
                        <input type="hidden" name="listing_order" id="listing_order">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="MyModal" data-backdrop="static">
    </div>
    <!-- end of col -->
@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js"></script> 
    <script>

    $('#listing_body').sortable({
        update: function(event, ui) {
            var productOrder = $(this).sortable('toArray').toString();
            $("#listing_order").val(productOrder);
        }
    });

       
    function GetModelFilter(url){
        $.ajax({
            url:url,
            method:'GET',
            success:function(res){
                $("#MyModal").html(res);
                $("#MyModal").modal();
            }
        });
    }

    function save_listing_order(){
        var order = $('#listing_order').val();
        $.ajax({
            type: 'POST',
            data: {order:order,type:'{{$type}}'},
            url:  "<?php echo route('product_listing_filters.save_listing_order')?>",
            success: function(response){
                // location.reload();
            }		
        });
    }

    </script>
@endsection