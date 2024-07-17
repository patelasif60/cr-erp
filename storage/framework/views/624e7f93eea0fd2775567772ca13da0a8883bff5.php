<div class="card" style="padding:10px; text-align:center;">
	<div class="container-fluid">

		<div class="row">
			<div class="col-lg-3 col-6">
				<!-- small box -->
				<div class="small-box bg-info">
					<div class="inner" style="padding:20px; color:#fff;">
						<h3 style="color:#fff;"><?php echo e($feedcount); ?></h3>

						<p>News Feed</p>
					</div>
					<div class="icon">
						<i class="ion ion-bag"></i>
					</div>
				</div>
			</div>
			<!-- ./col -->
			<div class="col-lg-3 col-6">
				<!-- small box -->
				<div class="small-box bg-success">
					<div class="inner" style="padding:20px; color:#fff;">
						<h3 style="color:#fff;"><?php echo e($approveproductcount); ?></h3>

						<p>Products Listed</p>
					</div>
					<div class="icon">
						<i class="ion ion-stats-bars"></i>
					</div>
				</div>
			</div>
			<!-- ./col -->
			<div class="col-lg-3 col-6">
				<!-- small box -->
				<div class="small-box bg-warning">
					<div class="inner" style="padding:20px; color:#fff;">
						<h3 style="color:#fff;"><?php echo e($notapproveproductcount); ?></h3>

						<p>New Products Pending Approval</p>
					</div>
					<div class="icon">
						<i class="ion ion-person-add"></i>
					</div>
				</div>
			</div>
			<!-- ./col -->
			<div class="col-lg-3 col-6">
				<!-- small box -->
				<div class="small-box bg-danger">
					<div class="inner" style="padding:20px; color:#fff;">
						<h3 style="color:#fff;"><?php echo e($queueproductcount); ?></h3>

						<p>Products Require Attention</p>
					</div>
					<div class="icon">
						<i class="ion ion-pie-graph"></i>
					</div>
				</div>
			</div>
			<!-- ./col -->
		</div>
	</div>
</div>
<br>
<?php /**PATH C:\wamp64\www\cranium_new\resources\views/countboxes.blade.php ENDPATH**/ ?>