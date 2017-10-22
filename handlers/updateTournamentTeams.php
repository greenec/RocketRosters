<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	header('Location: ../account.php');
	die();
}

require "../include/db.php";
require "../include/functions.php";

new Session($conn);

if($_SESSION['role'] == 'Coach' || $_SESSION['role'] == 'Director') {
	$role = $_SESSION['role'];
	$userKey = $_SESSION['id'];
} else {
	header('Location: ../account.php');
	die();
}

if(isset($_POST['action']) && isset($_POST['tournamentID']) && isset($_POST['teamID'])) {
	$action = $_POST['action'];
	$tournamentID = strtoupper(trim($_POST['tournamentID']));
	$teamID = $_POST['teamID'];
} else {
	die();
}

$teamInfo = getTeamInfo($conn, $teamID);
$tournamentInfo = getTournamentInfo($conn, $tournamentID);

if(isset($teamInfo) && isset($tournamentInfo)) {
	$teamKey = $teamInfo->teamKey;
	$teamName = $teamInfo->teamName;

	$tournamentKey = $tournamentInfo->tournamentKey;
	$tournamentName = $tournamentInfo->tournamentName;
} else {
	$teamKey = null;
	$teamName = null;

	$tournamentKey = null;
	$tournamentName = null;
}

// initialize JSON variables
$errors = validate($conn, $action, $role, $userKey, $tournamentKey, $teamKey, $teamID, $tournamentID);
$data = array();

if(empty($errors)) {
	if($action == 'add') {
		joinTournament($conn, $tournamentKey, $teamKey);
		$data["success"] = true;
		$data["url"] = "manageTournamentPlayers.php?teamID=$teamID&tournamentID=$tournamentID";
	}
	if($action == 'remove') {
		leaveTournament($conn, $tournamentKey, $teamKey);
		$data["success"] = true;
		$data["tournamentName"] = $tournamentName;
		$data["teamName"] = $teamName;
	}
} else {
	$data["success"] = false;
	$data["errors"] = $errors;
}

echo json_encode($data);

function validate($conn, $action, $role, $userKey, $tournamentKey, $teamKey, $teamID, $tournamentID) {
	$errors = array();
	if(empty($tournamentKey)) {
		$errors["tournamentID"] = "There are no tournaments with this code.";
	}
	if($role == 'Coach') {
		if($action == 'add') {
			if(!coachOwnsTeam($conn, $userKey, $teamKey)) {
				$errors["team"] = "You do not own this team, or this team does not exist.";
			}
			if($teamID == "defaultTeamName") {
				$errors["team"] = "Please select a team.";
			}
			if(empty($tournamentID)) {
				$errors["tournamentID"] = "Please enter a tournament code.";
			}
		}
	}
	if($role == 'Director' && $action == 'remove') {
		if(!directorOwnsTournament($conn, $userKey, $tournamentKey)) {
			$errors["error"] = "You do not own this tournament.";
		}
	}
	return $errors;
}

function joinTournament($conn, $tournamentKey, $teamKey) {
	$stmt = $conn->prepare("INSERT INTO tournamentTeams (tournamentID, teamID) VALUES (?, ?)");
	$stmt->bind_param("ii", $tournamentKey, $teamKey);
	$stmt->execute();
}

function leaveTournament($conn, $tournamentKey, $teamKey) {
	$stmt = $conn->prepare("DELETE FROM tournamentTeams WHERE tournamentID = ? AND teamID = ?");
	$stmt->bind_param("ii", $tournamentKey, $teamKey);
	$stmt->execute();
	$stmt = $conn->prepare("DELETE FROM tournamentPlayers WHERE tournamentID = ? AND teamID = ?");
	$stmt->bind_param("ii", $tournamentKey, $teamKey);
	$stmt->execute();
	$stmt->close();
}
