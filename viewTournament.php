<?php

parse_str($_SERVER['QUERY_STRING'], $qString);
if(isset($qString['tournamentID'])) {
	$url = 'viewTournament.php?tournamentID=' . $qString['tournamentID'];
} else {
	$url = '';
}

require 'include/cachestart.php';

require "include/db.php";
require "include/functions.php";

if(isset($_GET['tournamentID'])) {
	$tournamentID = $_GET['tournamentID'];
	$tournamentInfo = getTournamentInfo($conn, $tournamentID);
} else {
	header('Location: account.php');
	die();
}

if(isset($tournamentInfo)) {
	$tournamentKey = $tournamentInfo->tournamentKey;
	$tournamentName = $tournamentInfo->tournamentName;
}

require 'include/header.php';

?>

	<div class="container">
		<?php
		if(isset($tournamentInfo)) {
			$teams = getTournamentTeams($conn, $tournamentKey);

			if(!empty($teams)) { ?>
				<button class="btn btn-default" onclick="window.history.back();">
					<span class="glyphicon glyphicon-arrow-left"></span> Back to Account Page
				</button>
				<h1><?php echo $tournamentName; ?></h1>
				<hr>

				<div class='table-responsive'>
					<table id='teams' class='table table-striped table-bordered'>
						<thead>
							<tr>
								<th>Action</th>
								<th>Team Name</th>
								<th>Team ID</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($teams as $team) {
								$viewURL = "viewTeam.php?teamID=$team->teamID&tournamentID=$tournamentID"; ?>
								<tr>
									<td>
										<a href='<?php echo $viewURL; ?>' class='btn btn-primary btn-sm'>
											<span class='glyphicon glyphicon-search'></span>
										</a>
									</td>
									<td><?php echo $team->teamName; ?></td>
									<td><?php echo $team->teamID; ?></td>
								</tr>
								<?php
							} ?>
						</tbody>
					</table>
				</div>
				<?php
			} else { ?>
				<h4>No teams have joined this tournament yet.</h4>
				<?php
			}
		} else {
			$cache = false; ?>
			<h4>Tournament not found.</h4>
			<?php
		} ?>
	</div><!-- /.container -->

	<!-- required JS -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<!-- custom scripts -->
	<script src="js/dataTables.min.js"></script>
	<script>
	var teams = $('#teams').DataTable();
	</script>
</body>
</html>

<?php

require 'include/cacheend.php';
