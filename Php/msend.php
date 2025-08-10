<?php
//Session start and database
require '../PHP/sessioncheck.php';
require '../PHP/db.php';

$userID = $_SESSION['userID'];
$data = json_decode(file_get_contents('php://input'), true);

// Variables
$carpoolID = $data['carpoolID'] ?? 0;
$text = trim($data['text'] ?? '');

// For sending a message
if ($carpoolID && $text) {
    $stmt = $conn->prepare("INSERT INTO Message (carpoolID, userID, message) VALUES (?, ?, ?)");
    $stmt->execute([$carpoolID, $userID, $text]);
    echo json_encode(['success' => true, 'time' => date('H:i')]);
} else {
    echo json_encode(['success' => false]);
}
?>