<?php

$maintainance = false;

if($maintainance) {
	die("This site is currently down for maintainance.");
}

// new functions to minimize db interactions
function getPlayerInfo($conn, $playerID) {
	$stmt = $conn->prepare("SELECT joinKey, firstName, lastName FROM players WHERE playerID = ?");
	$stmt->bind_param("s", $playerID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($playerKey, $firstName, $lastName);
	while($stmt->fetch()) {
		$stmt->close();
		$team = new Player($firstName, $lastName, $playerID, $playerKey);
		return $team;
	}
}
function getDetailedPlayerInfo($conn, $playerID, $teamKey) {
	$stmt = $conn->prepare(
		"SELECT p.firstName, p.lastName, p.joinKey, p.dob, tp.parentEmail, tp.playerEmail, tp.phone, tp.jersey, tp.position, tp.address, tp.city, tp.state, tp.zip, tp.graduating
			FROM players AS p
				JOIN teamPlayers AS tp
					ON p.joinKey = tp.playerID
			WHERE p.playerID = ? AND tp.teamID = ?"
	);
	$stmt->bind_param("si", $playerID, $teamKey);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($firstName, $lastName, $playerKey, $dob, $parentEmail, $playerEmail, $phone, $jersey, $position, $address, $city, $state, $zip, $graduating);
	while($stmt->fetch()) {
		$stmt->close();
		$firstName = $firstName;
		$lastName = $lastName;
		$playerInfo = array(
			'dob' => date_format(date_create($dob), 'm/d/Y'),
			'parentEmail' => $parentEmail,
			'playerEmail' => $playerEmail,
			'phone' => $phone,
			'jersey' => $jersey,
			'position' => $position,
			'address' => $address,
			'city' => $city,
			'state' => $state,
			'zip' => $zip,
			'graduating' => $graduating
		);
		$player = new Player($firstName, $lastName, $playerID, $playerKey, $playerInfo);
		return $player;
	}
}
function getTeamInfo($conn, $teamID) {
	$stmt = $conn->prepare("SELECT joinKey, teamName FROM teams WHERE teamID = ?");
	$stmt->bind_param("s", $teamID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($teamKey, $teamName);
	while($stmt->fetch()) {
		$stmt->close();
		$team = new Team($teamName, $teamID, $teamKey);
		return $team;
	}
}
function getTournamentInfo($conn, $tournamentID) {
	$stmt = $conn->prepare("SELECT joinKey, tournamentName FROM tournaments WHERE tournamentID = ?");
	$stmt->bind_param("s", $tournamentID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($tournamentKey, $tournamentName);
	while($stmt->fetch()) {
		$stmt->close();
		$team = new Tournament($tournamentName, $tournamentID, $tournamentKey);
		return $team;
	}
}

// get join keys
function getTeamJoinKey($conn, $teamID) {
	$stmt = $conn->prepare("SELECT joinKey FROM teams WHERE teamID = ?");
	$stmt->bind_param("s", $teamID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($teamKey);
	while($stmt->fetch()) {
		$stmt->close();
		return $teamKey;
	}
}
function getPlayerJoinKey($conn, $playerID) {
	$stmt = $conn->prepare("SELECT joinKey FROM players WHERE playerID = ?");
	$stmt->bind_param("s", $playerID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($joinKey);
	while($stmt->fetch()) {
		$stmt->close();
		return $joinKey;
	}
}
function getTournamentJoinKey($conn, $tournamentID) {
	$stmt = $conn->prepare("SELECT joinKey FROM tournaments WHERE tournamentID = ?");
	$stmt->bind_param("s", $tournamentID);
	$stmt->store_result();
	$stmt->bind_result($joinKey);
	$stmt->execute();
	while($stmt->fetch()) {
		$stmt->close();
		return $joinKey;
	}
}
function getPlayerJoinKeyArray($conn, $playerIDs) {
	$playerKeys = array();
	foreach($playerIDs as $playerID) {
		array_push($playerKeys, getPlayerJoinKey($conn, $playerID));
	}
	return $playerKeys;
}

// check ownership
function coachOwnsTeam($conn, $coachKey, $teamKey) {
	$stmt = $conn->prepare("SELECT joinKey FROM teams WHERE joinKey = ? AND coachID = ?");
	$stmt->bind_param("ii", $teamKey, $coachKey);
	$stmt->execute();
	$stmt->store_result();
	$num_of_rows = $stmt->num_rows;
	$stmt->close();
	return $num_of_rows != 0;
}
function directorOwnsTournament($conn, $directorKey, $tournamentKey) {
	$stmt = $conn->prepare("SELECT joinKey FROM tournaments WHERE joinKey = ? AND directorID = ?");
	$stmt->bind_param("ii", $tournamentKey, $directorKey);
	$stmt->execute();
	$stmt->store_result();
	$num_of_rows = $stmt->num_rows;
	$stmt->close();
	return $num_of_rows != 0;
}

// check intermediate tables
function playerOnTeam($conn, $teamKey, $playerKey) {
	$stmt = $conn->prepare("SELECT playerID FROM teamPlayers WHERE teamID = ? AND playerID = ?");
	$stmt->bind_param("ii", $teamKey, $playerKey);
	$stmt->execute();
	$stmt->store_result();
	$num_of_rows = $stmt->num_rows;
	$stmt->close();
	return $num_of_rows != 0;
}
function teamInTournament($conn, $tournamentKey, $teamKey) {
	$stmt = $conn->prepare("SELECT teamID FROM tournamentTeams WHERE tournamentID = ? AND teamID = ?");
	$stmt->bind_param("ii", $tournamentKey, $teamKey);
	$stmt->execute();
	$stmt->store_result();
	$num_of_rows = $stmt->num_rows;
	$stmt->close();
	return $num_of_rows != 0;
}

// use intermediate tables for joins
function getTeamPlayers($conn, $teamKey) {
	$players = array();

	$stmt = $conn->prepare(
		"SELECT p.firstName, p.lastName, p.playerID, p.joinKey, p.dob, tp.parentEmail, tp.playerEmail, tp.phone, tp.jersey, tp.position, tp.address, tp.city, tp.state, tp.zip, tp.graduating
			FROM players AS p
				JOIN teamPlayers AS tp
					ON p.joinKey = tp.playerID
			WHERE tp.teamID = ?"
	);
	$stmt->bind_param("i", $teamKey);
	$stmt->store_result();
	$stmt->bind_result($firstName, $lastName, $playerID, $playerKey, $dob, $parentEmail, $playerEmail, $phone, $jersey, $position, $address, $city, $state, $zip, $graduating);
	$stmt->execute();
	while($stmt->fetch()) {
		$firstName = $firstName;
		$lastName = $lastName;
		$playerInfo = array(
			'dob' => date_format(date_create($dob), 'm/d/Y'),
			'parentEmail' => $parentEmail,
			'playerEmail' => $playerEmail,
			'phone' => $phone,
			'jersey' => $jersey,
			'position' => $position,
			'address' => $address,
			'city' => $city,
			'state' => $state,
			'zip' => $zip,
			'graduating' => $graduating
		);
		$player = new Player($firstName, $lastName, $playerID, $playerKey, $playerInfo);
		array_push($players, $player);
	}
	$stmt->close();

	return $players;
}
function getTournamentTeams($conn, $tournamentKey) {
	$players = array();

	$stmt = $conn->prepare(
		"SELECT t.teamName, t.teamID, t.joinKey, tournamentTeams.joinDate
			FROM teams AS t
				JOIN tournamentTeams
					ON t.joinKey = tournamentTeams.teamID
			WHERE tournamentTeams.tournamentID = ?"
	);
	$stmt->bind_param("i", $tournamentKey);
	$stmt->store_result();
	$stmt->bind_result($teamName, $teamID, $teamKey, $date);
	$stmt->execute();
	while($stmt->fetch()) {
		$team = new Team($teamName, $teamID, $teamKey, $date);
		array_push($players, $team);
	}
	$stmt->close();

	return $players;
}

// security
function token() {
	$token = bin2hex(openssl_random_pseudo_bytes(16));
	echo "<script>var token = '$token';</script>\n";
	return $token;
}
function e($str) {
	return htmlspecialchars($str);
}

// class autoloader
spl_autoload_register(function($class) {
	include "classes/$class.php";
});

// player info functions
function calc_age($dob) {
	return date_create()->diff(date_create($dob))->y;
}

// codes table functions
function getCode($conn, $length) {
	$stmt = $conn->prepare('INSERT INTO codes (code) VALUES (?)');
	$stmt->bind_param('s', $code);
	while(true) {
		$characters = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
		$code = "";
		for($i = 0; $i < $length; $i++) {
			$code .= $characters[rand(0, strlen($characters) - 1)];
		}
		if($stmt->execute()) {
			return $code;
		}
	}
}
function removeCode($conn, $code) {
	$stmt = $conn->prepare("DELETE FROM codes WHERE code = ?");
	$stmt->bind_param("s", $code);
	$stmt->execute();
	$stmt->close();
}
