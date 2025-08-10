<?php
require '../PHP/sessioncheck.php';
require '../PHP/db.php';

$userID = $_SESSION['userID'];
$carpoolID = $_GET['carpoolID'] ?? 0;

$sql = "SELECT 
            m.messageID, 
            m.userID, 
            u.firstName || ' ' || u.lastName AS senderName,
            m.message, 
            m.timestamp
        FROM Message m
        JOIN USER u 
            ON m.userID = u.userID
        WHERE m.carpoolID = :cid
        ORDER BY m.timestamp ASC";
$stmt = $conn->prepare($sql);
$stmt->execute(['cid' => $carpoolID]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>