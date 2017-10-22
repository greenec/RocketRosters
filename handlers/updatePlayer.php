<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	header('Location: ../account.php');
	die();
}

if(isset($_POST['action']) && isset($_POST['teamID'])) {
	$action = $_POST['action'];
	$teamID = $_POST['teamID'];
} else {
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

if($action == 'add') {
	$playerInfo = array();
	$playerInfo['firstName'] = trim($_POST['firstName']);
	$playerInfo['lastName'] = trim($_POST['lastName']);
	$playerInfo['parentEmail'] = trim($_POST['parentEmail']);
	$playerInfo['playerEmail'] = trim($_POST['playerEmail']);
	$playerInfo['phone'] = trim($_POST['phone']);
	$playerInfo['jersey'] = trim(str_replace('#', '', $_POST['jersey']));
	$playerInfo['position'] = trim($_POST['position']);
	$playerInfo['address'] = trim($_POST['address']);
	$playerInfo['city'] = trim($_POST['city']);
	$playerInfo['state'] = trim($_POST['state']);
	$playerInfo['zip'] = trim($_POST['zip']);
	$playerInfo['graduating'] = trim($_POST['graduating']);

	$dob = array();
	$dob['month'] = trim($_POST['dobM']);
	$dob['day'] = trim($_POST['dobD']);
	$dob['year'] = trim($_POST['dobY']);

	$teamKey = getTeamJoinKey($conn, $teamID);

	if(empty($teamKey)) {
		die();
	}
} else {
	die();
}

// initialize JSON variables
$errors = validate($conn, $coachID, $teamKey, $action, $playerInfo, $dob);
$data = array();

if(empty($errors)) { // no errors so far, let's look for a player
	$addPlayer = true;

	// format date of birth for the database
	$dob = date_create((int)$dob["year"] . "-" . (int)$dob["month"] . "-" . (int)$dob["day"]);
	$dob = date_format($dob, "Y-m-d");

	$playerInfo['graduating'] = (int)$playerInfo['graduating'];

	$playerMatch = matchPlayer($conn, $playerInfo['firstName'], $playerInfo['lastName'], $dob);

	if(isset($playerMatch)) { // player exists
		$playerKey = $playerMatch['playerKey'];
		$playerID = $playerMatch['playerID'];

		if(playerOnTeam($conn, $teamKey, $playerKey)) { // player is already on the team
			$errors["firstName"] = "Player has already joined this team.";

			$data["success"] = false;
			$data["errors"] = $errors;

			$addPlayer = false;
		}
	} else { // player doesn't exist
		$playerID = getCode($conn, 6);
		$playerKey = null;
	}

	if($addPlayer) {
		createPlayer($conn, $playerInfo, $playerID, $teamKey, $dob, $playerKey);
		$data["success"] = true;
		$data['playerInfo'] = array(
			'firstName' => e($playerInfo['firstName']),
			'lastName' => e($playerInfo['lastName']),
			'url' => "viewPlayer.php?teamID=$teamID&playerID=$playerID",
			'dob' => date_format(date_create($dob), "m/d/Y"),
			'graduating' => $playerInfo['graduating'],
			'jersey' => e($playerInfo['jersey']),
			'position' => e($playerInfo['position']),
			'playerID' => $playerID
		);
	}
} else {
	$data['success'] = false;
	$data['errors'] = $errors;
}

echo json_encode($data);

function validate($conn, $coachID, $teamKey, $action, $playerInfo, $dob) {
	$errors = array();
	if($action == 'add') {
		if(empty($playerInfo['firstName'])) {
			$errors["firstName"] = "No first name entered.";
		}
		if(empty($playerInfo['lastName'])) {
			$errors["lastName"] = "No last name entered.";
		}
		if(empty($playerInfo['phone'])) {
			$errors["phone"] = "No phone number entered.";
		}
		if(empty($playerInfo['position'])) {
			$errors["jerseyPositionGraduating"] = "No position entered.";
		}
		if(empty($playerInfo['jersey'])) {
			$errors["jerseyPositionGraduating"] = "No jersey entered.";
		}
		if(empty($playerInfo['address'])) {
			$errors["address"] = "No address entered.";
		}
		if(empty($playerInfo['zip'])) {
			$errors["cityStateZip"] = "No zip code entered.";
		}
		if(empty($playerInfo['state'])) {
			$errors["cityStateZip"] = "No state entered.";
		}
		if(empty($playerInfo['city'])) {
			$errors["cityStateZip"] = "No city entered.";
		}

		if(empty($playerInfo['parentEmail']) || !filter_var($playerInfo['parentEmail'], FILTER_VALIDATE_EMAIL)) { // email is invalid or empty
			$errors["parentEmail"] = "Please enter a valid email.";
		}
		if(empty($playerInfo['playerEmail']) || !filter_var($playerInfo['playerEmail'], FILTER_VALIDATE_EMAIL)) { // email is invalid or empty
			$errors["playerEmail"] = "Please enter a valid email.";
		}

		if(strlen($playerInfo['firstName']) > 50) {
			$errors["firstName"] = "First name cannot exceed 50 characters.";
		}
		if(strlen($playerInfo['lastName']) > 50) {
			$errors["lastName"] = "Last name cannot exceed 50 characters.";
		}
		if(strlen($playerInfo['parentEmail']) > 100) {
			$errors["parentEmail"] = "Email cannot exceed 100 characters.";
		}
		if(strlen($playerInfo['playerEmail']) > 100) {
			$errors["playerEmail"] = "Email cannot exceed 100 characters.";
		}
		if(strlen($playerInfo['phone']) > 25) {
			$errors["phone"] = "Phone number cannot exceed 25 characters.";
		}
		if(strlen($playerInfo['position']) > 15) {
			$errors["jerseyPositionGraduating"] = "Position cannot exceed 15 characters.";
		}
		if(strlen($playerInfo['jersey']) > 3) {
			$errors["jerseyPositionGraduating"] = "Jersey number cannot exceed 3 characters.";
		}
		if(strlen($playerInfo['address']) > 100) {
			$errors["address"] = "Address cannot exceed 100 characters.";
		}
		if(strlen($playerInfo['zip']) > 5) {
			$errors["cityStateZip"] = "Zip code cannot exceed 5 characters.";
		}
		if(strlen($playerInfo['state']) > 25) {
			$errors["cityStateZip"] = "State cannot exceed 25 characters.";
		}
		if(strlen($playerInfo['city']) > 35) {
			$errors["cityStateZip"] = "City cannot exceed 35 characters.";
		}

		if(!coachOwnsTeam($conn, $coachID, $teamKey)) {
			$errors["error"] = "You do not own this team.";
		}
		$dobErrors = validateDate($dob);
		if(isset($dobErrors)) {
			$errors["dob"] = $dobErrors;
		}

		$graduating = (int)$playerInfo['graduating'];
		if($graduating < 1970 || $graduating > 2155) {
			$errors['jerseyPositionGraduating'] = 'Graduating year is out of range.';
		}
		if(strlen($playerInfo['graduating']) != 4) {
			$errors['jerseyPositionGraduating'] = 'Please enter a valid graduating year.';
		}
		if(empty($playerInfo['graduating'])) {
			$errors['jerseyPositionGraduating'] = 'No graduating year entered.';
		}

	}
	return $errors;
}

function validateDate($date) {
	$month = $date['month'];
	$day = $date['day'];
	$year = $date['year'];
	if($month === 'default') {
		return "Please select a month.";
	}
	if(empty($day)) {
		return "Please enter a day.";
	}
	if(empty($year)) {
		return "Please enter a year.";
	}
	if(strlen($year) != 4) {
		return "Please enter a valid 4-digit year.";
	}
	$month = (int)$month;
	$day = (int)$day;
	$year = (int)$year;
	if($year < 1970 || $year > date('Y') - 2) {
		return "Year is out of range.";
	}
	if(!checkdate($month, $day, $year)) {
		return "Invalid date.";
	}
	if(date_create("$year-$month-$day") > date_create()) {
		return "This date hasn't occured yet.";
	}
}

function matchPlayer($conn, $firstName, $lastName, $dob) {
	$stmt = $conn->prepare("SELECT joinKey, playerID FROM players WHERE dob = ? AND lastName = ? AND firstName = ?");
	$stmt->bind_param("sss", $dob, $lastName, $firstName);
	$stmt->store_result();
	$stmt->bind_result($playerKey, $playerID);
	$stmt->execute();
	while($stmt->fetch()) {
		return array("playerKey" => $playerKey, "playerID" => $playerID);
	}
}

function createPlayer($conn, $playerInfo, $playerID, $teamKey, $dob, $playerKey) {
	if(empty($playerKey)) { // create a new player
		$stmt = $conn->prepare("INSERT INTO players (firstName, lastName, playerID, dob) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("ssss", $playerInfo['firstName'], $playerInfo['lastName'], $playerID, $dob);
		$stmt->execute();

		$playerKey = getPlayerJoinKey($conn, $playerID);
	}

	$stmt = $conn->prepare("INSERT INTO teamPlayers (teamID, playerID, parentEmail, playerEmail, phone, jersey, position, address, city, state, zip, graduating) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("iissssssssss", $teamKey, $playerKey, $playerInfo['parentEmail'], $playerInfo['playerEmail'], $playerInfo['phone'], $playerInfo['jersey'], $playerInfo['position'], $playerInfo['address'], $playerInfo['city'], $playerInfo['state'], $playerInfo['zip'], $playerInfo['graduating']);
	$stmt->execute();
	$stmt->close();
}
