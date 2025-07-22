<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST"){
    // Variables
    $studentID = $_POST['studentID'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT userID, userPassword FROM USER WHERE studentID = :studentID");
    if (!$stmt) {
        $errorInfo = $conn->errorInfo();
        die("Error preparing SQL statement: " . implode(" | ", $errorInfo));
    }
    $stmt->execute([':studentID' => $studentID]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $id = $user['userID'];
    $password_hash = $user['userPassword'];
    if ($id && password_verify($password, $password_hash)) {
        
        // Successful login
        $_SESSION['user_id'] = $id;
        header("Location: dashboard.php");
        exit;
    } else {
         // Redirect back to login form with error message in URL
        header("Location: login_form.php?error=Invalid+studentID+or+password");
        exit();
    }
        $stmt->close();
    }
?>