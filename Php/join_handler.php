<?php
session_start();
require '../PHP/db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../Access/login.php");
    exit();
}

$userID = $_SESSION['userID'];
$carpoolID = $_POST['carpoolID'] ?? null;
$_SESSION['carpoolID'] = $carpoolID;
$pickup = $_SESSION['pickup_place'] ?? null;

if (!$carpoolID) {
    die("Invalid request.");
}

// Check if already joined
$stmt = $conn->prepare("SELECT * FROM RIDE WHERE carpoolID = :cid AND riderID = :rid");
$stmt->execute([
    ':cid' => $carpoolID,
    ':rid' => $userID
]);
$alreadyJoined = $stmt->fetch(PDO::FETCH_ASSOC);

if ($alreadyJoined) {
    echo "<script>alert('You have already joined this ride.'); window.location.href='../Main/find.php';</script>";
    $_SESSION['carpoolID'] = $carpoolID;
    exit();
}

// Check available seats
$stmt = $conn->prepare("SELECT availableSeats FROM CARPOOL WHERE carpoolID = :cid");
$stmt->execute([':cid' => $carpoolID]);
$ride = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ride || $ride['availableSeats'] <= 0) {
    echo "<script>alert('No seats left for this ride.'); window.location.href='../Main/find.php';</script>";
    exit();
}

// Join the ride
try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM RIDE WHERE carpoolID = :carpoolID");
    $stmt->execute([':carpoolID' => $carpoolID]);
    $joinedCount = (int)$stmt->fetchColumn();

    $stmt = $conn->prepare("SELECT availableSeats FROM CARPOOL WHERE carpoolID = :carpoolID");
    $stmt->execute([':carpoolID' => $carpoolID]);
    $availableSeats = (int)$stmt->fetchColumn();

    if ($joinedCount < $availableSeats) {
        $stmt = $conn->prepare("INSERT INTO RIDE (carpoolID, riderID, pickupLocation) VALUES (:carpoolID, :riderID, :pickupLocation)");
        $stmt->execute([
            ':carpoolID' => $carpoolID,
            ':riderID' => $userID,
            ':pickupLocation' => $pickup
]);
    } else {
        // No seats available, handle error
        echo "<script>alert('Sorry, no seats left for this ride.'); window.history.back();</script>";
        exit();
    }

    $conn->commit();

    echo "<script>alert('You joined the ride!'); window.location.href='../Main/dashboard.php';</script>";
} catch (Exception $e) {
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
}
?>