<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	header('Location: ../account.php');
	die();
}

require "../include/db.php";
require "../include/functions.php";

new Session($conn);

if(isset($_SESSION['loggedin']) && $_SESSION['role'] == 'Coach') {
	$coachID = $_SESSION['id'];
} else {
	die();
}

if(isset($_POST['tournamentID']) && isset($_POST['teamID']) && isset($_POST['playerID']) && isset($_POST['action'])) {
	$tournamentID = $_POST['tournamentID'];
	$teamID = $_POST['teamID'];
	$playerID = $_POST['playerID'];
	$action = $_POST['action'];

	$teamKey = getTeamJoinKey($conn, $teamID);
	$tournamentKey = getTournamentJoinKey($conn, $tournamentID);
	$playerKey = getPlayerJoinKey($conn, $playerID);
} else {
	die();
}

if(!coachOwnsTeam($conn, $coachID, $teamKey)) {
	die();
}
// initialize JSON variables
$errors = validate($conn, $action, $teamKey, $playerKey, $tournamentKey);
$data = array();

if(empty($errors)) {
	if($action == "add") {
		addPlayerToTournament($conn, $playerKey, $teamKey, $tournamentKey);
	}
	if($action == "remove") {
		removePlayerFromTournament($conn, $playerKey, $teamKey, $tournamentKey);
	}
}

if(empty($errors)) {
	$data["success"] = true;
} else {
	$data["success"] = false;
	$data["errors"] = $errors;
}

echo json_encode($data);

function validate($conn, $action, $teamKey, $playerKey, $tournamentKey) {
	$errors = array();
	if(!isset($teamKey)) {
		$errors["error"] = "Team does not exist.";
	}
	if(!isset($tournamentKey)) {
		$errors["error"] = "Tournament does not exist.";
	}
	if(!isset($playerKey)) {
		$errors["error"] = "Player does not exist.";
	}
	if(!playerOnTeam($conn, $teamKey, $playerKey)) {
		$errors["error"] = "Player is not on your team.";
	}
	if(!teamInTournament($conn, $tournamentKey, $teamKey)) {
		$errors["error"] = "This team is not in the tournament";
	}
	return $errors;
}

function addPlayerToTournament($conn, $playerKey, $teamKey, $tournamentKey) {
	$stmt = $conn->prepare("INSERT INTO tournamentPlayers (playerID, teamID, tournamentID) VALUES (?, ?, ?)");
	$stmt->bind_param("iii", $playerKey, $teamKey, $tournamentKey);
	$stmt->execute();
}

function removePlayerFromTournament($conn, $playerKey, $teamKey, $tournamentKey) {
	$stmt = $conn->prepare("DELETE FROM tournamentPlayers WHERE playerID = ? AND teamID = ? AND tournamentID = ?");
	$stmt->bind_param("iii", $playerKey, $teamKey, $tournamentKey);
	$stmt->execute();
}
