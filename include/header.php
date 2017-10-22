<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Rocket Rosters</title>
	<link rel="icon" type="image/png" href="img/favicon.gif" />
	<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="css/style.css" rel="stylesheet" type="text/css">
	<link href="css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css">
</head>
<body>
	<!-- Navigation -->
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<a href="https://rocketrosters.com" class="navbar-brand"><img src="img/logo.gif" /></a>
				<button class="navbar-toggle" data-toggle="collapse" data-target=".navHeaderCollapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div><!-- /.navbar-header -->
			<div class="collapse navbar-collapse navHeaderCollapse">
				<ul class="nav navbar-nav navbar-right">
					<?php
					$loggedin = isset($_SESSION['loggedin']);
					if(!$loggedin) { ?>
						<li<?php echo navbarActive('register'); ?>><a href="register.php">Register</a></li>
						<?php
					}
					?>
					<li<?php echo navbarActive('account'); ?>><a href="account.php">Account</a></li>
					<?php
					if($loggedin) { ?>
						<li><a href='handlers/logout.php'>Logout</a></li>
						<?php
					} else {
						?>
						<li<?php echo navbarActive('login'); ?>><a href="login.php">Login</a></li>
						<?php
					} ?>
				</ul>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container -->
	</div><!-- /.navbar -->

	<?php

	function navbarActive($str) {
		$page = basename($_SERVER["SCRIPT_NAME"], '.php');
		if($str == $page) {
			return ' class="active"';
		}
		// special case for all other account pages
		if($str == 'account' && (strpos($page, 'manage') !== false || strpos($page, 'view') !== false)) {
			return ' class="active"';
		}
		return '';
	}
