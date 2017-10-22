<?php

parse_str($_SERVER['QUERY_STRING'], $qString);
if(isset($qString['teamID']) && isset($qString['tournamentID'])) {
	$url = 'viewTeam.php?teamID=' . $qString['teamID'] . '&tournamentID=' . $qString['tournamentID'];
} else {
	$url = '';
}

require 'include/cachestart.php';

require "include/db.php";
require "include/functions.php";

if(isset($_GET['tournamentID']) && isset($_GET['teamID'])) {
	$tournamentID = $_GET['tournamentID'];
	$teamID = $_GET['teamID'];

	$tournamentKey = getTournamentJoinKey($conn, $tournamentID);
	$teamInfo = getTeamInfo($conn, $teamID);
} else {
	header('Location: account.php');
	die();
}

if(isset($teamInfo)) {
	$teamKey = $teamInfo->teamKey;
	$teamName = $teamInfo->teamName;
}

require 'include/header.php';

?>

	<div class="container">
		<?php
		if(isset($tournamentKey) && isset($teamKey)) {
			$players = getTeamPlayersInTournament($conn, $tournamentKey, $teamKey);

			if(isset($players)) { ?>
				<button class="btn btn-default" onclick="window.history.back();">
					<span class="glyphicon glyphicon-arrow-left"></span> Back to Tournament Page
				</button>
				<h1><?php echo $teamName ?></h1>
				<hr>
				<div class='table-responsive'>
					<table id='players' class='table table-striped table-bordered'>
						<thead>
							<tr>
								<th>Action</th>
								<th>Last Name</th>
								<th>First Name</th>
								<th>Jersey</th>
								<th>Position</th>
								<th>Graduating</th>
								<th>Date of Birth</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($players as $player) {
								$url = "viewPlayer.php?teamID=$teamID&playerID=$player->playerID"; ?>
								<tr id='<?php echo $player->playerID; ?>'>
									<td><a href='<?php echo $url; ?>' class='btn btn-primary btn-sm'><span class='glyphicon glyphicon-search'></span></a></td>
									<td><?php echo $player->lastName; ?></td>
									<td><?php echo $player->firstName; ?></td>
									<td><?php echo $player->jersey; ?></td>
									<td><?php echo $player->position; ?></td>
									<td><?php echo $player->graduating; ?></td>
									<td><?php echo $player->dob; ?></td>
								</tr>
								<?php
							} ?>
						</tbody>
					</table>
				</div>
				<?php
			} else { ?>
				<h4>No players found on this team in this tournament.</h4>
				<?php
			}
		} else {
			$cache = false; ?>
			<h4>Team or tournament not found.</h4>
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
		$('#players').DataTable();
	});
	</script>

</body>
</html>

<?php

function getTeamPlayersInTournament($conn, $tournamentKey, $teamKey) {
	$players = array();
	$stmt = $conn->prepare(
		"SELECT DISTINCT p.firstName, p.lastName, p.playerID, p.joinKey, p.dob, tp.jersey, tp.position, tp.graduating
			FROM players AS p
				JOIN tournamentPlayers
					ON p.joinKey = tournamentPlayers.playerID
				JOIN teamPlayers AS tp
					ON p.joinKey = tp.playerID
			WHERE tournamentPlayers.tournamentID = ? AND tournamentPlayers.teamID = ?"
	);
	$stmt->bind_param("ii", $tournamentKey, $teamKey);
	$stmt->store_result();
	$stmt->bind_result($firstName, $lastName, $playerID, $playerKey, $dob, $jersey, $position, $graduating);
	$stmt->execute();
	while($stmt->fetch()) {
		$firstName = $firstName;
		$lastName = $lastName;
		$playerInfo = array(
			'dob' => date_format(date_create($dob), 'm/d/Y'),
			'jersey' => $jersey,
			'position' => $position,
			'graduating' => $graduating
		);
		$player = new Player($firstName, $lastName, $playerID, $playerKey, $playerInfo);
		array_push($players, $player);
	}
	$stmt->close();

	return $players;
}

require 'include/cacheend.php';
