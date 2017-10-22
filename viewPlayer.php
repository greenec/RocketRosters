<?php

parse_str($_SERVER['QUERY_STRING'], $qString);
if(isset($qString['teamID']) && isset($qString['playerID'])) {
	$url = 'viewPlayer.php?teamID=' . $qString['teamID'] . '&playerID=' . $qString['playerID'];
} else {
	$url = '';
}

require 'include/cachestart.php';

require "include/db.php";
require "include/functions.php";

if(isset($_GET['teamID']) && isset($_GET['playerID'])) {
	$teamID = $_GET['teamID'];
	$playerID = $_GET['playerID'];

	$teamKey = getTeamJoinKey($conn, $teamID);
	$player = getDetailedPlayerInfo($conn, $playerID, $teamKey);
} else {
	header('Location: account.php');
	die();
}

require 'include/header.php';

?>

	<div class="container">
		<?php
		if(isset($player)) {
			$teams = $player->teams($conn); ?>
			<button class="btn btn-default" onclick="window.history.back();">
				<span class="glyphicon glyphicon-arrow-left"></span> Back to Team Page
			</button>
			<h2><?php echo $player->fullName(); ?></h2>
			<hr>

			<strong>Graduating Year: </strong><?php echo $player->graduating; ?><br>
			<strong>Date of Birth: </strong><?php echo $player->dob; ?><br>
			<strong>Age: </strong><?php echo $player->age(); ?><br>
			<br>
			<strong>Player Email: </strong><?php echo $player->playerEmail; ?><br>
			<strong>Parent Email: </strong><?php echo $player->parentEmail; ?><br>
			<strong>Phone: </strong><?php echo $player->phone; ?><br>
			<strong>Address: </strong><?php echo $player->address(); ?><br>
			<br>
			<strong>Jersey: </strong><?php echo $player->jersey; ?><br>
			<strong>Position: </strong><?php echo $player->position; ?><br>

			<hr>
			<h2>Teams</h2>
			<br>
			<table id='teams' class='table table-striped table-bordered'>
				<thead>
					<tr>
						<th>Team Name</th>
						<th>Team ID</th>
					</tr>
				</thead>
				<?php
				foreach ($teams as $team) { ?>
					<tr>
						<td><?php echo $team->teamName; ?></td>
						<td><?php echo $team->teamID; ?></td>
					</tr>
					<?php
				} ?>
			</table>
			<?php
		} else {
			$cache = false; ?>
			<h4>Player not found.</h4>
			<?php
		} ?>
	</div><!-- /.container -->

	<!-- required JS -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<!-- custom scripts -->
	<script src="js/dataTables.min.js"></script>
	<script>
	$(document).ready(function() {
		$('#teams').dataTable();
	});
	</script>

</body>
</html>

<?php

require 'include/cacheend.php';
