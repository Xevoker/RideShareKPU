<?php
if ($_SERVER["REQUEST_METHOD"] === "POST"){
  $userID = $_SESSION['userID'];
  $message = $_POST['message'];
  $carpoolID = $SESSION['carpool'];
  //maybe add a name column to display who text what?
  $timestamp =  date('Y-m-d H:i:s');
    
  // Insert user into the database
    $sql = "INSERT INTO Message (carpoolID, userID, message, timestamp)
        VALUES (  :carpoolID, :userID, :message, :timestamp)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $errorInfo = $conn->errorInfo();
        die("Error preparing SQL statement: " . implode(" | ", $errorInfo));
    }
    $stmt->execute([
    ':carpoolID'     => $carpoolID,
    ':userID'        => $userID,
    ':message'       => $message,
    ':timestamp'     => $timestamp,
    ]);
    }
?>