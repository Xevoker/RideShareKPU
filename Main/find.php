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
// ../CSS/find.css

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
        <button class="btn-notify" title="Notifications">
          <i class="fas fa-bell"></i>
        </button>
        <img src="a.jpg" class="user-avatar" alt="User Avatar" />
      </div>
    </header>

    <section class="search-section">
      <p>Search for a ride by entering starting and destination postal codes.</p>
      <div class="search-bar">
        <input
          type="text"
          id="postalFrom"
          placeholder="From postal code (e.g. V3W 1A1)"
          pattern="[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d"
          title="Enter a valid Canadian postal code"
          required
        />
        <input
          type="text"
          id="postalTo"
          placeholder="To postal code (e.g. V6T 1Z4)"
          pattern="[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d"
          title="Enter a valid Canadian postal code"
          required
        />
        <input type="time" id="searchTime" />
        <button id="searchBtn">
          <i class="fas fa-search"></i> Search
        </button>
      </div>
    </section>

    <section class="rides-list" id="ridesList">
      <!-- rides appear here -->
    </section>
  </main>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // Get current user
      let currentUserName = "Guest";
      const savedProfile = JSON.parse(localStorage.getItem("rideShareProfile"));
      if (savedProfile && savedProfile.fullName) {
        currentUserName = savedProfile.fullName;
      } else {
        const savedSession = JSON.parse(localStorage.getItem("rideShareSession"));
        if (savedSession && savedSession.username) {
          currentUserName = savedSession.username;
        }
      }

      const ridesList = document.getElementById("ridesList");
      const postalFromInput = document.getElementById("postalFrom");
      const postalToInput = document.getElementById("postalTo");
      const searchTimeInput = document.getElementById("searchTime");
      const searchBtn = document.getElementById("searchBtn");

      // Load rides and joined ride IDs
      let rides = JSON.parse(localStorage.getItem("rides") || "[]");
      let joinedRideIds = JSON.parse(localStorage.getItem("joinedRideIds") || "[]");

      function saveRides() {
        localStorage.setItem("rides", JSON.stringify(rides));
      }
      function saveJoinedRideIds() {
        localStorage.setItem("joinedRideIds", JSON.stringify(joinedRideIds));
      }

      // Helper: Validate postal code format (simple)
      function isValidPostalCode(pc) {
        return /^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/.test(pc);
      }

      // Display rides filtered or full list
      function displayRides(list) {
        ridesList.innerHTML = "";
        if (list.length === 0) {
          ridesList.innerHTML = `<p style="color:#990000;font-weight:600;">No rides found.</p>`;
          return;
        }
        list.forEach(ride => {
          const joinedThisRide = joinedRideIds.includes(ride.id);
          const rideCard = document.createElement("div");
          rideCard.className = "ride-card";
          rideCard.innerHTML = `
            <h3>${ride.from} ➡️ ${ride.to}</h3>
            <p><strong>Date:</strong> ${ride.date}</p>
            <p><strong>Time:</strong> ${ride.time}</p>
            <p><strong>Seats left:</strong> ${ride.seats}</p>
            <p><strong>Driver:</strong> ${ride.driver || "Unknown"}</p>
            ${ride.notes ? `<p><strong>Notes:</strong> ${ride.notes}</p>` : ""}
            <p class="posted-time"><em>Posted at: ${ride.postedAt || "N/A"}</em></p>
            <button class="btn-join" data-id="${ride.id}" ${joinedThisRide || ride.seats <= 0 ? "disabled" : ""}>
              ${joinedThisRide ? "Joined" : ride.seats <= 0 ? "Full" : "Join Ride"}
            </button>
          `;
          ridesList.appendChild(rideCard);
        });
      }

      // Show all rides initially
      displayRides(rides);

      // Search button click
      searchBtn.addEventListener("click", () => {
        const postalFrom = postalFromInput.value.trim().toUpperCase();
        const postalTo = postalToInput.value.trim().toUpperCase();
        const timeQuery = searchTimeInput.value.trim();

        // Validate postal codes
        if (!isValidPostalCode(postalFrom)) {
          alert("Please enter a valid FROM postal code (e.g. V3W 1A1).");
          return;
        }
        if (!isValidPostalCode(postalTo)) {
          alert("Please enter a valid TO postal code (e.g. V6T 1Z4).");
          return;
        }

        // Filter rides by postal codes (check if ride.from or ride.to includes these codes partially)
        // We can do a simple substring check; improve with more logic as needed
        const filtered = rides.filter(ride => {
          const fromMatch = ride.from.toUpperCase().includes(postalFrom);
          const toMatch = ride.to.toUpperCase().includes(postalTo);
          const timeMatch = timeQuery === "" || ride.time === timeQuery;

          return fromMatch && toMatch && timeMatch;
        });

        displayRides(filtered);
      });

      // Join a ride button
      ridesList.addEventListener("click", e => {
        if (e.target.classList.contains("btn-join") && !e.target.disabled) {
          const rideId = e.target.dataset.id;
          const rideIndex = rides.findIndex(r => r.id === rideId);
          if (rideIndex === -1) return;

          const ride = rides[rideIndex];

          if (joinedRideIds.includes(rideId)) {
            alert("You have already joined this ride.");
            return;
          }

          if (ride.seats > 0) {
            ride.seats = Number(ride.seats) - 1;

            if (!ride.passengers) ride.passengers = [];
            if (!ride.passengers.includes(currentUserName)) {
              ride.passengers.push(currentUserName);
            }

            joinedRideIds.push(rideId);
            saveJoinedRideIds();
            saveRides();
// Save ride details to localStorage for confirmation page
const confirmedRide = {
  from: ride.from,
  to: ride.to,
  date: ride.date,
  time: ride.time,
  driver: ride.driver || "Unknown"
};
localStorage.setItem("lastConfirmedRide", JSON.stringify(confirmedRide));

// Redirect to confirmation page
window.location.href = "confirmation.html";
            // Optionally redirect or update UI:
            e.target.textContent = "Joined";
            e.target.disabled = true;
          } else {
            alert("Sorry, no seats left for this ride!");
          }
        }
      });
    });
  </script>
</body>
</html>
