<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	header('Location: ../register.php');
	die();
}

require "../include/db.php";
require "../include/functions.php";
require "../include/mail/PHPMailerAutoload.php";

// get POST variables
$firstName = isset($_POST['firstName']) ? $_POST['firstName'] : '';
$lastName = isset($_POST['lastName']) ? $_POST['lastName'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$passwordConfirm = isset($_POST['passwordConfirm']) ? $_POST['passwordConfirm'] : '';
$role = isset($_POST['role']) ? $_POST['role'] : '';

// initialize JSON variables
$errors = validate($conn, $firstName, $lastName, $email, $password, $passwordConfirm, $role);
$data = array();

if(empty($errors)) {
	if(!registerUser($conn, $firstName, $lastName, $email, $password, $role)) {
		$data['success'] = false;
		$errors['email'] = "Email not sent. Please contact our webmaster at <a href='mailto:connor@efight.me'>connor@efight.me</a> if this problem persists.";
		$data['errors'] = $errors;
	} else {
		$data['success'] = true;
		$data['email'] = $email;
	}
} else {
	$data['success'] = false;
	$data['errors'] = $errors;
}

echo json_encode($data);

function validate($conn, $firstName, $lastName, $email, $password, $passwordConfirm, $role) {
	$errors = array();
	if($role !== 'Coach' && $role !== 'Director' && $role !== 'Recruiter') { // input is hijacked
		$errors["role"] = "Invalid role.";
	}
	if($role == "default") { // no role selected
		$errors["role"] = "Please select a role.";
	}
	if(empty($firstName)) { // first name is empty
		$errors["firstName"] = "Please enter a first name.";
	}
	if(strlen($firstName) > 63) { // first name is too long
		$errors["firstName"] = "Name cannot exceed 63 characters.";
	}
	if(empty($lastName)) { // last name is empty
		$errors["lastName"] = "Please enter a last name.";
	}
	if(strlen($lastName) > 63) { // last name is too long
		$errors["lastName"] = "Name cannot exceed 63 characters.";
	}
	if(!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($email)) { // email is invalid or empty
		$errors["email"] = "Please enter a valid email.";
	}
	if(strlen($email) > 254) {
		$errors["email"] = "Email length cannot exceed 254 characters.";
	}
	if(emailUsed($conn, $email)) { // email is in use
		$errors["email"] = "Email is already in use.";
	}
	if(empty($password)) { // password is empty
		$errors["password"] = "Please enter a password.";
	}
	if($password != $passwordConfirm) { // passwords are different
		$errors["passwordConfirm"] = "Passwords are not the same.";
	}
	if(strlen($password) > 72) {
		$errors["password"] = "Password length cannot exceed 72 characters.";
	}
	return $errors;
}

function emailUsed($conn, $email) {
	$stmt = $conn->prepare("SELECT email FROM members WHERE email = ? AND registered = true");
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$stmt->store_result();
	$num_of_rows = $stmt->num_rows;
	$stmt->close();
	return $num_of_rows != 0;
}

function registerUser($conn, $firstName, $lastName, $email, $password, $role) {
	$match = matchMember($conn, $email);
	if(!$match) {
		$password = password_hash($password, PASSWORD_BCRYPT);
		$memberID = getCode($conn, 6);

		$stmt = $conn->prepare("INSERT INTO members (firstName, lastName, email, password, role, id) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssss", $firstName, $lastName, $email, $password, $role, $memberID);
		$stmt->execute();
		$stmt->close();
	} else {
		$memberID = $match['memberID'];
		$joinKey = $match['joinKey'];
		$password = password_hash($password, PASSWORD_BCRYPT);

		$stmt = $conn->prepare("UPDATE members SET firstName = ?, lastName = ?, password = ?, role = ? WHERE joinKey = ?");
		$stmt->bind_param("sssss", $firstName, $lastName, $password, $role, $joinKey);
		$stmt->execute();
		$stmt->close();
	}
	return sendConfirmationEmail($email, $firstName, $memberID);
}

function matchMember($conn, $email) {
	$stmt = $conn->prepare("SELECT id, joinKey FROM members WHERE email = ? AND registered = false");
	$stmt->bind_param('s', $email);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $joinKey);
	while($stmt->fetch()) {
		$stmt->close();
		return array('joinKey'=>$joinKey, 'memberID'=>$id);
	}
	return false;
}

function sendConfirmationEmail($to, $firstName, $memberID) {
	$mail = new Mail();

	$subject = 'Rocket Rosters Registration';
	$url = "https://rocketrosters.com/completeRegistration.php?id=$memberID";
	$msg = "Hello, $firstName!<br>Complete your registration for Rocket Rosters at <a href='$url'>$url</a>.";

	return $mail->send($to, 'Registration', $subject, $msg);
}
