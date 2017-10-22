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
		<?php if(isset($_GET['c'])) echo "<p class=text-success><strong>Account successfully created!</strong></p>"; ?>
		<h2>Account Info</h2>
		<p>Role: <?php echo $role; ?></p>
		<p>Name: <?php echo "$firstName $lastName"; ?></p>
		<p>Email: <?php echo $email; ?></p>
		<p><a href="editAccount.php" class='btn btn-default'><span class='glyphicon glyphicon-cog'></span> Edit Account</a></p>
		<hr>

		<?php
		require 'include/account/' . $_SESSION['role'] . '.php';
		?>

	</div><!-- /.container -->

	<!-- required JS -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<!-- custom scripts -->
	<script src="js/account-<?php echo $_SESSION['role']; ?>.js"></script>
	<script src="js/dataTables.min.js"></script>

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
