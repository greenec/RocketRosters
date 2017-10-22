<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	header('Location: ../account.php');
	die();
}

require "../include/db.php";
require "../include/functions.php";

new Session($conn);

if(isset($_SESSION['loggedin']) && $_SESSION['role'] == 'Coach') {
	$coachID = $_SESSION["id"];
} else {
	die();
}

if(empty($_POST['token']) || $_SESSION['token'] != $_POST['token']) {
	die('Invalid CSRF token.');
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

if($action == 'add') {
	$teamName = isset($_POST['teamName']) ? $_POST['teamName'] : '';
	$teamKey = null;
}
if($action == 'remove') {
	if(isset($_POST['teamID'])) {
		$teamID = $_POST["teamID"];
		$teamInfo = getTeamInfo($conn, $teamID);
	}

	if(isset($teamInfo)) {
		$teamKey = $teamInfo->teamKey;
		$teamName = $teamInfo->teamName;
	} else {
		die();
	}
}

// initialize JSON variables
$errors = validate($conn, $teamName, $coachID, $teamKey, $action);
$data = array();

if(empty($errors)) {
	if($action == 'add') {
		$code = getCode($conn, 6);
		createTeam($conn, $coachID, $code, $teamName);
		$data["success"] = true;
		$data["teamName"] = e($teamName);
		$data["teamID"] = $code;
		$data['date'] = date('m/d/Y');
	}
	if($action == 'remove') {
		deleteTeam($conn, $teamKey, $teamID);
		$data["success"] = true;
		$data["teamName"] = $teamName;
		$data["teamID"] = $teamID;
	}
} else {
	$data["success"] = false;
	$data["errors"] = $errors;
}

echo json_encode($data);

function validate($conn, $teamName, $coachID, $teamKey, $action) {
	$errors = array();
	if($action == 'add') {
		if(empty($teamName)) {
			$errors["teamName"] = "No team name entered.";
		}
		if(strlen($teamName) > 75) {
			$errors["teamName"] = "Team name cannot exceed 75 characters.";
		}
		if(teamNameUsed($conn, $teamName, $coachID)) {
			$errors["teamName"] = "You already have a team with this name.";
		}
	}
	if($action == 'remove' && !coachOwnsTeam($conn, $coachID, $teamKey)) {
		$errors["teamName"] = "You do not own this team.";
	}
	return $errors;
}

// TODO index or remove this
function teamNameUsed($conn, $teamName, $coachID) {
	$stmt = $conn->prepare("SELECT teamID FROM teams WHERE coachID = ? AND teamName = ? ");
	$stmt->bind_param("is", $coachID, $teamName);
	$stmt->execute();
	$stmt->store_result();
	$num_of_rows = $stmt->num_rows;
	$stmt->close();
	return $num_of_rows != 0;
}

function createTeam($conn, $coachID, $code, $teamName) {
	$stmt = $conn->prepare("INSERT INTO teams (coachID, teamID, teamName) VALUES (?, ?, ?)");
	$stmt->bind_param("iss", $coachID, $code, $teamName);
	$stmt->execute();
	$stmt->close();
}

function deleteTeam($conn, $teamKey, $teamID) {
	$stmt = $conn->prepare("DELETE FROM teams WHERE joinKey = ?");
	$stmt->bind_param("i", $teamKey);
	$stmt->execute();

	// $stmt = $conn->prepare("DELETE FROM tournamentTeams WHERE teamID = ?");
	// $stmt->bind_param("i", $teamKey);
	// $stmt->execute();
	//
	// $stmt = $conn->prepare("DELETE FROM teamPlayers WHERE teamID = ?");
	// $stmt->bind_param("i", $teamKey);
	// $stmt->execute();
	//
	// $stmt = $conn->prepare("DELETE FROM tournamentPlayers WHERE teamID = ?");
	// $stmt->bind_param("i", $teamKey);
	// $stmt->execute();

	removeCode($conn, $teamID);

	$stmt->close();
}
