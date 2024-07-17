@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
@endsection
@php
    $data = \App\UpsZipZoneByWH::orderBy('id')->get();
@endphp
@section('main-content')
    <div class="breadcrumb">
        <h1>Carrier Management & Configuration</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>
    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">                
                <div class="card-header">
                    <h3 class="w-100 float-left card-title m-0">
                        Zip Zone WH Transit Days
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="zip_zone_wh" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>State</th>
                                    <th>Zip 3</th>
                                    <th>Zone WI</th>
                                    <th>Transit Day WI</th>
                                    <th>Zone PA</th>
                                    <th>Transit Day PA</th>
                                    <th>Zone NV</th>
                                    <th>Transit Day NV</th>
                                    <th>Zone OKC</th>                                    
                                    <th>Transit Day OKC</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($data)
                                    @foreach($data as $key => $row)
                                        <tr>
                                            <td>{{ $row->state }}</td>
                                            <td>{{ $row->zip_3 }}</td>
                                            <td>{{ $row->zone_WI }}</td>
                                            <td>{{ $row->transit_days_WI }}</td>
                                            <td>{{ $row->zone_PA }}</td>
                                            <td>{{ $row->transit_days_PA }}</td>
                                            <td>{{ $row->zone_NV }}</td>
                                            <td>{{ $row->transit_days_NV }}</td>
                                            <td>{{ $row->zone_OKC }}</td>
                                            <td>{{ $row->transit_days_OKC }}</td>
                                            <td><a href="#" class="btn btn-warning btn-sm" onclick="showEditDialog({{ $row->id }});">Edit</a></td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>                
            </div>
        </div>
    </div>
    <!-- end of col -->
@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script>
       $(document).ready(function () {

        $(document).on("preInit.dt", function(){
            $(".dataTables_filter input[type='search']").attr("maxlength", 3);     
        });

        $('#zip_zone_wh').DataTable({
            "ordering": false, 
            "oLanguage": {
                "sSearch": "Search Zip (1st 3 Numbers Only):"
            }
        });            
       });

       function showEditDialog(id) {
            var url = "{{ url('edit_zip_zone_wh') }}/" + id;
            GetModel(url);
       }

       function updateTransitDays() {
            swal({
				title: 'Are you sure you want to change the Transit Days?',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Confirm'
			}).then((result) => {
                if(result) {
                    initiateUpdateTransitDay();
                } else {
					return; 
				}
            });            
       }

       function initiateUpdateTransitDay() {
            var id = document.getElementById('zip_wh_id').value;
            var tdWi = document.getElementById('td_wi').value;
            var tdPa = document.getElementById('td_pa').value;
            var tdNv = document.getElementById('td_nv').value;
            var tdOkc = document.getElementById('td_okc').value;

            var form = new FormData();
            if (tdWi && tdWi.trim() !== '') form.append('td_wi', tdWi);
            if (tdPa && tdPa.trim() !== '') form.append('td_pa', tdPa);
            if (tdNv && tdNv.trim() !== '') form.append('td_nv', tdNv);
            if (tdOkc && tdOkc.trim() !== '') form.append('td_okc', tdOkc);
            form.append('id', id);

            $.ajax({
                url: '{{route('update_transit_day')}}',
                method: 'POST',
                data: form,
                processData: false,
                contentType: false,
                success: function(res) {
                    if(res.error === 0) {
                        toastr.success(res.msg);
                        setTimeout(function(){
                            window.location.reload();
                        },2000);                        
                    } else {
                        toastr.error(res.msg);
                    }
                }			
            });
       }

   </script>
@endsection
