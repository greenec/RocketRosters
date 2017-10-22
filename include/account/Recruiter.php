<?php
$tournaments = array_reverse(getTournamentsForRecruiter($conn, $userID)); ?>
<h2>Get Rosters</h2>
<br>
<form id="getRosters">
	<div class="form-group row" id="getRosters-group">
		<div class="col-sm-2">
			<label for="tournamentID" class="form-control-label">Tournament Code</label>
		</div>
		<div class="col-sm-4">
			<input type="text" class="form-control" name="tournamentID" placeholder="Tournament Code">
		</div>
	</div>
	<div class="form-group row">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-default">Get Rosters</button>
		</div>
	</div>
</form>
<hr>
<h2>My Tournaments</h2>
<br>
<div class='table-responsive'>
	<table id='tournaments' class='table table-striped table-bordered'>
		<thead>
			<tr>
				<th>Actions</th>
				<th>Tournament Name</th>
				<th>Tournament ID</th>
				<th>Join Date</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($tournaments as $tournament) {
				$viewURL = "viewTournament.php?tournamentID=$tournament->tournamentID"; ?>
				<tr id='<?php echo $tournament->tournamentID; ?>'>
					<td class='text-nowrap'>
						<a href='<?php echo $viewURL; ?>' class='btn btn-primary btn-sm'><span class='glyphicon glyphicon-search'></span></a>
						<button class='removeRecruiter btn btn-danger btn-sm' id='<?php echo $tournament->tournamentID; ?>'><span class='glyphicon glyphicon-trash'></span></button>
					</td>
					<td><?php echo $tournament->tournamentName; ?></td>
					<td><?php echo $tournament->tournamentID; ?></td>
					<td><?php echo $tournament->date; ?></td>
				</tr>
				<?php
			} ?>
		</tbody>
	</table>
	<br>
</div>

<?php

function getTournamentsForRecruiter($conn, $recruiterKey) {
	$tournaments = array();

	$stmt = $conn->prepare(
	"SELECT t.tournamentName, t.tournamentID, t.joinKey, recruiters.joinDate
		FROM tournaments AS t
			JOIN recruiters
				ON recruiters.tournamentID = t.joinKey
		WHERE recruiters.recruiterID = ?");
	$stmt->bind_param("i", $recruiterKey);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($tournamentName, $tournamentID, $tournamentKey, $joinDate);
	while($stmt->fetch()) {
		$tournament = new Tournament($tournamentName, $tournamentID, $tournamentKey, $joinDate);
		array_push($tournaments, $tournament);
	}
	$stmt->close();

	return $tournaments;
}
