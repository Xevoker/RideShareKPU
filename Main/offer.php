<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: ../Access/login.php");
    exit();
}
include '../PHP/db.php';
if ($_SERVER["REQUEST_METHOD"] === "POST"){
$driverID = $_SESSION['userID'] ?? null;
$originAddress = trim($_POST['startaddress'] ?? '');
$destinationAddress = trim($_POST['endaddress'] ?? '');
$departureDate = $_POST['date'] ?? '';
$departureTime = $_POST['time'] ?? '';
$availableSeats = intval($_POST['seats'] ?? 0);
$status = "offered";
$distance = null; // or calculate it

if ($driverID && $originAddress && $destinationAddress && $departureDate && $departureTime && $availableSeats > 0) {

    $sql = "INSERT INTO CARPOOL 
            (driverID, originAddress, destinationAddress, departureDate, departureTime, availableSeats, status, distance) 
            VALUES 
            (:driverID, :originAddress, :destinationAddress, :departureDate, :departureTime, :availableSeats, :status, :distance)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $errorInfo = $conn->errorInfo();
        die("Error preparing SQL statement: " . implode(" | ", $errorInfo));
    }

    $stmt->execute([
        ':driverID'         => $driverID,
        ':originAddress'    => $originAddress,
        ':destinationAddress' => $destinationAddress,
        ':departureDate'    => $departureDate,
        ':departureTime'    => $departureTime,
        ':availableSeats'   => $availableSeats,
        ':status'           => $status,
        ':distance'         => $distance,
    ]);

    $carpoolID = $conn->lastInsertId();

    $_SESSION['carpoolID'] = $carpoolID;

    echo "Ride posted successfully.";

} else {
    echo "Missing required fields or invalid data.";
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Offer a Ride – RideShare</title>
  <link rel="stylesheet" href="../CSS/offer.css" />
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="logo-container">
      <img src="../images/a.jpg" class="logo-img" alt="Logo">
      <h2>Ride<span>Share</span></h2>
    </div>
    <nav>
      <ul>
        <li><a href="dashboard.php"aria-label="Dashboard">Dashboard</a></li>
        <li><a href="join.html" aria-label="Join Ride">Join Ride</a></li>
        <li><a href="offer.php" aria-label="Offer Ride">Offer Ride</a></li>
        <li><a href="find.php" aria-label="Find Ride">Find Ride</a></li>
        <li><a href="profile.php" aria-label="Profile">Profile</a></li>
        <li><a href="messages.html" aria-label="Messages">Messages</a></li>
        <li><a href="ride history.html" aria-label="Ride History">Ride History</a></li>
        <li><a href="feedback.html" aria-label="User Feedback">Feedback</a></li>
        <li><a href="settings.html"aria-label="Settings">Settings</a></li>
        <li><a href="logout.php" aria-label="Logout">Logout</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <header class="header">
      <h1>Offer a Ride</h1>
      <div class="header-actions">
        <button class="btn-notify" title="Notifications">
          <i class="fas fa-bell"></i>
        </button>
        <img src="../images/a.jpg" class="user-avatar" alt="User Avatar">
      </div>
    </header>

    <section class="offer-ride-intro">
      <p>
        Fill out the details below to offer a ride to your fellow KPU students.
        Once submitted, your ride will be visible to others in the community.
        You can cancel your ride anytime.
      </p>
    </section>
<section class="offer-ride-form"> 
  <form action="offer.php" method="POST" id="rideForm" class="rideForm" novalidate>
    <div class="form-group">
      <label for="from">Starting Point</label>
      <input type="text" id="startaddress" name="startaddress" required />
    </div>

    <div class="form-group">
      <label for="to">Destination</label>
      <input type="text" id="endaddress" name="endaddress" required />
    </div>

    <div class="form-group">
      <label for="date">Date</label>
      <input type="date" id="date" name="date" required />
    </div>

    <div class="form-group">
      <label for="time">Time</label>
      <input type="time" id="time" name="time" required />
    </div>

    <div class="form-group">
      <label for="seats">Available Seats</label>
      <input type="number" id="seats" name="seats" min="1" max="6" required />
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-action">Post Ride</button>
      <a href="dashboard.html" class="btn-cancel">Cancel</a>
    </div>
  </form>
</section>

  </main>

</body>
</html>
