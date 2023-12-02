<?php
include 'partials/headerarea.php';
include 'partials/header.php';
?>

<body class="main-body app sidebar-mini ltr">

	<!-- Loader -->
	<div id="global-loader">
		<img src="assets/img/svgicons/loader.svg" class="loader-img" alt="Loader">
	</div>
	<!-- /Loader -->

	<!-- Page -->
	<div class="page custom-index">
		<div>
			<!-- main-header -->
			<?php
			include 'partials/navbar.php';
			?>
			<!-- /main-header -->

			<!-- main-sidebar -->
			<?php
			include 'partials/sidebar.php';
			?>
			<!-- main-sidebar -->
		</div>

		<!-- main-content -->
		<div class="main-content app-content">

			<!-- container -->
			<div class="main-container container-fluid">

				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="left-content">
						<div>
							<h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Hi, welcome back!</h2>
							<p class="mg-b-0">Sales monitoring dashboard template.</p>
						</div>
					</div>
					<div class="main-dashboard-header-right">
						<div>
							<label class="tx-13">Customer Ratings</label>
							<div class="main-star">
								<i class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i class="typcn typcn-star"></i> <span>(14,873)</span>
							</div>
						</div>
						<div>
							<label class="tx-13">Online Sales</label>
							<h5>563,275</h5>
						</div>
						<div>
							<label class="tx-13">Offline Sales</label>
							<h5>783,675</h5>
						</div>
					</div>
				</div>
				<!-- breadcrumb -->

				<!-- row -->
				<div class="row row-sm">
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">

					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">

					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">

					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">

					</div>
				</div>
				<!-- row closed -->
			</div>
			<!-- /Container -->
		</div>
		<!-- /main-content -->

		<!-- Footer opened -->
		<?php
		include 'partials/footer.php';
		include 'partials/modal.php';
		?>
		<!-- Footer closed -->

	</div>
	<!-- End Page -->

	<!-- Back-to-top -->
	<?php
	include 'partials/library.php';
	?>

</body>

</html>