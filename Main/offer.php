<?php
//Session start and database
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: ../Access/login.php");
    exit();
}
include '../PHP/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
  //Variables
  $driverID = $_SESSION['userID'] ?? null;
  $originAddress = trim($_POST['startaddress'] ?? '');
  $destinationAddress = trim($_POST['endaddress'] ?? '');
  $departureDate = $_POST['date'] ?? '';
  $departureTime = $_POST['time'] ?? '';
  $notes = $_POST['notes'] ?? null;
  $availableSeats = intval($_POST['seats'] ?? 0);
  $status = "offered";
  
  // Inserts into table
  if ($driverID && $originAddress && $destinationAddress && $departureDate && $departureTime && $availableSeats > 0) {

      $sql = "INSERT INTO CARPOOL 
              (driverID, originAddress, destinationAddress, departureDate, departureTime, availableSeats, status, notes) 
              VALUES 
              (:driverID, :originAddress, :destinationAddress, :departureDate, :departureTime, :availableSeats, :status, :notes)";

      $stmt = $conn->prepare($sql);
      if (!$stmt) {
          $errorInfo = $conn->errorInfo();
          die("Error preparing SQL statement: " . implode(" | ", $errorInfo));
      }

      $result = $stmt->execute([
          ':driverID'         => $driverID,
          ':originAddress'    => $originAddress,
          ':destinationAddress' => $destinationAddress,
          ':departureDate'    => $departureDate,
          ':departureTime'    => $departureTime,
          ':availableSeats'   => $availableSeats,
          ':status'           => $status,
          ':notes'         => $notes
      ]);

      $carpoolID = $conn->lastInsertId();

      $_SESSION['carpoolID'] = $carpoolID;

      echo "Ride posted successfully.";

  } else {
      echo "Missing required fields or invalid data.";
  }
}
// Display your offerred rides
$offeredRides = [];
$driverID = $_SESSION['userID'];
$sql = "SELECT * FROM CARPOOL WHERE driverID = :driverID AND status = 'offered' ORDER BY departureDate, departureTime";
$stmt = $conn->prepare($sql);
$stmt->execute([':driverID' => $driverID]);
$offeredRides = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Deletes ride when cancelleds
if (isset($_POST['cancel'])) {
    $cancelID = intval($_POST['cancel']);
    $driverID = $_SESSION['userID'];

    $stmt = $conn->prepare("DELETE FROM CARPOOL WHERE carpoolID = :id AND driverID = :driverID");
    $stmt->execute([
        ':id' => $cancelID,
        ':driverID' => $driverID
    ]);
     header("Location: offer.php");
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
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    /* Simple styling for inputs and form */
    body { font-family: Arial, sans-serif; background: #f9f9f9; margin: 0; padding: 0; }
    main { max-width: 600px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgb(0 0 0 / 0.1);}
    .form-group { margin-bottom: 15px; }
    label { display: block; font-weight: bold; margin-bottom: 6px; }
    input[type="text"], input[type="date"], input[type="time"], input[type="number"], textarea {
      width: 100%; padding: 8px; box-sizing: border-box; border-radius: 4px; border: 1px solid #ccc;
      font-size: 1rem;
    }
    textarea { resize: vertical; min-height: 60px; }
    .form-actions { display: flex; gap: 10px; }
    button.btn-action {
      background: #e60023; color: white; border: none; padding: 10px 20px; cursor: pointer; font-size: 1rem; border-radius: 4px;
    }
    button.btn-action:hover { background: #b8001c; }
    a.btn-cancel {
      display: inline-block; padding: 10px 20px; background: #777; color: white; text-decoration: none; border-radius: 4px;
      font-size: 1rem; text-align: center;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="logo-container">
      <img src="../images/kpu.jpg" class="logo-img" alt="Logo" />
      <h2>Ride<span>Share</span></h2>
    </div>
    <nav>
      <ul>
        <li><a href="dashboard.php"aria-label="Dashboard">Dashboard</a></li>
        <li><a href="join.php" aria-label="Join Ride">Join Ride</a></li>
        <li><a href="offer.php" aria-label="Offer Ride">Offer Ride</a></li>
        <li><a href="find.php" aria-label="Find Ride">Find Ride</a></li>
        <li><a href="profile.php" aria-label="Profile">Profile</a></li>
        <li><a href="messages.php" aria-label="Messages">Messages</a></li>
        <li><a href="ride history.php" aria-label="Ride History">Ride History</a></li>
        <li><a href="feedback.php" aria-label="User Feedback">Feedback</a></li>
        <li><a href="settings.php"aria-label="Settings">Settings</a></li>
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
      </div>
    </header>

    <section class="offer-ride-intro">
      <p>
        Fill out the details below to offer a ride. You can offer a ride between any two postal codes,
        whether from home to KPU, campus to campus, or anywhere else!
        Your ride will be visible to others once posted. You can cancel anytime.
      </p>
    </section>

    <section class="offer-ride-form">
      <form id="rideForm" method="POST" action="">
        <label for="startaddress">From (Address)</label>
        <input
            type="text"
            id="startaddress"
            name="startaddress"
            placeholder="Enter starting address"
            required
          />
        </div>

        <div class="form-group">
          <label for="endaddress">To (Address)</label>
          <input
            type="text"
            id="endaddress"
            name="endaddress"
            placeholder="Enter destination address"
            required
          />
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

        <div class="form-group">
          <label for="notes">Notes (optional)</label>
          <textarea
            id="notes"
            name="notes"
            placeholder="Any special instructions?"
          ></textarea>
        </div>

        <div class="form-actions">
          <button type="submit" name="submit" class="btn-action">Post Ride</button>
          <a href="dashboard.php" class="btn-cancel">Cancel</a>
        </div>
      </form>
    </section>

    <section class="rides-list">
      <h2>Your Offered Rides</h2>
      <ul id="ridesContainer">
      <?php if (empty($offeredRides)): ?>
          <li>No rides offered yet.</li>
        <?php else: ?>
          <?php foreach ($offeredRides as $ride): ?>
            <div style="background-color: white;">
              <strong><?= htmlspecialchars($ride['originAddress']) ?> ➡️ <?= htmlspecialchars($ride['destinationAddress']) ?></strong><br>
              Date: <?= htmlspecialchars($ride['departureDate']) ?> | Time: <?= htmlspecialchars($ride['departureTime']) ?> | Seats: <?= htmlspecialchars($ride['availableSeats']) ?><br>
              <?php if (!empty($ride['notes'])): ?>
                Notes: <?= htmlspecialchars($ride['notes']) ?><br>
              <?php endif; ?>
              <em>Posted on: <?= htmlspecialchars($ride['createdDate']) ?></em>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="cancel" value="<?= $ride['carpoolID'] ?>">
                <button type="submit" onclick="return confirm('Are you sure you want to cancel this ride?');">
                Cancel Ride
                </button>
              </form>
              </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </section>
  </main>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD1J-oGI6bSoPOCz_Gu8hYl_spxzgY7-EM&libraries=places"></script>
  <script>
      document.addEventListener("DOMContentLoaded", () => {
      let startAutocomplete, endAutocomplete;

      // Google Places Autocomplete
      function initAutocomplete() {
          startAutocomplete = new google.maps.places.Autocomplete(
              document.getElementById("startaddress"),
              { types: ["geocode"],
                componentRestrictions: { country: "ca" } 
              }
          );
          endAutocomplete = new google.maps.places.Autocomplete(
              document.getElementById("endaddress"),
              { types: ["geocode"],
                componentRestrictions: { country: "ca" }
              }
          );
      }
      initAutocomplete();
  });
  </script>
</body>
</html>
