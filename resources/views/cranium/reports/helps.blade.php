@extends('layouts.master')

@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Queries</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            {{-- In Progress Ticket --}}
            <div class="card text-left">
                <div class="card-header bg-primary">
                    <div class="row">
                        <div class="col-md-4">
                            <h3 class="w-100 float-left card-title m-0 text-white">In Progress</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">                        
                        <table id="all_helps" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Type</th>
                                    <th>Level</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Attachment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($helps)
                                    @foreach($helps as $row)
                                        <tr>
                                            <td>{{ $row['name'] }}</td>
                                            <td>{{ $row['type'] }}</td>
                                            <td>{{ $row['urgent_level'] }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($row['desc'], 50) }}</td>
                                            <td>{{ $row['date'] }}</td>
                                            <td>
                                                @if (isset($row['image_url']) && $row['image_url'] !== '')
                                                    <a href="{{ url($row['image_url']) }}" data-toggle="tooltip" data-placement="top" title="Download">
                                                        Download Attachment<i class="nav-icon i-Data-Download"></i>
                                                    </a>
                                                @endif                                                
                                            </td>
                                            <td>
                                                <button class="btn btn-primary" onclick="showDetails({{ $row['id'] }})">View Details</button>
                                                <button class="btn btn-success" onclick="resolveTicket({{ $row['id'] }})">Resolve</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>                        
                    </div>
                </div>
                @if($helps)
                    <div class="card-footer">
                        <a class="btn btn-primary text-white" href="{{ route('download_help_csv', 1) }}">Download CSV</a>
                    </div>
                @endif 
            </div>
            <div class="separator-breadcrumb border-top"></div>
            {{-- Resolved Tickets --}}
            <div class="card text-left">
                <div class="card-header bg-primary">
                    <div class="row">
                        <div class="col-md-4">
                            <h3 class="w-100 float-left card-title m-0 text-white">Resolved</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">                        
                        <table id="all_helps_resolved" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Type</th>
                                    <th>Level</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Attachment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($helps_resolved)
                                    @foreach($helps_resolved as $row)
                                        <tr>
                                            <td>{{ $row['name'] }}</td>
                                            <td>{{ $row['type'] }}</td>
                                            <td>{{ $row['urgent_level'] }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($row['desc'], 50) }}</td>
                                            <td>{{ $row['date'] }}</td>
                                            <td>
                                                @if (isset($row['image_url']) && $row['image_url'] !== '')
                                                    <a href="{{ url($row['image_url']) }}" data-toggle="tooltip" data-placement="top" title="Download">
                                                        Download Attachment<i class="nav-icon i-Data-Download"></i>
                                                    </a>
                                                @endif                                                
                                            </td>                                            
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>                        
                    </div>
                </div>
                @if($helps_resolved)
                    <div class="card-footer">
                        <a class="btn btn-primary text-white" href="{{ route('download_help_csv', 2) }}">Download CSV</a>
                    </div>
                @endif                  
            </div>
        </div>            
    </div> 
    
    <div class="modal fade" id="help_detail_modal" role="dialog" aria-labelledby="help_detail_modal" aria-hidden="true"></div>
    
@endsection

@section('page-js')
<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $('#all_helps').DataTable();
        $('#all_helps_resolved').DataTable();
    });
    function showDetails(helpId) {
        $.ajax({
            type:'GET',
            url: '/get_help_details/' + helpId,
            processData: false,
            contentType: false,
            success:function(response){                    
                $("#help_detail_modal").html('');
                $('#help_detail_modal').html(response);
                $('#help_detail_modal').modal('show');
            }
        });
    }
    function resolveTicket(helpId) {
        $.ajax({
            type:'PUT',
            url: '/resolve_help/' + helpId,
            processData: false,
            contentType: false,
            success: function(response){
                if(response.error == false){
                    toastr.success(response.msg);
                    setTimeout(function(){
                        $('#help_detail_modal').modal('hide');
                        window.location.reload();
                    }, 2000);
                } else {
                    toastr.error(response.msg);
                }                
            },
            error: function(data){
                toastr.error("Some error occurred");
            }
        });        
    }
</script>
@endsection