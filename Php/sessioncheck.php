<?php
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: ../Access/login.php");
    exit();
}
?>