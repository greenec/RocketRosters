<?php require 'include/header.php'; ?>

	<!-- Form -->
	<div class="container">
		<h1>Register</h1><br>
		<form>
			<div class="form-group row" id="role-group">
				<div class="col-sm-2">
					<label for="role" class="form-control-label">Role</label>
				</div>
				<div class="col-sm-6">
					<select class="form-control" name="role">
						<option selected value="default">Select a Role</option>
						<option value="Coach">Coach</option>
						<option value="Director">Tournament Director</option>
						<option value="Recruiter">Recruiter</option>
					</select>
				</div>
			</div>
			<div class="form-group row" id="firstName-group">
				<div class="col-sm-2">
					<label for="firstName" class="form-control-label">First Name</label>
				</div>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="firstName" placeholder="First Name">
				</div>
			</div>
			<div class="form-group row" id="lastName-group">
				<div class="col-sm-2">
					<label for="lastName" class="form-control-label">Last Name</label>
				</div>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="lastName" placeholder="Last Name">
				</div>
			</div>
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
			<div class="form-group row" id="password-confirm-group">
				<div class="col-sm-2">
					<label for="passwordConfirm" class="form-control-label">Confirm Password</label>
				</div>
				<div class="col-sm-6">
					<input type="password" class="form-control" name="passwordConfirm" placeholder="Confirm Password">
				</div>
			</div>
			<div class="form-group row">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-default">Sign Up</button>
				</div>
			</div>
		</form>
	</div><!-- /.container -->

	<!-- required JS -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<!-- custom scripts -->
	<script src="js/register.js"></script>
</body>
</html>
