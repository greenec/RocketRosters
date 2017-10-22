<?php

require "include/db.php";
require "include/functions.php";

if(isset($_GET['id'])) {
	$memberID = $_GET['id'];
} else {
	header('Location: register.php');
	die();
}

$memberInfo = getMemberInfo($conn, $memberID);

if(isset($memberInfo) && !$memberInfo['registered']) {
	completeRegistration($conn, $memberID);

	new Session($conn);
	$_SESSION["loggedin"] = true;
	$_SESSION["role"] = $memberInfo['role'];
	$_SESSION["id"] = $memberInfo['joinKey'];

	header('Location: account.php?c=1');
} else {
	header('Location: account.php');
	die();
}

function getMemberInfo($conn, $memberID) {
	$stmt = $conn->prepare("SELECT joinKey, role, registered FROM members WHERE id = ?");
	$stmt->bind_param("s", $memberID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($joinKey, $role, $registered);
	while($stmt->fetch()) {
		return array('joinKey' => $joinKey, 'role' => $role, 'registered' => $registered);
	}
	return false;
}

function completeRegistration($conn, $memberID) {
	$stmt = $conn->prepare("UPDATE members SET registered = true WHERE id = ?");
	$stmt->bind_param("s", $memberID);
	$stmt->execute();
	$stmt->close();
}
