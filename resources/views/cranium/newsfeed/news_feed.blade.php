@extends('layouts.master')

@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<style>
	.note-editor.note-frame.card {
		width: 100%;
	}
</style>
@endsection

@section('main-content')
<div class="card">
	<div class="card-header bg-transparent">
		<div class="col-md-6" style="float: left;">
			<h3 class="card-title"> ADD News Feed</h3>
		</div>
	</div>
	<div class="card-body">
		<div class="card">
			<form method="POST" action="{{ route('insertfeed') }}" enctype="multipart/form-data">
				@csrf
				@method('put')  
		
				<div class="form-group col-md-12">
					<div class="form-group col-md-12">
						<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Feed Title">Feed Title <span class="text-danger">*</span></label>

						<div class="input-group mb-12">
							<input type="text" class="form-control" id="feed_title" placeholder="Feed Title" name ="feed_title" <?php if(isset($feed)) { ?> value='{{$feed->feed_title}}' <?php } ?> required>
						</div>
					</div>
					<div class="form-group col-md-12">
						<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Feed Description goes here">Description<span class="text-danger">*</span></label>
						
						<div class="input-group mb-12">
							<textarea class="form-control" id="feed_description" name="feed_description" placeholder="Feed Description" required><?php if(isset($feed)) { ?> {!!$feed->feed_description!!} <?php } ?></textarea>
						</div>
					</div>
					<div class="form-group col-md-12">
						<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="The Author of this feed">&nbsp;Author Name:</label>
							<input type="text" class="form-control" name="feed_auth_name" placeholder="Feed Author Name" <?php if(isset($feed)) { ?> value='{{$feed->feed_auth_name}}' <?php } ?> style="width: 92%; float: right;">
							<input type="hidden" class="form-control" id="feed_auth_id" name="feed_auth_id" value="{{ Auth::user()->id }}" >

<?php if(isset($feed)) { ?>
	<input type="hidden" class="form-control" id="exsisting_id" name="exsisting_id" value="{{$feed->id}}" >
<?php } ?>
							
					</div>
					<div class="card-footer">
						<div class="mc-footer">
							<div class="row">
								<div class="col-lg-12 text-center">
									<input type="submit" class="btn  btn-primary m-1" value="Save">
									<input type="cancel" class="btn  btn-primary m-1" value="Cancel">
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>			
		</div>
	</div>
</div>
<br>

			<div class="row">
                <div class="col-md-12">
                    <div class="card o-hidden mb-4">
                        <div class="card-header">
                            <h3 class="w-50 float-left card-title m-0">News Feed List (<b>All Author</b>)</h3>
                            
							<a onclick="refreshdatatable()"><img src="{{ asset('assets/images/refresh.png') }}" style="width: 25px; float: right; cursor: pointer;"></a>
						</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="newsfeedtable" class="table table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <!--<th scope="no">Category</th>-->
                                            <th scope="col" id="idclass">#</th>
                                            <th scope="col">Feed Title</th>
                                            <th scope="col">Feed Author Name</th>											
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
@endsection

@section('page-js')

<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
	
	
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script type="text/javascript">
$(function () {
	
	GetFeed();
});

		$('#feed_description').summernote({
        height: 400
    });
	function editnewsfeed(){
		var table = null;
		var table = $('#newsfeedtable').DataTable();
		$('#newsfeedtable tbody').on( 'click', 'tr', function () {
			var row = table.row( this ).data();
			
			var pageURL = $(location).attr("href");
			var array = pageURL.split("/");
            if(array[4]){
				window.location=row['id']; 
			} else {
				window.location="news_feed/"+row['id']; 
			}
			
		});
	}
	   function GetFeed(){
        var table = $('#newsfeedtable').DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: "{{ route('getnewsfeed') }}",
            columns: [
                {data: 'id', name: 'ID'},
                {data: 'feed_title', name: 'Feed Title'},
                {data: 'feed_auth_name', name: 'Feed Author Name'},
                {data: 'action', name: 'Action', orderable: false},             
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false,
					"searchable":false
					
                }
            ],
            oLanguage: {
                "sSearch": "Filter results: "
            },
        });
		}
</script>
@endsection