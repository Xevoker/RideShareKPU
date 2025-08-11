<?php
$userID = $_SESSION['userID'];

// Function to check if the address already exists
function IfNotExists($conn, $userID, $address) {
    $address = trim($address);
    if ($address === '') return; // skip empty

    // Check for duplicate - only one since there shouldn't be more than 1
    $stmtCheck = $conn->prepare("SELECT 1 FROM ADDRESS WHERE userID = ? AND address = ? LIMIT 1");
    $stmtCheck->execute([$userID, $address]);
    if (!$stmtCheck->fetch()) {
        // Insert if not exists
        $stmtInsert = $conn->prepare("INSERT INTO ADDRESS (userID, address) VALUES (?, ?)");
        $stmtInsert->execute([$userID, $address]);
    }
}

// Get offered rides
$stmtOffered = $conn->prepare("
    SELECT originAddress 
    FROM CARPOOL
    WHERE driverID = ?
");
$stmtOffered->execute([$userID]);
$offered = $stmtOffered->fetchAll(PDO::FETCH_ASSOC);

foreach ($offered as &$ride) {
    IfNotExists($conn, $userID, $ride['originAddress']);
}

// Get joined rides
$stmtJoined = $conn->prepare("
    SELECT pickupLocation 
    FROM RIDE
    WHERE riderID = ?
");
$stmtJoined->execute([$userID]);
$joined = $stmtJoined->fetchAll(PDO::FETCH_ASSOC);

foreach ($joined as &$ride) {
    IfNotExists($conn, $userID, $ride['pickupLocation']);
}

    //Move to the main page
    header("Location: ../Main/dashboard.php");
    exit;
?>