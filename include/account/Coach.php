<?php
$teams = array_reverse(getTeamsForCoach($conn, $userID));
$tournaments = getTournamentsForCoach($conn, $userID); ?>
<div class="row">
	<div class='col-sm-6'>
		<h2>Create a Team</h2>
		<br>
		<form id="createTeam">
			<div class="form-group row" id="createTeam-group">
				<div class="col-sm-4">
					<label for="teamName" class="form-control-label">Team Name</label>
				</div>
				<div class="col-sm-8">
					<input type="text" class="form-control" name="teamName" placeholder="Team Name">
				</div>
			</div>
			<div class="form-group row">
				<div class="col-sm-offset-4 col-sm-8">
					<button type="submit" class="btn btn-default">Create Team</button>
				</div>
			</div>
		</form>
	</div>
	<div class='col-sm-6'>
		<h2>Join a Tournament</h2>
		<br>
		<form id="joinTournament">
			<div class="form-group row" id="team-group">
				<div class="col-sm-4">
					<label for="role" class="form-control-label">Team</label>
				</div>
				<div class="col-sm-8">
					<select class="form-control" name="team">
						<option selected value="defaultTeamName">Select a Team</option>
						<?php
						foreach($teams as $team) {
							echo "<option value='$team->teamID'>$team->teamName</option>";
						} ?>
					</select>
				</div>
			</div>
			<div class="form-group row" id="tournamentID-group">
				<div class="col-sm-4">
					<label for="tournamentID" class="form-control-label">Tournament Code</label>
				</div>
				<div class="col-sm-8">
					<input type="text" class="form-control" name="tournamentID" placeholder="Tournament Code">
				</div>
			</div>
			<div class="form-group row">
				<div class="col-sm-offset-4 col-sm-8">
					<button type="submit" class="btn btn-default">Join Tournament</button>
				</div>
			</div>
		</form>
	</div>
</div><!-- /.row -->
<hr>
<h2>My Teams</h2><br>
<div class='table-responsive'>
	<table id='teams' class='table table-striped table-bordered'>
		<thead>
			<tr>
				<th>Actions</th>
				<th>Team Name</th>
				<th>Creation Date</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($teams as $team) { ?>
				<tr id='<?php echo $team->teamID; ?>'>
					<td class='text-nowrap'>
						<a href='<?php echo "manageTeam.php?teamID=$team->teamID"; ?>' class='btn btn-primary btn-sm'><span class='glyphicon glyphicon-pencil'></span></a>
						<button class='btn btn-danger btn-sm deleteTeam' id='<?php echo $team->teamID; ?>'><span class='glyphicon glyphicon-trash'></span></button>
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
<hr>
<h2>My Tournaments</h2><br>
<div class='table-responsive'>
	<table id='tournaments' class='table table-striped table-bordered'>
		<thead>
			<tr>
				<th>Actions</th>
				<th>Tournament Name</th>
				<th>Team Name</th>
				<th>Join Date</th>
			</tr>
		</thead>
		<tbody>
			<?php
			for ($i = 0; $i < count($tournaments); $i += 2) {
				$tournament = $tournaments[$i];
				$team = $tournaments[$i + 1];
				$manageURL = "manageTournamentPlayers.php?teamID=$team->teamID&tournamentID=$tournament->tournamentID"; ?>
				<tr id='<?php echo "$tournament->tournamentID,$team->teamID"; ?>'>
					<td class='text-nowrap'>
						<a href='<?php echo $manageURL; ?>' class='btn btn-primary btn-sm'><span class='glyphicon glyphicon-pencil'></span></a>
						<button class='btn btn-danger btn-sm leaveTournament' id='<?php echo "$tournament->tournamentID,$team->teamID"; ?>'><span class='glyphicon glyphicon-trash'></span></button>
					</td>
					<td><?php echo "$tournament->tournamentName ($tournament->tournamentID)"; ?></td>
					<td><?php echo $team->teamName; ?></td>
					<td><?php echo $tournament->date; ?></td>
				</tr>
				<?php
			} ?>
		</tbody>
	</table>
	<br>
</div>

<?php

function getTeamsForCoach($conn, $coachKey) {
	$teams = array();

	$stmt = $conn->prepare("SELECT teamName, teamID, joinKey, creationDate FROM teams WHERE coachID = ?");
	$stmt->bind_param("i", $coachKey);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($teamName, $teamID, $joinKey, $creationDate);
	while($stmt->fetch()) {
		$team = new Team($teamName, $teamID, $joinKey, $creationDate);
		array_push($teams, $team);
	}
	$stmt->close();

	return $teams;
}

function getTournamentsForCoach($conn, $coachKey) {
	$tournaments = array();

	$stmt = $conn->prepare(
	"SELECT t.tournamentName, t.tournamentID, t.joinKey, tournamentTeams.joinDate, teams.teamName, teams.teamID
		FROM tournaments AS t
			JOIN tournamentTeams
				ON t.joinKey = tournamentTeams.tournamentID
			JOIN teams
				ON tournamentTeams.teamID = teams.joinKey
		WHERE teams.coachID = ?");
	$stmt->bind_param("i", $coachKey);
	$stmt->store_result();
	$stmt->bind_result($tournamentName, $tournamentID, $tournamentKey, $joinDate, $teamName, $teamID);
	$stmt->execute();
	while($stmt->fetch()) {
		$tournament = new Tournament($tournamentName, $tournamentID, $tournamentKey, $joinDate);
		$team = new Team($teamName, $teamID);
		array_push($tournaments, $tournament);
		array_push($tournaments, $team);
	}
	$stmt->close();

	return $tournaments;
}
