<?php
// Set session time and checks the session for a userID to allow access
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: ../Access/login.php");
    exit();
}
?>