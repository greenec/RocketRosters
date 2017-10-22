<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	header('Location: account.php');
	die();
}

require "../include/db.php";
require "../include/functions.php";

new Session($conn);

if(isset($_SESSION['loggedin']) && $_SESSION["role"] == "Director") {
	$directorID = $_SESSION['id'];
} else {
	die();
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

if($action == 'add') {
	$tournamentName = isset($_POST['tournamentName']) ? $_POST['tournamentName'] : '';
	$tournamentKey = null;
}
if($action == 'remove') {
	if(isset($_POST['tournamentID'])) {
		$tournamentID = $_POST['tournamentID'];
		$tournamentInfo = getTournamentInfo($conn, $tournamentID);
	}

	if(isset($tournamentInfo)) {
		$tournamentKey = $tournamentInfo->tournamentKey;
		$tournamentName = $tournamentInfo->tournamentName;
	} else {
		die();
	}
}

// initialize JSON variables
$errors = validate($conn, $tournamentName, $directorID, $tournamentKey, $action);
$data = array();

if(empty($errors)) {
	if($action == 'add') {
		$code = getCode($conn, 6);
		createTournament($conn, $directorID, $code, $tournamentName);
		$data["success"] = true;
		$data["tournamentName"] = e($tournamentName);
		$data["tournamentID"] = $code;
		$data['date'] = date('m/d/Y');
	}
	if($action == 'remove') {
		deleteTournament($conn, $tournamentKey, $tournamentID);
		$data["success"] = true;
		$data["tournamentName"] = $tournamentName;
		$data["tournamentID"] = $tournamentID;
	}
} else {
	$data["success"] = false;
	$data["errors"] = $errors;
}

echo json_encode($data);

function validate($conn, $tournamentName, $directorID, $tournamentKey, $action) {
	$errors = array();
	if($action == 'add') {
		if(empty($tournamentName)) {
			$errors["tournamentName"] = "Please enter a tournament name.";
		}
		if(strlen($tournamentName) > 63) {
			$errors["tournamentName"] = "Tournament name length cannot exceed 63 characters.";
		}
		if(tournamentNameUsed($conn, $tournamentName, $directorID)) {
			$errors["tournamentName"] = "You already have a tournament with this name.";
		}
	}
	if($action == 'remove') {
		if(!directorOwnsTournament($conn, $directorID, $tournamentKey)) {
			$errors["error"] = "You do not own this tournament.";
		}
	}
	return $errors;
}

function tournamentNameUsed($conn, $tournamentName, $directorID) {
	$stmt = $conn->prepare("SELECT joinKey FROM tournaments WHERE directorID = ? AND tournamentName = ?");
	$stmt->bind_param("is", $directorID, $tournamentName);
	$stmt->execute();
	$stmt->store_result();
	$num_of_rows = $stmt->num_rows;
	$stmt->close();
	return $num_of_rows != 0;
}

function createTournament($conn, $directorID, $code, $tournamentName) {
	$stmt = $conn->prepare("INSERT INTO tournaments (directorID, tournamentID, tournamentName) VALUES (?, ?, ?)");
	$stmt->bind_param("iss", $directorID, $code, $tournamentName);
	$stmt->execute();
	$stmt->close();
}

function deleteTournament($conn, $tournamentKey, $tournamentID) {
	$stmt = $conn->prepare("DELETE FROM tournaments WHERE joinKey = ?");
	$stmt->bind_param("i", $tournamentKey);
	$stmt->execute();

	// $stmt = $conn->prepare("DELETE FROM tournamentTeams WHERE tournamentID = ?");
	// $stmt->bind_param("i", $tournamentKey);
	// $stmt->execute();
	//
	// $stmt = $conn->prepare("DELETE FROM recruiters WHERE tournamentID = ?");
	// $stmt->bind_param("i", $tournamentKey);
	// $stmt->execute();
	//
	// $stmt = $conn->prepare("DELETE FROM tournamentPlayers WHERE tournamentID = ?");
	// $stmt->bind_param("i", $tournamentKey);
	// $stmt->execute();

	$stmt->close();

	removeCode($conn, $tournamentID);
}
