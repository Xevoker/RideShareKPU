<?php
session_start();

if (isset($_SESSION['user_id'])) {
    echo "Logged in user ID: " . htmlspecialchars($_SESSION['user_id']);
} else {
    echo "No user is logged in.";
}
?>