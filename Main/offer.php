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
      <form id="rideForm">
        <div class="form-group">
          <label for="postalFrom">From (Postal Code)</label>
          <input
            type="text"
            id="postalFrom"
            name="postalFrom"
            placeholder="Enter starting postal code (e.g. V3W 1A1)"
            pattern="[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d"
            title="Enter a valid Canadian postal code"
            required
          />
        </div>

        <div class="form-group">
          <label for="postalTo">To (Postal Code)</label>
          <input
            type="text"
            id="postalTo"
            name="postalTo"
            placeholder="Enter destination postal code (e.g. V6T 1Z4)"
            pattern="[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d"
            title="Enter a valid Canadian postal code"
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
          <button type="submit" class="btn-action">Post Ride</button>
          <a href="dashboard.html" class="btn-cancel">Cancel</a>
        </div>
      </form>
    </section>

    <section class="rides-list">
      <h2>Your Offered Rides</h2>
      <ul id="ridesContainer"></ul>
    </section>
  </main>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // Get current logged-in user name
      let currentUserName = "Guest";
      const loggedIn = JSON.parse(localStorage.getItem("loggedInUser"));
      if (loggedIn && loggedIn.fullname) {
        currentUserName = loggedIn.fullname;
      } else {
        const savedProfile = JSON.parse(localStorage.getItem("rideShareProfile"));
        if (savedProfile && savedProfile.fullName) {
          currentUserName = savedProfile.fullName;
        } else {
          const savedSession = JSON.parse(localStorage.getItem("rideShareSession"));
          if (savedSession && savedSession.username) {
            currentUserName = savedSession.username;
          }
        }
      }

      const rideForm = document.getElementById("rideForm");
      const ridesContainer = document.getElementById("ridesContainer");

      let savedRides = JSON.parse(localStorage.getItem("rides") || "[]");

      // Save rides back to localStorage
      function saveRides() {
        localStorage.setItem("rides", JSON.stringify(savedRides));
      }

      // Render rides offered by current user
      function renderRides() {
        ridesContainer.innerHTML = "";
        const userRides = savedRides.filter(
          (ride) => ride.driver === currentUserName && ride.status === "offered"
        );
        if (userRides.length === 0) {
          ridesContainer.innerHTML = "<li>No rides offered yet.</li>";
          return;
        }

        userRides.forEach((ride) => {
          const li = document.createElement("li");
          li.className = "ride-item";
          li.dataset.id = ride.id;
         li.innerHTML = `
  <a href="details.html?id=${ride.id}" style="text-decoration: none; color: inherit;">
    <strong>${ride.from} ➡️ ${ride.to}</strong><br>
    Date: ${ride.date} | Time: ${ride.time} | Seats: ${ride.seats}<br>
    ${ride.notes ? `Notes: ${ride.notes}<br>` : ""}
    <em>Posted at: ${ride.postedAt}</em>
  </a><br>
  <button class="cancel-btn">Cancel Ride</button>
`;
          ridesContainer.appendChild(li);
        });
      }

      renderRides();


      // Validate postal code format
      function isValidPostalCode(pc) {
        return /^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/.test(pc);
      }

      // Handle form submit to add ride
      rideForm.addEventListener("submit", (e) => {
        e.preventDefault();

        const from = document.getElementById("postalFrom").value.trim().toUpperCase();
        const to = document.getElementById("postalTo").value.trim().toUpperCase();
        const date = document.getElementById("date").value;
        const time = document.getElementById("time").value;
        const seats = parseInt(document.getElementById("seats").value, 10);
        const notes = document.getElementById("notes").value.trim();

        // Validate postal codes
        if (!isValidPostalCode(from)) {
          alert("Please enter a valid FROM postal code (e.g., V3W 1A1).");
          return;
        }
        if (!isValidPostalCode(to)) {
          alert("Please enter a valid TO postal code (e.g., V6T 1Z4).");
          return;
        }

        if (!date || !time || !seats || seats < 1) {
          alert("Please fill all required fields correctly.");
          return;
        }

        const ride = {
          id: Date.now().toString(),
          from,
          to,
          date,
          time,
          seats,
          notes,
          postedAt: new Date().toLocaleString(),
          driver: currentUserName,
          status: "offered",
          passengers: []
        };

        savedRides.push(ride);
        saveRides();
        renderRides();

        alert("Your ride has been posted!");
        rideForm.reset();
      });

      // Cancel ride button
      ridesContainer.addEventListener("click", (e) => {
        if (e.target.classList.contains("cancel-btn")) {
          const li = e.target.closest("li");
          const rideId = li.dataset.id;

          const rideIndex = savedRides.findIndex(
            (r) => r.id === rideId && r.driver === currentUserName
          );
          if (rideIndex === -1) return;

          if (confirm("Are you sure you want to cancel this ride?")) {
            savedRides.splice(rideIndex, 1);
            saveRides();
            renderRides();
          }
        }
      });
    });
  </script>
</body>
</html>
