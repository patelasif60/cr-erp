@extends('layouts.master')

@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/custom/css/custom.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">

@endsection
@section('main-content')
<style>
	.notification-dropdown .dropdown-item .notification-icon {
		height: 100%;
		width: 44px;
		display: flex;
		align-items: center;
		justify-content: center;
	}
</style>
			@if(count($errors) > 0 )
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			<ul class="p-0 m-0" style="list-style: none;">
				@foreach($errors->all() as $error)
				<li>{{$error}}</li>
				@endforeach
			</ul>
			</div>
			@endif
			<div class="breadcrumb">
                <h1>Notification</h1>
                <ul>
                    <li><a href="">Home</a></li>
                    <li>Notification</li>
                </ul>
            </div>

<div class="card feed-post">
	<div id="content" class="row view-list">
		<div class="col-md-6">
				<div class="card-header" style="height: 60px;">
					<h3 class="w-50 float-left card-title m-0" style="padding-top: 10px;">All Notifications</h3>
				</div>
				

				
				<ul class="notification-dropdown">
					@php
					$user = auth()->user();
					$count = count($user->unreadNotifications);
					@endphp
					@foreach ($user->notifications as $notification)
						<li class="border rounded">
							<a href={{ $notification->data['url'] }}>
								<div class="dropdown-item d-flex" style="@if($notification->read_at == '') background: #e4e6e9; @endif" onClick="MarkAsRead('{{ $notification->id }}')">
									<div class="notification-icon">
										<span style='border: 1px solid #111!important;border-radius: 50%;padding: 5px 10px 2px 10px;'>
											<?php echo ucwords(substr($notification->data['name'], 0, 1)); ?>
										</span>
									</div>
									<div class="notification-details flex-grow-1">
										<p class="m-0 d-flex align-items-center">
											<span>{{ $notification->data['type'] }}</span>
											<!-- <span class="badge badge-pill badge-primary ml-1 mr-1">new</span> -->
											<!-- <span class="flex-grow-1"></span> -->
											<span class="text-small text-muted ml-auto">{{ $notification->created_at->diffForHumans() }}</span>
										</p>
										<p class="text-small text-muted m-0"><b><?php echo htmlspecialchars_decode($notification->data['note']); ?></b> </p>
									</div>
								</div>
							</a>
						</li>
					@endforeach
				</ul>
			</div>
	</div>
</div>

@endsection

@section('page-js')
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>
	<script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
	<script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>
	<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
	<script src="{{asset('assets/js/es5/dashboard.v2.script.js')}}"></script>
	<script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>
	<script src="{{asset('assets/js/sweetalert.script.js')}}"></script>

	 <script type="text/javascript">
	function viewfeedpopup(id){			
		 var text = document.getElementById("popuphtml-" + id);		 
		 
		  
		swal({
			html: text,  
			confirmButtonText: "Ok", 
			customClass: 'swal-wide',
			});
	}
		

	function setCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    } else var expires = "";
    document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
}

function readCookie(name) {
    var nameEQ = escape(name) + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return unescape(c.substring(nameEQ.length, c.length));
    }
    return null;
}

function eraseCookie(name) {
    setCookie(name, "", -1);
}

/* Layout Preference Functions */

$(function() {
  var view = readCookie('view-pref');
  if (view != null) {
    $('#content').removeClass('view-list').addClass(view);
  }
});

$('#view-control button[value="view-blocks"]').click(function() {
  // This Method requires the jQuery UI Library:
  $('#content').switchClass('view-list','view-grid',0);
  $('#view-list').switchClass('active','notactive',0);
  $('#view-blocks').switchClass('notactive','active',0);
  // This method works in jQuery but takes two steps:
  //$('#content').toggleClass('view-list').toggleClass('view-grid');
  
  setCookie('view-pref','view-grid');

  return false;

});

$('#view-control button[value="view-list"]').click(function() {
  $('#content').switchClass('view-grid','view-list',0);
  $('#view-blocks').switchClass('active','notactive',0);
  $('#view-list').switchClass('notactive','active',0);
  setCookie('view-pref','view-list');

  return false;

});
</script>

@endsection