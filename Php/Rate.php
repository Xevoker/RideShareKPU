<?php
session_start();
 include '../Php/db.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userID = $_SESSION['userID'];
    $carpoolID = $_SESSION['carpoolID'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];
    $timestamp = date('Y-m-d H:i:s');

    $sql = "INSERT INTO Review (carpoolID, userID, rating, review, timestamp)
            VALUES (:carpoolID, :userID, :rating, :review, :timestamp)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $errorInfo = $conn->errorInfo();
        die("Error preparing SQL statement: " . implode(" | ", $errorInfo));
    }

    $stmt->execute([
        ':carpoolID' => $carpoolID,
        ':userID'    => $userID,
        ':rating'    => $rating,
        ':review'    => $review,
        ':timestamp' => $timestamp
    ]);

    echo "Review submitted successfully.";
}
?>