<?php

require "include/db.php";
require "include/functions.php";

if(isset($_GET['teamID']) && isset($_GET['tournamentID'])) {
	$teamID = $_GET["teamID"];
	$tournamentID = $_GET["tournamentID"];

	$teamInfo = getTeamInfo($conn, $teamID);
	$tournamentInfo = getTournamentInfo($conn, $tournamentID);
} else {
	header('Location: account.php');
	die();
}

if(isset($teamInfo) && isset($tournamentInfo)) {
	$teamKey = $teamInfo->teamKey;
	$teamName = $teamInfo->teamName;

	$tournamentKey = $tournamentInfo->tournamentKey;
	$tournamentName = $tournamentInfo->fullName();
} else {
	header('Location: account.php');
	die();
}

new Session($conn);

if(isset($_SESSION['loggedin'])) {
	$coachID = $_SESSION["id"];
} else {
	header('Location: account.php');
	die();
}

if(!coachOwnsTeam($conn, $coachID, $teamKey)) {
	header('Location: account.php');
	die();
}

require 'include/header.php';

?>

	<div class="container">
		<?php
		$teamPlayers = getTeamPlayers($conn, $teamKey);
		$tournamentPlayers = getTournamentPlayers($conn, $teamKey, $tournamentKey); ?>
		<button class="btn btn-default" onclick="window.history.back();">
			<span class="glyphicon glyphicon-arrow-left"></span> Back to Account Page
		</button>
		<h2><?php echo $tournamentName; ?></h2>
		<hr>
		<h2><?php echo $teamName; ?></h2>
		<br>
		<div class='table-responsive'>
			<table id='tournamentPlayers' class='table table-bordered'>
				<thead>
					<tr>
						<th>Name</th>
						<th>Position</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($teamPlayers as $player) {
						$joined = in_array($player->playerID, $tournamentPlayers);
						if($joined) {
							$rowColor = 'success';
							$action = 'removePlayer';
							$actionText = 'Remove';
						} else {
							$rowColor = 'danger';
							$action = 'addPlayer';
							$actionText = 'Add';
						} ?>
						<tr id='<?php echo $player->playerID; ?>' class='<?php echo $rowColor; ?>'>
							<td><?php echo $player->fullName(); ?></td>
							<td><?php echo $player->position; ?></td>
							<td>
								<button class='btn btn-default btn-sm <?php echo $action; ?>' id='<?php echo $player->playerID; ?>'><?php echo $actionText; ?></button>
							</td>
						</tr>
						<?php
					} ?>
				</tbody>
			</table>
		</div>
	</div><!-- /.container -->

	<!-- required JS -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<!-- custom scripts -->
	<script src="js/manageTournamentPlayers.js"></script>
	<script src="js/dataTables.min.js"></script>
</body>
</html>

<?php

function getTournamentPlayers($conn, $teamKey, $tournamentKey) {
	$players = array();

	$stmt = $conn->prepare(
		"SELECT players.playerID
			FROM players
				JOIN tournamentPlayers
					ON players.joinKey = tournamentPlayers.playerID
			WHERE tournamentPlayers.tournamentID = ? AND tournamentPlayers.teamID = ?"
	);
	$stmt->bind_param("ii", $tournamentKey, $teamKey);
	$stmt->store_result();
	$stmt->bind_result($playerID);
	$stmt->execute();
	while($stmt->fetch()) {
		array_push($players, $playerID);
	}
	$stmt->close();

	return $players;
}
