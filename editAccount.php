<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if(isset($_SESSION["loggedin"])) {
	$role = $_SESSION["role"];
	$userID = $_SESSION["id"];
} else {
	header('Location: login.php');
	die();
}

$accountInfo = getAccountInfo($conn, $userID);

$firstName = $accountInfo["firstName"];
$lastName = $accountInfo["lastName"];
$email = $accountInfo["email"];

require 'include/header.php';

?>

	<div class="container">
		<button class="btn btn-default" onclick="window.history.back();">
			<span class="glyphicon glyphicon-arrow-left"></span> Back to Account Page
		</button>
		<form class='form-horizontal'>
			<h2>Edit Account</h2>
			<div class='form-group row' id='firstName-group'>
				<div class='col-md-2 control-label'>
					<label>First Name:</label>
				</div>
				<div class='col-md-6'>
					<input class='form-control' value='<?php echo $firstName; ?>' name='firstName' />
				</div>
			</div>
			<div class='form-group row' id='lastName-group'>
				<div class='col-md-2 control-label'>
					<label>Last Name:</label>
				</div>
				<div class='col-md-6'>
					<input class='form-control' value='<?php echo $lastName; ?>' name='lastName' />
				</div>
			</div>
			<br>
			<h3>Change Password</h3>
			<div class='form-group row' id='oldPassword-group'>
				<div class='col-md-2 control-label'>
					<label>Old Password:</label>
				</div>
				<div class='col-md-6'>
					<input class='form-control' type='password' name='oldPassword' />
				</div>
			</div>
			<div class='form-group row' id='newPassword-group'>
				<div class='col-md-2 control-label'>
					<label>New Password:</label>
				</div>
				<div class='col-md-6'>
					<input class='form-control' type='password' name='newPassword' />
				</div>
			</div>
			<div class='form-group row' id='newPasswordConfirm-group'>
				<div class='col-md-2 control-label'>
					<label>Confirm New Password:</label>
				</div>
				<div class='col-md-6'>
					<input class='form-control' type='password' name='newPasswordConfirm' />
				</div>
			</div>
			<button type='submit' class='btn btn-primary saveChanges'>Save Changes</button>
		</form>
	</div><!-- /.container -->

	<!-- required JS -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<!-- custom scripts -->
	<script src="js/editAccount.js"></script>

	<?php
	$_SESSION['token'] = token();
	?>
</body>
</html>

<?php

function getAccountInfo($conn, $joinKey) {
	$arr = array();

	$stmt = $conn->prepare("SELECT firstName, lastName, email FROM members WHERE joinKey = ?");
	$stmt->bind_param("i", $joinKey);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($firstName, $lastName, $email);
	while($stmt->fetch()) {
		$stmt->close();
		$arr["firstName"] = e($firstName);
		$arr["lastName"] = e($lastName);
		$arr["email"] = e($email);
		return $arr;
	}
}

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
