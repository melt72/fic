<?php
include '../include/init.php';
//
if (isset($_COOKIE[$sessione])) :
	header('location: home.php');
endif;
include 'partials/header.php';
?>

<body class="ltr error-page1 main-body bg-light text-dark error-3 login-img">


	<!-- Loader -->
	<div id="global-loader">
		<img src="assets/img/svgicons/loader.svg" class="loader-img" alt="Loader">
	</div>
	<!-- /Loader -->

	<!-- Page -->
	<div class="page">

		<div class="container-fluid">
			<div class="row no-gutter">
				<!-- The image half -->
				<div class="col-md-6 col-lg-6 col-xl-7 d-none d-md-flex bg-primary-transparent">
					<div class="row wd-100p mx-auto text-center">
						<div class="col-md-12 col-lg-12 col-xl-12 my-auto mx-auto wd-100p">
							<img src="assets/img/pngs/8.png" class="my-auto ht-xl-80p wd-md-100p wd-xl-80p mx-auto" alt="logo">
						</div>
					</div>
				</div>
				<!-- The content half -->
				<div class="col-md-6 col-lg-6 col-xl-5 bg-white py-4">
					<div class="login d-flex align-items-center py-2">
						<!-- Demo content-->
						<div class="container p-0">
							<div class="row">
								<div class="col-md-10 col-lg-10 col-xl-9 mx-auto">
									<div class="card-sigin">
										<div class="mb-5 d-flex">
											<a href="index.php"><img src="assets/img/brand/favicon.png" class="sign-favicon-a ht-40" alt="logo">
												<img src="assets/img/brand/favicon-white.png" class="sign-favicon-b ht-40" alt="logo">
											</a>
											<h1 class="main-logo1 ms-1 me-0 my-auto tx-28">Va<span>le</span>x</h1>
										</div>
										<div class="card-sigin">
											<div class="main-signup-header">
												<h2>Welcome back!</h2>
												<h5 class="fw-semibold mb-4">Please sign in to continue.</h5>
												<form id="loginform">
													<div class="form-group">
														<label>Email</label> <input class="form-control" id="loginmail" name="loginmail" placeholder="Enter your email" type="text">
													</div>
													<div class="form-group">
														<label>Password</label> <input class="form-control" id="loginpassword" name="loginpassword" placeholder="Enter your password" type="password">
													</div>
													<div id="messaggio" class="alert alert-danger text-center" style="display: none;" role="alert">Errore</div>
													<button type="submit" class="btn btn-main-primary btn-block">Entra</button>
												</form>
												<div class="main-signin-footer mt-5">
													<p><a href="forgot.html">Password dimenticata?</a></p>

												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div><!-- End -->
					</div>
				</div><!-- End -->
			</div>
		</div>

	</div>
	<!-- End Page -->
	<!-- JQuery min js -->
	<script src="assets/plugins/jquery/jquery.min.js"></script>

	<!-- Bootstrap Bundle js -->
	<script src="assets/plugins/bootstrap/js/popper.min.js"></script>
	<script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>

	<!-- Moment js -->
	<script src="assets/plugins/moment/moment.js"></script>

	<!-- P-scroll js -->
	<script src="assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>

	<!-- eva-icons js -->
	<script src="assets/js/eva-icons.min.js"></script>

	<!--themecolor js-->
	<script src="assets/js/themecolor.js"></script>

	<!-- custom js -->
	<script src="assets/js/custom.js"></script>

	<!-- switcher-styles js -->
	<script src="assets/js/swither-styles.js"></script>
	<script src="assets/js/jquery.validate.js"></script>
</body>

</html>