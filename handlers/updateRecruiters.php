<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	header('Location: ../account.php');
	die();
}

require "../include/db.php";
require "../include/functions.php";

if(isset($_POST['action']) && isset($_POST['tournamentID'])) {
	$action = $_POST['action'];
	$tournamentID = strtoupper(trim($_POST['tournamentID']));

	$tournamentInfo = getTournamentInfo($conn, $tournamentID);

	if(isset($tournamentInfo)) {
		$tournamentKey = $tournamentInfo->tournamentKey;
		$tournamentName = $tournamentInfo->tournamentName;
	} else {
		die();
	}
}

new Session($conn);

if(isset($_SESSION['loggedin']) && $_SESSION["role"] == "Recruiter") {
	$recruiterID = $_SESSION['id'];
} else {
	die();
}

// initialize JSON variables
$errors = validate($conn, $tournamentKey, $tournamentID, $recruiterID, $action);
$data = array();

if(empty($errors)) {
	if($action == 'add') {
		addRecruiter($conn, $recruiterID, $tournamentKey);

		$data["tournamentName"] = $tournamentName;
		$data["tournamentID"] = $tournamentID;
		$data["viewURL"] = "viewTournament.php?tournamentID=$tournamentID";
		$data['date'] = date('m/d/Y');
	}
	if($action == 'remove') {
		removeRecruiter($conn, $recruiterID, $tournamentKey);

		$data["tournamentID"] = $tournamentID;
	}
}

if(!empty($errors)) {
	$data["success"] = false;
	$data["errors"] = $errors;
} else {
	$data["success"] = true;
}

echo json_encode($data);

function validate($conn, $tournamentKey, $tournamentID, $recruiterID, $action) {
	$errors = array();
	if(empty($tournamentKey)) {
		$errors["tournamentID"] = "Tournament does not exist.";
	}
	if(empty($tournamentID)) {
		$errors["tournamentID"] = "Please enter a tournament code.";
	}
	return $errors;
}

function addRecruiter($conn, $recruiterID, $tournamentKey) {
	$stmt = $conn->prepare("INSERT INTO recruiters (recruiterID, tournamentID) VALUES (?, ?)");
	$stmt->bind_param("ii", $recruiterID, $tournamentKey);
	$stmt->execute();
	$stmt->close();
}

function removeRecruiter($conn, $recruiterID, $tournamentKey) {
	$stmt = $conn->prepare("DELETE FROM recruiters WHERE recruiterID = ? AND tournamentID = ?");
	$stmt->bind_param("ii", $recruiterID, $tournamentKey);
	$stmt->execute();
	$stmt->close();
}
