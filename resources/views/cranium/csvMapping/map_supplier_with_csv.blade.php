@extends('layouts.master')

@section('main-content')

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default card" style="padding: 20px;">
                <div class="panel-heading"><h2>Map Suppliers with Master Product Table</h2></div>
				<br>
				<br>
                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('import_map_supplier_with_csv') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="">
                                    <label>
                                        Select Supplier Type
                                    </label>

                                    <select name="supplier_name" class="form-control">
                                        <option value="">Select</option>
                                        <option value="supplier_dot">DOT</option>
                                        <option value="supplier_kehe">KEHE</option>
                                        <option value="supplier_dryers">Dryers</option>
                                        <option value="supplier_mars">Mars</option>
                                        <option value="supplier_miscellaneous">Miscellaneous</option>
                                        <option value="supplier_3pl">3PL</option>
                                        <option value="supplier_nestle">Nestle</option>
                                        <option value="supplier_hersley">Hershey</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <br>

                        <div class="form-group{{ $errors->has('csv_file') ? ' has-error' : '' }}">
                            <label for="csv_file" class="col-md-4 control-label">CSV file to import</label>

                            <div class="col-md-6">
                                <input id="csv_file" type="file" class="form-control" name="csv_file" required>

                                @if ($errors->has('csv_file'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('csv_file') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <!-- <label>
                                        <input type="checkbox" name="header" checked> File contains header row?
                                    </label> -->
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Parse CSV
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection