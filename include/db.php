<?php

// configure these to match your DB setup
$servername = "localhost";
$username = "";
$password = "";
$dbname = "";

// create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// check connection
if($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$password = null;
