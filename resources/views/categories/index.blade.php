@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <style>
        ul li:last-child .jstree-anchor{
            color:green !important;
           
        }
    </style>
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Categories</h1>
       
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <!-- <div class="col-md-4 mb-4">
            <div id="hierarchy_container"></div>
        </div> -->
        <div class="col-md-4 mb-4">
            <div id="hierarchy_container_1"></div>
        </div>
        <!-- <div class="col-md-8 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title text-right task-title m-0"><a href="{{ route('users.create') }}" class="btn btn-primary btn-md m-1"><i class="i-Add-User text-white mr-2"></i> Category</a></h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">

                    </div>
                </div>
            </div>
        </div> -->
    </div>
    <!-- end of col -->
   
@endsection

@section('page-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
   <script>
       $(function(){
            // GetCategoryHeirarchy();
            CategoryFromTopToBottom();
       })
    //    function GetCategoryHeirarchy(type = '', cat = '', type1 = '', sub_cat1 = '', type2 = '', sub_cat2 = ''){
    //        $.ajax({
    //             url: '{{ route('categories.GetCategoryHeirarchy') }}',
    //             method:'GET',
    //             data:{
    //                 type:type,
    //                 cat:cat,
    //                 type1:type1,
    //                 sub_cat1:sub_cat1,
    //                 type2:type2,
    //                 sub_cat2:sub_cat2
    //             },
    //             dataType:'html',
    //             success:function(res){
    //                 $("#hierarchy_container").html(res);
    //             }
    //        });
    //    }


        function CategoryFromTopToBottom(id = ''){
           $.ajax({
                url: '{{ route('categories.CategoryFromTopToBottom') }}',
                method:'GET',
                data:{
                    id:id
                },
                dataType:'html',
                success:function(res){
                    $("#hierarchy_container_1").html(res);
                }
           });
        }
   </script>
@endsection
