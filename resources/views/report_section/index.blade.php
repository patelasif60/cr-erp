@extends('layouts.master')

@section('main-content')
    
    @php
    $types = [
        'Open Orders',
        'Shipped Orders',
        'Inventory',
        'OOD',
        'Shipped Items',
    ];
    @endphp

    <div class="breadcrumb">
        <h1>Report Section</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>
    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Generate Reports</h6>
                </div>
                <div class="card-body">
                    <div class="form-group col-md-4">
                        <label for="report_type" class="ul-form__label">Report Type:</label>
                        <select class="form-control select2" id="report_type" name="report_type">
                            <option value="">--Select--</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <button type="button" class="btn btn-primary" onclick="GetFilters()">
                            Show / Hide Columns
                        </button>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="mc-footer">
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-primary m-1">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>             
            </div>
        </div>
    </div>
    <div class="modal fade" id="MyColumnsModal" data-backdrop="static">
    </div>
@endsection

@section('page-js')
<script type="text/javascript">
    
    function GetFilters(){
        
        var options = document.getElementById("report_type");
        var type = options.options[options.selectedIndex].text;

        if (options.selectedIndex === 0) {
            return;
        }

        $.ajax({
            url: '/reports-filter/' + type,
            method: 'GET',
            success:function(res){
                $("#MyColumnsModal").html(res);
                $("#MyColumnsModal").modal();
            }
        });
    }

    function SaveFilters() {
        
        var checkboxes = document.getElementsByName('columns');
        var result = "";

        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                result += checkboxes[i].value + ", ";
            }
        }
        
        if (result === '') {
            return
        }

        var options = document.getElementById("report_type");
        var type = options.options[options.selectedIndex].text;

        if (options.selectedIndex === 0) {
            return;
        }

        let form_data = new FormData();
        form_data.append('col_ids', result);
        form_data.append('report_type', type)

        $.ajax({
            url: '/save-filters',
            type: 'POST',
            data: form_data,
            processData: false,
            contentType: false,
            success: function(res) {
                if(response.error == 0) {
                    toastr.success(response.msg);
                } else {
                    toastr.error(response.msg);
                }
            }
        });
    }

</script>
@endsection