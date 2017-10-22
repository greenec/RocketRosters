<?php
$tournaments = array_reverse(getTournamentsForDirector($conn, $userID)); ?>
<h2>Create a Tournament</h2>
<br>
<form id="createTournament">
	<div class="form-group row" id="tournamentName-group">
		<div class="col-sm-2">
			<label for="teamName" class="form-control-label">Tournament Name</label>
		</div>
		<div class="col-sm-6">
			<input type="text" class="form-control" name="tournamentName" placeholder="Tournament Name">
		</div>
	</div>
	<div class="form-group row">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-default">Create Tournament</button>
		</div>
	</div>
</form>
<hr>
<h2>Your Tournaments</h2>
<br>
<div class='table-responsive'>
	<table id='tournaments' class='table table-bordered table-striped'>
		<thead>
			<tr>
				<th>Actions</th>
				<th>Tournament Name</th>
				<th>Tournament Code</th>
				<th>Creation Date</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($tournaments as $tournament) {
				$manageURL = "manageTournament.php?tournamentID=$tournament->tournamentID"; ?>
				<tr id='<?php echo $tournament->tournamentID; ?>'>
					<td class='text-nowrap'>
						<a href='<?php echo $manageURL; ?>' class='btn btn-primary btn-sm'><span class='glyphicon glyphicon-pencil'></span></a>
						<button class='deleteTournament btn btn-danger btn-sm' id='<?php echo $tournament->tournamentID; ?>'><span class='glyphicon glyphicon-trash'></span></button>
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

function getTournamentsForDirector($conn, $directorkey) {
	$tournaments = array();

	$stmt = $conn->prepare("SELECT tournamentName, tournamentID, joinKey, creationDate FROM tournaments WHERE directorID = ?");
	$stmt->bind_param("i", $directorkey);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($tournamentName, $tournamentID, $tournamentKey, $creationDate);
	while($stmt->fetch()) {
		$tournament = new Tournament($tournamentName, $tournamentID, $tournamentKey, $creationDate);
		array_push($tournaments, $tournament);
	}
	$stmt->close();

	return $tournaments;
}
