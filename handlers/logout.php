<?php

require "../include/db.php";
require "../include/functions.php";

new Session($conn);
session_destroy();
header('Location: ../login.php');
