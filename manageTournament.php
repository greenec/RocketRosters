<?php

require "include/db.php";
require "include/functions.php";

if(isset($_GET['tournamentID'])) {
	$tournamentID = $_GET["tournamentID"];
	$tournamentInfo = getTournamentInfo($conn, $tournamentID);
} else {
	header('Location: account.php');
	die();
}

if(isset($tournamentInfo)) {
	$tournamentKey = $tournamentInfo->tournamentKey;
	$tournamentName = $tournamentInfo->fullName();
} else {
	header('Location: account.php');
	die();
}

new Session($conn);

if(isset($_SESSION['loggedin'])) {
	$directorID = $_SESSION["id"];
} else {
	header('Location: account.php');
	die();
}

if(!directorOwnsTournament($conn, $directorID, $tournamentKey)) {
	header('Location: account.php');
	die();
}

require 'include/header.php';

?>

	<div class="container">
		<?php $teams = getTournamentTeams($conn, $tournamentKey); ?>
		<button class="btn btn-default" onclick="window.history.back();">
			<span class="glyphicon glyphicon-arrow-left"></span> Back to Account Page
		</button>
		<h2><?php echo $tournamentName; ?></h2>
		<hr>
		<div class='table-responsive'>
			<table id='teams' class='table table-bordered table-striped'>
				<thead>
					<tr>
						<th>Actions</th>
						<th>Team Name</th>
						<th>Join Date</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($teams as $team) {
						$viewURL = "viewTeam.php?teamID=$team->teamID&tournamentID=$tournamentID"; ?>
						<tr id='<?php echo $team->teamID; ?>'>
							<td class='text-nowrap'>
								<a href='<?php echo $viewURL; ?>' class='btn btn-primary btn-sm'><span class='glyphicon glyphicon-search'></span></a>
								<button class='btn btn-danger btn-sm removeTeam' id='<?php echo $team->teamID ?>'><span class='glyphicon glyphicon-trash'></span></button>
							</td>
							<td><?php echo $team->teamName; ?></td>
							<td><?php echo $team->date; ?></td>
						</tr>
						<?php
					} ?>
				</tbody>
			</table>
			<br>
		</div>
	</div><!-- /.container -->

	<!-- required JS -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<!-- custom scripts -->
	<script src="js/manageTournament.js"></script>
	<script src='js/dataTables.min.js'></script>
</body>
</html>
