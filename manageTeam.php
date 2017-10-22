<?php

require "include/db.php";
require "include/functions.php";

if(isset($_GET['teamID'])) {
	$teamID = $_GET['teamID'];
	$teamInfo = getTeamInfo($conn, $teamID);
} else {
	header('Location: account.php');
	die();
}

if(isset($teamInfo)) { // invalid teamID
	$teamKey = $teamInfo->teamKey;
	$teamName = $teamInfo->teamName;
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
		<?php $players = getTeamPlayers($conn, $teamKey); ?>
		<button class="btn btn-default" onclick="window.history.back();">
			<span class="glyphicon glyphicon-arrow-left"></span> Back to Account Page
		</button>
		<h1><?php echo $teamName; ?></h1>
		<hr>
		<button class="btn btn-primary drawAddPlayer">Add Player</button>
		<div id="addPlayerForm">
			<br>
			<form>
				<div class="form-group row" id="firstName-group">
					<div class="col-sm-2">
						<label for="firstName" class="form-control-label">First Name</label>
					</div>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="firstName" placeholder="First Name">
					</div>
				</div>

				<div class="form-group row" id="lastName-group">
					<div class="col-sm-2">
						<label for="lastName" class="form-control-label">Last Name</label>
					</div>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="lastName" placeholder="Last Name">
					</div>
				</div>

				<div class="form-group row" id="dob-group">
					<div class="col-sm-2">
						<label class="form-control-label">Date of Birth</label>
					</div>
					<div class="col-sm-2">
						<select class="form-control pad-input" name="dobM">
							<option selected value="default">Month</option>
							<option value="01">January</option>
							<option value="02">February</option>
							<option value="03">March</option>
							<option value="04">April</option>
							<option value="05">May</option>
							<option value="06">June</option>
							<option value="07">July</option>
							<option value="08">August</option>
							<option value="09">September</option>
							<option value="10">October</option>
							<option value="11">November</option>
							<option value="12">December</option>
						</select>
					</div>
					<div class="col-sm-2">
						<input type="number" class="form-control pad-input" name="dobD" placeholder="Day">
					</div>
					<div class="col-sm-2">
						<input type="number" class="form-control" name="dobY" placeholder="Year">
					</div>
				</div>

				<div class="form-group row" id="jerseyPositionGraduating-group">
					<div class="col-sm-2">
						<label class="form-control-label">Jersey / Position / Graduating Year</label>
					</div>
					<div class="col-sm-2">
						<input type="number" class="form-control pad-input" name="jersey" placeholder="Jersey">
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control pad-input" name="position" placeholder="Position">
					</div>
					<div class="col-sm-2">
						<input type="number" class="form-control" name="graduating" placeholder="Graduating Year">
					</div>
				</div>

				<div class="form-group row" id="parentEmail-group">
					<div class="col-sm-2">
						<label class="form-control-label">Parent's Email</label>
					</div>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="parentEmail" placeholder="Parent's Email">
					</div>
				</div>

				<div class="form-group row" id="playerEmail-group">
					<div class="col-sm-2">
						<label class="form-control-label">Player's Email</label>
					</div>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="playerEmail" placeholder="Player's Email">
					</div>
				</div>

				<div class="form-group row" id="phone-group">
					<div class="col-sm-2">
						<label class="form-control-label">Phone</label>
					</div>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="phone" placeholder="Phone">
					</div>
				</div>

				<div class="form-group row" id="address-group">
					<div class="col-sm-2">
						<label class="form-control-label">Address</label>
					</div>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="address" placeholder="Address">
					</div>
				</div>

				<div class="form-group row" id="cityStateZip-group">
					<div class="col-sm-2">
						<label class="form-control-label">City / State / Zip</label>
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control pad-input" name="city" placeholder="City">
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control pad-input" name="state" placeholder="State">
					</div>
					<div class="col-sm-2">
						<input type="number" class="form-control" name="zip" placeholder="Zip">
					</div>
				</div>

				<div class="form-group row">
					<div class="col-sm-offset-2 col-sm-10">
						<button class="btn btn-default addPlayer">Add Player</button>
					</div>
				</div>
			</form>
			<button class="btn btn-danger drawAddPlayer">Close</button>
		</div>

		<hr>

		<div class='table-responsive'>
			<table id='players' class='table table-striped table-bordered'>
				<thead>
					<tr>
						<th></th>
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
							<td></td>
							<td><a href='<?php echo $url; ?>' class='btn btn-sm btn-primary'><span class='glyphicon glyphicon-search'></span></a></td>
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
			<br>
		</div>

		<button class='btn btn-danger removePlayer'>Delete</button>

	</div><!-- /.container -->

	<!-- required JS -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<!-- custom scripts -->
	<script src="js/manageTeam.js"></script>
	<script src="js/dataTables.min.js"></script>

</body>
</html>
