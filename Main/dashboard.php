<?php
//Session start and database
include '../PHP/sessioncheck.php';
  $userName = $_SESSION['firstName'];
include '../PHP/db.php'; 

$carpoolID = $_SESSION['carpoolID'] ?? null;
$userID = $_SESSION['userID'];
// Get the addresses for the map
$stmt = $conn->prepare("SELECT originAddress, destinationAddress FROM CARPOOL WHERE carpoolID = :cid");
$stmt->execute([':cid' => $carpoolID]);
$carpool = $stmt->fetch(PDO::FETCH_ASSOC);

//Upcoming
$sqlup = "SELECT COUNT(*) AS total FROM CARPOOL 
        WHERE driverID = :userID AND status = 'offered'";
$stmt = $conn->prepare($sqlup);
$stmt->execute([':userID' => $userID]);
$row1 = $stmt->fetch(PDO::FETCH_ASSOC);

$upcomingRides = $row1['total'];

// Offered 
$sqloff = "SELECT COUNT(*) AS total FROM CARPOOL 
        WHERE driverID = :userID AND status = 'complete'";
$stmt = $conn->prepare($sqloff);
$stmt->execute([':userID' => $userID]);
$row2 = $stmt->fetch(PDO::FETCH_ASSOC);

$ridesOffered = $row2['total'] ?? 0;

//taken
$sqlta = "SELECT COUNT(*) AS total 
        FROM RIDE
        WHERE  riderID = :userID";
$stmt = $conn->prepare($sqlta);
$stmt->execute([':userID' => $userID]);
$row3 = $stmt->fetch(PDO::FETCH_ASSOC);

$ridesTaken = $row3['total'] ?? 0;

//Grabs the pickup locations
if (!$carpool) {
}
else{
$stmt = $conn->prepare("SELECT pickupLocation FROM RIDE WHERE carpoolID = :cid");
$stmt->execute([':cid' => $carpoolID]);
$pickups = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard – RideShare KPU</title>
  <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600;700&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="../CSS/dashboard.css">
<script src="dashboard.js"></script>
<style>
    #map {
      height: 600px;
      width: 100%;
      margin-top: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    #route-info {
      margin-top: 10px;
      font-weight: 600;
    }
  </style>
  
</head>
<body>

  <aside class="sidebar">
    <div class="logo-container">
      <img src="../images/kpu.jpg" alt="KPU Logo" class="logo-img" />
      <h2>RideShare <span>KPU</span></h2>
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

    <!-- Header -->
    <header class="header">
      <h1>Welcome back, <?php echo htmlspecialchars($userName) ?></span>! 👋</h1>
      <div class="header-actions">
        <button class="btn-notify" title="Notifications">🔔</button>
        <img src="../images/a.jpg" alt="User Avatar" class="user-avatar" />
      </div>
    </header>
    <?php
    if (!$carpool) {
    echo "Carpool not found, Join a Carpool to See the Route";
    }
    ?>
    <div id="map"></div>
    <p id="route-info"></p>
   <section class="stats-cards">
      <div class="card">
        <h3>Upcoming Rides</h3>
        <p class="number"><?php echo $upcomingRides ?></p>
      </div>
      <div class="card">
        <h3>Rides Offered</h3>
        <p class="number"><?php echo $ridesOffered ?></p>
      </div>
      <div class="card">
        <h3>Rides Taken</h3>
        <p class="number"><?php echo $ridesTaken ?></p>
      </div>
  

    <!-- Upcoming Rides Table -->
    <section class="rides-section">
      <h2>📅 Upcoming Rides</h2>
      <table class="rides-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>From</th>
            <th>To</th>
            <th>Driver</th>
            <th>Seats</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>July 20, 2025</td>
            <td>Surrey Campus</td>
            <td>Langley Campus</td>
            <td>Alex M.</td>
            <td>2</td>
            <td><span class="status confirmed">Confirmed</span></td>
            <td><a href="details.php?rideId=1" class="btn-details">Details</a></td>
          </tr>
          <tr>
            <td>July 23, 2025</td>
            <td>Langley Campus</td>
            <td>Surrey Campus</td>
            <td>Jamie L.</td>
            <td>1</td>
            <td><span class="status pending">Pending</span></td>
            <td><a href="details.php?rideId=2" class="btn-details">Details</a></td>
          </tr>
          <tr>
            <td>July 27, 2025</td>
            <td>Surrey Campus</td>
            <td>Richmond Campus</td>
            <td>Sarah P.</td>
            <td>3</td>
            <td><span class="status cancelled">Cancelled</span></td>
            <td><a href="details.php?rideId=3" class="btn-details">Details</a></td>
          </tr>
        </tbody>
      </table>
    </section>

    <section class="live-stats">
      <h2>📈 Live Ride Stats</h2>
      <div class="live-stats-grid">
        <div class="stat-item">
          <h4>Total Active Drivers</h4>
          <p>128</p>
        </div>
        <div class="stat-item">
          <h4>Available Seats Today</h4>
          <p>452</p>
        </div>
        <div class="stat-item">
          <h4>Avg Ride Rating</h4>
          <p>⭐ 4.8 / 5</p>
        </div>
      </div>
    </section>

  <!-- With this -->
<section class="environmental-impact">
  <h2>🌍 Environmental Impact</h2>
  <p>Since launching, RideShare KPU community has saved:</p>
  <ul>
    <li><strong>18,000 kg</strong> of CO₂ emissions</li>
    <li><strong>7,500</strong> liters of fuel</li>
    <li><strong>4,200</strong> car trips avoided</li>
  </ul>
  <p>Thank you for helping make our campuses greener!</p>
</section>

    <!-- NEW: Popular Destinations -->
    <section class="popular-destinations">
      <h2>📍 Popular Destinations</h2>
      <ul>
        <li>Surrey ➝ Langley</li>
        <li>Richmond ➝ Surrey</li>
        <li>Langley ➝ Civic Plaza</li>
        <li>Surrey ➝ Cloverdale</li>
      </ul>
    </section>

    <!-- NEW: Community Highlights -->
    <section class="community-highlights">
      <h2>🌟 Community Highlights</h2>
      <p>“We saved 12,000 kg of CO₂ this semester through carpooling!”</p>
      <p>“Top driver Alex M. just completed 100 rides this year!”</p>
      <p>“Thank you to all drivers participating in Sustainability Week.”</p>
    </section>

    <!-- Announcements -->
    <section class="announcements">
      <h2>📰 Announcements</h2>
      <ul>
        <li><strong>🚦 Traffic Update:</strong> Expect delays on Hwy 91 this week.</li>
        <li><strong>🌱 Sustainability Week:</strong> Earn bonus points for carpooling!</li>
        <li><strong>🔒 Safety Tip:</strong> Always verify your ride details before departure.</li>
      </ul>
    </section>
<!-- Recent Activity -->
<section class="recent-activity">
  <h2>📌 Recent Activity</h2>
  <div class="activity-list" id="recentActivityList"></div>
</section>

    <!-- Stats Graph -->
    <section class="stats-graph">
      <h2>📈 Ride Activity Overview</h2>
      <div class="chart-placeholder">
        <p>Graph/chart coming soon! 📊</p>
      </div>
    </section>

    <!-- Leaderboard -->
    <section class="leaderboard">
      <h2>🏆 Top Drivers This Month</h2>
      <ol>
        <li><strong>Jamie L.</strong> — 24 rides offered</li>
        <li><strong>Alex M.</strong> — 19 rides offered</li>
        <li><strong>Sarah P.</strong> — 15 rides offered</li>
        <li><strong>Chris B.</strong> — 12 rides offered</li>
        <li><strong>Priya K.</strong> — 9 rides offered</li>
      </ol>
    </section>

    <!-- User Feedback -->
    <section class="user-feedback">
      <h2>💬 Recent Feedback</h2>
      <div class="feedback-list">
        <div class="feedback-item">
          <p>“Alex was a great driver, punctual and friendly!” — <em>Jamie L.</em></p>
        </div>
        <div class="feedback-item">
          <p>“Loved the ride, very comfortable and safe.” — <em>Sarah P.</em></p>
        </div>
        <div class="feedback-item">
          <p>“Thanks for the quick response and flexibility!” — <em>Priya K.</em></p>
        </div>
      </div>
    </section>

    <!-- Tips & Resources -->
    <section class="tips-resources">
      <h2>🛡 Ride Safety Tips & Resources</h2>
      <ul>
        <li>Always confirm your driver's identity before the ride.</li>
        <li>Share your trip details with a trusted friend or family member.</li>
        <li>Keep emergency contacts handy on your phone.</li>
        <li>Use seat belts at all times.</li>
        <li>Report any suspicious behavior immediately.</li>
      </ul>
      <a href="learn.php" class="btn-learn-more">Learn More</a>
    </section>

    <!-- Upcoming Events -->
    <section class="events">
      <h2>🎉 Upcoming Events & Promotions</h2>
      <ul>
        <li><strong>Aug 1-7:</strong> Sustainability Challenge Week — Earn extra points!</li>
        <li><strong>Aug 15:</strong> Safety Workshop Webinar (Online)</li>
        <li><strong>Sep 1:</strong> Fall Semester RideShare Meetup</li>
      </ul>
    </section>

    <!-- New: FAQs Section -->
    <section class="faqs">
      <h2>❓ Frequently Asked Questions</h2>
      <dl>
        <dt>How do I join a ride?</dt>
        <dd>Go to the 'Join Ride' page and browse available rides, then request to join.</dd>
        <dt>Can I offer rides as a driver?</dt>
        <dd>Yes! Use the 'Offer Ride' page to create a ride listing for others.</dd>
        <dt>How do I contact other users?</dt>
        <dd>You can message users via the 'Messages' section in the dashboard.</dd>
        <dt>What if I need to cancel a ride?</dt>
        <dd>Navigate to your ride details and choose the cancel option.</dd>
      </dl>
    </section>

    <!-- Footer -->
    <footer class="dashboard-footer">
      &copy; 2025 RideShare KPU — <a href="terms.php">Terms of Service</a> | <a href="privacy.php">Privacy Policy</a>
      <div class="social-links">
        <a href="#" aria-label="Facebook">👍</a>
        <a href="#" aria-label="Twitter">🐦</a>
        <a href="#" aria-label="Instagram">📸</a>
      </div>
    </footer>
</main>
<?php if ($carpoolID): ?>
  <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD1J-oGI6bSoPOCz_Gu8hYl_spxzgY7-EM&callback=initMap"
    async defer>
  </script>
<?php else: ?>
  <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD1J-oGI6bSoPOCz_Gu8hYl_spxzgY7-EM"
    async defer>
  </script>
<?php endif; ?>
<script>
  let geocoder;
class RouteMap {

    // sets up properties
      constructor(destination, waypoints = [], options = {}) {
        this.destination = destination;
        this.waypoints = waypoints.map(loc => ({ location: loc, stopover: true }));
        this.options = options;
        this.map = null;
        this.directionsService = new google.maps.DirectionsService();
        this.directionsRenderer = new google.maps.DirectionsRenderer();
      }

      // creates a new map at the orgin
      initMap(origin) {
        this.map = new google.maps.Map(document.getElementById("map"), {
          center: origin,
          zoom: 7
        });
        this.directionsRenderer.setMap(this.map);
        this.calculateRoute(origin);
      }

      // calculates route using the inpured information
      calculateRoute(origin) {
        const request = {
          origin: origin,
          destination: this.destination,
          waypoints: this.waypoints,
          optimizeWaypoints: this.options.optimize || false,
          travelMode: google.maps.TravelMode.DRIVING,
          avoidTolls: this.options.avoidTolls || false,
          avoidHighways: this.options.avoidHighways || false
        };

        this.directionsService.route(request, (result, status) => {
          if (status === "OK") {
            this.directionsRenderer.setDirections(result);
            this.displayRouteInfo(result);
          } else {
            alert("Could not display directions due to: " + status);
          }
        });
      }

      // Info calculation
      displayRouteInfo(result) {
        const route = result.routes[0];
        let totalDistance = 0;
        let totalDuration = 0;

        route.legs.forEach(leg => {
          totalDistance += leg.distance.value;
          totalDuration += leg.duration.value;
        });

        const totalDistanceKM = totalDistance/1000;
        const gasCostPerKm = 0.15;
        const totalCost = totalDistanceKM * gasCostPerKm;
        const numPeople = this.waypoints.length + 1;
        const pricePerPerson = (totalCost / numPeople).toFixed(2);
        
        document.getElementById("route-info").innerText =
          `Total Distance: ${totalDistanceKM.toFixed(2)} km | Total Time: ${(totalDuration / 60).toFixed(1)} mins | Cost per person: $${pricePerPerson}`;
      }
    }
    // Converts address string to LatLng using Geocoder, returns Promise
    function geocodeAddress(address) {
    return new Promise((resolve, reject) => {
        geocoder.geocode({ address: address }, (results, status) => {
        if (status === "OK" && results[0]) {
            resolve(results[0].geometry.location);
        } else {
            reject(`Geocode failed for ${address}: ${status}`);
        }
        });
    });
    }

      async function initMap() {
        geocoder = new google.maps.Geocoder();

        const originAddress = <?= json_encode($carpool['originAddress'] ?? '') ?>;
        const destinationAddress = <?= json_encode($carpool['destinationAddress'] ?? '') ?>;
        const pickupLocations = <?= json_encode($pickups ?? []) ?>;

        if (!originAddress || !destinationAddress) {
            alert("Missing origin or destination address.");
            return;
        }
        console.log("Origin:", originAddress);
        console.log("Destination:", destinationAddress);
        console.log("Stops:", pickupLocations);
        pickupLocations.forEach((loc, i) => {
          geocodeAddress(loc).then(pos => {
              console.log(`Stop ${i+1} (${loc}) is valid:`, pos.toString());
          }).catch(err => {
              console.warn(`Invalid stop: ${loc}`, err);
          });
      });
        try {
            const originLatLng = await geocodeAddress(originAddress);
            const destinationLatLng = await geocodeAddress(destinationAddress);

            const stops = pickupLocations.map(loc => loc.trim()).filter(loc => loc.length > 0);

            const routeMap = new RouteMap(destinationLatLng, stops, { optimize: true });
            routeMap.initMap(originLatLng);
        } catch (error) {
            alert(error);
            console.error(error);
        }
    }
  </script>
</body>

    
    