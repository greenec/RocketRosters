<?php require 'include/header.php'; ?>

	<!-- Form -->
	<div class="container">
		<h1>Login</h1><br>
		<form>
			<div class="form-group row" id="email-group">
				<div class="col-sm-2">
					<label for="email" class="form-control-label">Email</label>
				</div>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="email" placeholder="Email">
				</div>
			</div>
			<div class="form-group row" id="password-group">
				<div class="col-sm-2">
					<label for="password" class="form-control-label">Password</label>
				</div>
				<div class="col-sm-6">
					<input type="password" class="form-control" name="password" placeholder="Password">
				</div>
			</div>
			<div class="form-group row">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-default login">Login</button>
				</div>
			</div>
		</form>
		<p>Don't have an account? <a href="register.php">Sign Up!</a></p>
	</div><!-- /.container -->

	<!-- required JS -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<!-- custom scripts -->
	<script src="js/login.js"></script>
</body>
</html>
