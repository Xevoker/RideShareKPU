<?php
//Session start and database
session_start();
require '../PHP/db.php';
if (!isset($_SESSION['userID'])) {
    header("Location: ../Access/login.php");
    exit();
}

// Search avaliable rides
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
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
  />
  <style>
    /* Simple minimal styling for demo */
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f9f9f9; }
    main { padding: 20px; max-width: 800px; margin: auto; }
    .search-bar {
      display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px;
    }
    input[type="text"], input[type="time"] {
      padding: 8px; flex: 1 1 150px; font-size: 1rem;
    }
    button#searchBtn {
      background: #e60023; color: white; border: none; padding: 8px 20px;
      font-size: 1rem; cursor: pointer; border-radius: 4px;
      display: flex; align-items: center; gap: 6px;
    }
    button#searchBtn:hover {
      background: #b8001c;
    }
    .ride-card {
      background: white; padding: 15px; margin-bottom: 12px; border-radius: 6px;
      box-shadow: 0 2px 5px rgb(0 0 0 / 0.1);
    }
    .ride-card h3 { margin: 0 0 8px 0; }
    .ride-card p { margin: 4px 0; }
    .btn-join {
      margin-top: 8px;
      background: #007bff;
      color: white;
      border: none;
      padding: 8px 14px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 1rem;
    }
    .btn-join:disabled {
      background: #888;
      cursor: default;
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
      <h1>Find a Ride</h1>
      <div class="header-actions">
        <button class="btn-notify" title="Notifications">
          <i class="fas fa-bell"></i>
        </button>
        <img src="a.jpg" class="user-avatar" alt="User Avatar" />
      </div>
    </header>

    <section class="search-section">
      <p>Search for a ride by entering starting and destination postal codes.</p>
      <form method="post" action="find.php" class="search-bar" id="search-bar">
        <input
          type="text"
          id="pickupLocation"
          name="pickupLocation"
          placeholder="Enter pickup location"
          list="address-list"
          required
        />
        <datalist id="address-list">
            <?php
            $stmt = $conn->prepare("SELECT address FROM ADDRESS WHERE userID = :userID");
            $stmt->execute([':userID' => $_SESSION['userID']]);
            $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($addresses as $row) {
                echo '<option value="' . htmlspecialchars($row['address']) . '">';
            }
            ?>
          </datalist>
        <input
          type="text"
          id="searchLocation"
          name="searchLocation"
          placeholder="Enter destination"
          required
        />
        <input
          type="time"
          id="searchtime"
          name="searchtime"
        />
        <button id="searchBtn">
          <i class="fas fa-search"></i> Search
        </button>
  </form>
    </section>

    <section class="rides-list" id="ridesList"> 
      <?php if (count($rides) === 0): ?> 
        <p>No rides found<?= htmlspecialchars($destinationSearch) ?></p> 
          <?php else: ?> 
            <?php foreach ($rides as $ride): ?> 
              <div class="ride-card"> 
                <h3><?= htmlspecialchars($ride['originAddress']) ?> -> <?= htmlspecialchars($ride['destinationAddress']) ?></h3> 
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

  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD1J-oGI6bSoPOCz_Gu8hYl_spxzgY7-EM&libraries=places"></script>
  <script>
    //Google autocomplete
    function initAutocomplete() {
      const pickupInput = document.getElementById("pickupLocation");
      const destInput = document.getElementById("searchLocation");

      new google.maps.places.Autocomplete(pickupInput, 
      { 
        types: ["geocode"],
        componentRestrictions: { country: "ca" }
      });
      new google.maps.places.Autocomplete(destInput, 
      { types: ["geocode"], 
        componentRestrictions: { country: "ca" }
      });
    }

    window.addEventListener("load", () => {
      if (typeof google !== "undefined" && google.maps && google.maps.places) {
        initAutocomplete();
      }
    });
  </script>
</body>
</html>
