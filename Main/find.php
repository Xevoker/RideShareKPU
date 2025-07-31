<?php
session_start();
require '../PHP/db.php'; // connection

if (!isset($_SESSION['userID'])) {
    header("Location: ../Access/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['search_destination'] = $_POST['searchLocation'] ?? '';
    $_SESSION['search_time'] = $_POST['searchtime'] ?? '';
    $_SESSION['pickup_place'] = $_POST['pickupLocation'] ?? '';
    header("Location: find.php");
    exit();
}

$userID = $_SESSION['userID'];
$destinationSearch = $_SESSION['search_destination'] ?? '';
$timeSearch = $_SESSION['search_time'] ?? '';
$rides = [];

// Search rides by destination
if (!empty($destinationSearch)) {
    $searchTerm = '%' . $destinationSearch . '%';
    $stmt = $conn->prepare("SELECT * FROM CARPOOL WHERE destinationAddress LIKE :search AND availableSeats > 0");
    $stmt->execute([':search' => $searchTerm]);
    $rides = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Find a Ride – RideShare</title>
  <link rel="stylesheet" href="../CSS/find.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="logo-container">
      <img src="../images/a.jpg" class="logo-img" alt="Logo" />
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
      <h1>Find a Ride</h1>
      <div class="header-actions">
        <button class="btn-notify" title="Notifications"><i class="fas fa-bell"></i></button>
        <img src="../images/a.jpg" class="user-avatar" alt="User Avatar" />
      </div>
    </header>

    <section class="search-section">
      <p>Search for available rides offered by fellow KPU students. Enter a starting point, destination, or time to filter.</p>
      <form method="post" action="find.php" class="search-bar" id="search-bar">
        <input type="text" name="pickupLocation" id="pickupLocation" placeholder="Insert your Pickup Location" />
        <input type="text" name="searchLocation" id="searchLocation" placeholder="Search by destination..." />
        <input type="time" name="searchTime" id="searchTime" />
        <button type="submit" id="searchBtn"><i class="fas fa-search"></i> Search</button>
      </form>
    </section>

    <section class="rides-list" id="ridesList">
      <?php if (count($rides) === 0): ?>
        <p>No rides found for "<?= htmlspecialchars($destinationSearch) ?>".</p>
      <?php else: ?>
        <?php foreach ($rides as $ride): ?>
          <div class="ride-card">
            <h3><?= htmlspecialchars($ride['originAddress']) ?> ➡ <?= htmlspecialchars($ride['destinationAddress']) ?></h3>
            <p><strong>Date:</strong> <?= $ride['departureDate'] ?></p>
            <p><strong>Departure Time:</strong> <?= $ride['departureTime'] ?></p>
            <p><strong>Seats:</strong> <?= $ride['availableSeats'] ?></p>
            <form method="post" action="../PHP/join_handler.php">
              <input type="hidden" name="carpoolID" value="<?= $ride['carpoolID'] ?>">
              <button type="submit">Join Ride</button>
            </form>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </main>

</body>
</html>
