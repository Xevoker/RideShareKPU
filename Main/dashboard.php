<?php
include '../PHP/sessioncheck.php';
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
  $userName = $_SESSION['firstName'];

include '../PHP/db.php'; 

$carpoolID = $_SESSION['carpoolID'] ?? null;

$stmt = $conn->prepare("SELECT originAddress, destinationAddress FROM CARPOOL WHERE carpoolID = :cid");
$stmt->execute([':cid' => $carpoolID]);
$carpool = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$carpool) {
    echo "Carpool not found, Join a Carpool to See the Route";
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
<script src="../Js/dashboard.js"></script>
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

        document.getElementById("route-info").innerText =
          `Total Distance: ${(totalDistance / 1000).toFixed(2)} km | Total Time: ${(totalDuration / 60).toFixed(1)} mins`;
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

    
    