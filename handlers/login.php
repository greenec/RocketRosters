<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	header('Location: ../login.php');
	die();
}

require "../include/db.php";
require "../include/functions.php";

if(isset($_POST['email']) && isset($_POST['password'])) {
	$email = $_POST['email'];
	$password = $_POST['password'];
} else {
	header('Location: ../login.php');
	die();
}

// initialize JSON variables
$errors = array();
$data = array();

// check email and password
$stmt = $conn->prepare("SELECT password, role, joinKey FROM members WHERE email = ? AND registered = true");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$num_of_rows = $stmt->num_rows;
$stmt->bind_result($password_db, $role, $joinKey);

if($num_of_rows !== 0) {
	while($stmt->fetch()) {
		if(password_verify($password, $password_db)) {
			new Session($conn);
			$_SESSION["loggedin"] = true;
			$_SESSION["role"] = $role;
			$_SESSION["id"] = $joinKey;
		} else {
			$error = true;
		}
	}
} else {
	$error = true;
}

if(isset($error)) {
	$errors["error"] = "No account with this email and password found.";
}

if(!empty($errors)) {
	$data["success"] = false;
	$data["errors"] = $errors;
} else {
	$data["success"] = true;
}

echo json_encode($data);

$stmt->close();
