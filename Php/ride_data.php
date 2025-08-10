<?php
//Session start and database
require '../PHP/sessioncheck.php';
require '../PHP/db.php';

$userID = $_SESSION['userID'];

// Get offered rides (sorted by date/time)
$stmtOffered = $conn->prepare("
    SELECT carpoolID, originAddress AS `from`, destinationAddress AS `to`,
           departureDate AS `date`, departureTime AS `time`,
           availableSeats AS `seats`, notes, createdDate AS postedAt
    FROM CARPOOL
    WHERE driverID = ?
    ORDER BY departureDate DESC, departureTime DESC
");
$stmtOffered->execute([$userID]);
$offered = $stmtOffered->fetchAll(PDO::FETCH_ASSOC);

foreach ($offered as &$ride) {
    $ride['type'] = 'offered';
}

// Get joined rides (sorted by date/time)
$stmtJoined = $conn->prepare("
    SELECT c.carpoolID, c.originAddress AS `from`, c.destinationAddress AS `to`,
           c.departureDate AS `date`, c.departureTime AS `time`,
           1 AS `seats`, 'You joined this ride.' AS notes, c.createdDate AS postedAt
    FROM RIDE r
    INNER JOIN CARPOOL c ON r.carpoolID = c.carpoolID
    WHERE r.riderID = ?
    ORDER BY c.departureDate DESC, c.departureTime DESC
");
$stmtJoined->execute([$userID]);
$joined = $stmtJoined->fetchAll(PDO::FETCH_ASSOC);

foreach ($joined as &$ride) {
    $ride['type'] = 'joined';
}

// Merge results 
$allRides = array_merge($offered, $joined);

usort($allRides, function($a, $b) {
    return strtotime($b['date'] . ' ' . $b['time']) - strtotime($a['date'] . ' ' . $a['time']);
});

header('Content-Type: application/json');
echo json_encode($allRides);
?>