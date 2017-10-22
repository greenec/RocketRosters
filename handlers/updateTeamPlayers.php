<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	header('Location: ../account.php');
	die();
}

require "../include/db.php";
require "../include/functions.php";

new Session($conn);

if(isset($_SESSION['loggedin']) && $_SESSION["role"] == "Coach") {
	$coachID = $_SESSION['id'];
} else {
	die();
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

if($action == 'remove') {
	$playerIDs = isset($_POST['playerIDs']) ? $_POST['playerIDs'] : '';

	if(isset($_POST['teamID'])) {
		$teamID = $_POST['teamID'];
		$teamKey = getTeamJoinKey($conn, $teamID);
	}
} else {
	die();
}

// initialize JSON variables
$errors = validate($conn, $teamKey, $playerIDs, $coachID, $action);
$data = array();

if(empty($errors)) {
	removePlayersFromTeam($conn, $teamKey, $playerIDs);
}

if(!empty($errors)) {
	$data["success"] = false;
	$data["errors"] = $errors;
} else {
	$data["success"] = true;
}

echo json_encode($data);

function validate($conn, $teamKey, $playerIDs, $coachKey, $action) {
	$errors = array();
	if($action == 'remove') {
		if(empty($teamKey)) {
			$errors["teamID"] = "Team does not exist.";
		}
		if(!coachOwnsTeam($conn, $coachKey, $teamKey)) {
			$errors["error"] = "You do not own this team.";
		}
		if(empty($playerIDs)) {
			$errors["error"] = "No player(s) selected.";
		}
	}
	return $errors;
}

function removePlayersFromTeam($conn, $teamKey, $playerIDs) {
	$playerKeys = getPlayerJoinKeyArray($conn, $playerIDs);

	$stmt = $conn->prepare("DELETE FROM teamPlayers WHERE playerID = ? AND teamID = ?");
	$stmt->bind_param("ii", $playerKey, $teamKey);
	foreach($playerKeys as $playerKey) {
		$stmt->execute();
	}
	$stmt->close();

	// $stmt = $conn->prepare("DELETE FROM tournamentPlayers WHERE playerID = ? AND teamID = ?");
	// $stmt->bind_param("ii", $playerKey, $teamKey);
	// foreach($playerKeys as $playerKey) {
	// 	$stmt->execute();
	// }
	$stmt->close();
}
