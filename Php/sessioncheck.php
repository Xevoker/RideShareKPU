<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: ../Access/login.php");
    exit();
}
?>