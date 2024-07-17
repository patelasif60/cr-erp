

<?php $__env->startSection('page-css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/datatables.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('assets/custom/css/custom.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/sweetalert2.min.css')); ?>">
<?php $__env->stopSection(); ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<?php $__env->startSection('main-content'); ?>
<?php if(count($errors) > 0 ): ?>
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
	<ul class="p-0 m-0" style="list-style: none;">
		<?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<li><?php echo e($error); ?></li>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</ul>
	</div>
<?php endif; ?>
<div class="breadcrumb">
	<h1>Cranium</h1>
	<ul>
		<li><a href="">Dashboard</a></li>
		<li>Home</li>
	</ul>
</div>
<div class="card text-left mb-5">
	<div class="card-header bg-dark" style="height: 60px;">
		<h3 class="w-50 float-left card-title m-0 text-white" style="padding-top: 10px;">Orders Table By Transit Day</h3>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-12">
				<?php echo $__env->make('report_section.parts.container_tb_total_td', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			</div>
		</div>
	</div>
</div>
<div class="card text-left mb-5">
	<div class="card-header bg-dark" style="height: 60px;">
		<h3 class="w-50 float-left card-title m-0 text-white" style="padding-top: 10px;">Orders Table By Order Status</h3>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-12">
				<?php echo $__env->make('report_section.parts.container_tb_total_os', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			</div>
		</div>
	</div>
</div>
<div class="card text-left mb-5">
	<div class="card-header bg-dark" style="height: 60px;">
		<h3 class="w-50 float-left card-title m-0 text-white" style="padding-top: 10px;">Orders Table By Ship Day</h3>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-12">
				<?php echo $__env->make('report_section.parts.container_tb_total_sd', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			</div>
		</div>
	</div>
</div>
<?php echo $__env->make('weather', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('countboxes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('download', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<!--<div class="card">
	<div id="content" class="view-list row">
		<div  class="col-md-12" style="padding-right: 0px; padding-left: 0px;">
			<header>
				<h3 class="w-50 float-left card-title m-0">News Feed</h3>
				<span id="view-control">
					<button id="view-blocks" value="view-blocks" class="btn  btn-primary m-1">Blocks</button>
					<button id="view-list" value="view-list" class="btn  btn-primary m-1">List</button>
				</span>
			</header>
			
			<ul id="post-list">
				<?php $__currentLoopData = $feeds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feed): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<li class="">				
						<h2><?php echo e($feed->feed_title); ?></h2>
						<div class="excerpt">
							<p><?php echo $feed->feed_description; ?></p>
						</div>
						<div class="byline"><a href="">By <?php echo e($feed->feed_auth_name); ?></a></div>
					</li>
					
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</ul>
		</div>
	</div>
</div>-->
<div class="card feed-post">
	<div id="content" class="row view-list">
		<div class="col-md-12">
				<div class="card-header" style="height: 60px;">
					<h3 class="w-50 float-left card-title m-0" style="padding-top: 10px;">News Feed</h3>
					<span id="view-control">
												
						<div class="dropdown dropleft text-right w-50 float-right">
							<button id="view-blocks" value="view-blocks" class="btn  btn-primary m-1"><i class="fas fa-th-large"></i></button>
						<button id="view-list" value="view-list" class="btn  btn-primary m-1"><i class="fas fa-list"></i></button>
						</div>
					</span>
				</div>
				

			
				<ul id="post-list">
					<?php $__currentLoopData = $feeds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feed): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php 
							$feeddesc = $feed->feed_description;
							$length = strlen($feeddesc);
							if ($length > 500 ) {
								$feeddesc = substr($feeddesc, 0, 250).'...</p></div>';
									libxml_use_internal_errors(true);

									$dom = new DOMDocument();
									$dom->loadHTML('<root>' . $feeddesc . '</root>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
									$xpath = new DOMXPath($dom);

									foreach( $xpath->query('//*[not(node())]') as $node ) {
										$node->parentNode->removeChild($node);
									}
									$feeddesc = substr($dom->saveHTML(), 6, -8);
							}

						?>
						<li class="feed_single">				
							<h2 class="feed_header"><?php echo e($feed->feed_title); ?></h2>
							<!-- <div class="excerpt">
								<p> -->
								
								<!-- </p>
							</div>
							<br> -->
							<div class="byline feed_auth">Date : <?php if($feed->created_at != ''): ?><?php echo e(date('Y-m-d',strtotime($feed->created_at))); ?><?php endif; ?></div>
							<div class="byline feed_auth">Author : <?php echo e($feed->feed_auth_name); ?></div>
							<!--<button id="viewfeedpopup/<?php echo e($feed->id); ?>" onclick='viewfeedpopup(<?php echo e($feed->id); ?>)' class="viewfeedpopup">Sweat Alert Feed </button/>-->
							<button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#exampleModal<?php echo e($feed->id); ?>"><i class="fas fa-rss"></i> &nbsp; View Full Feed</button>
						</li>
						<!-- Sweat Alert Popup -->
						<!--<div class="parentpopup" style="display:none;">
							<div id="popuphtml-<?php echo e($feed->id); ?>">
								<h2><?php echo e($feed->feed_title); ?></h2>
								<div class="byline feed_auth">Author : <?php echo e($feed->feed_auth_name); ?></div>
								<div class="excerpt">
									<p><?php echo $feed->feed_description; ?></p>
								</div>
								<br>
								
							</div>
						</div>-->
						<!-- Bootstrap Modal -->
						<div class="modal fade" id="exampleModal<?php echo e($feed->id); ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-xl" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title feed_header_modal" id="exampleModalLabel"><?php echo e($feed->feed_title); ?></h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
									<?php echo $feed->feed_description; ?>

									</div>

									
									<div class="modal-footer">
										
										<div class="col-md-6 byline feed_auth_modal">Author : <?php echo e($feed->feed_auth_name); ?></div>
										<div class="col-md-6">
											<button type="button" class="btn btn-secondary cancel-for-feed" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<?php $feeddesc = null; ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</ul>
			</div>
	</div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>
	<script src="<?php echo e(asset('assets/js/vendor/echarts.min.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/es5/echart.options.min.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/vendor/datatables.min.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/es5/dashboard.v2.script.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/vendor/sweetalert2.min.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/sweetalert.script.js')); ?>"></script>

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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\cranium_new\resources\views/landingpage.blade.php ENDPATH**/ ?>