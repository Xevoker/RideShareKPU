<?php
require '../PHP/sessioncheck.php';
require '../PHP/db.php';

if (empty($_SESSION['carpoolID'])) {
    die("You cannot leave a review because you have not actively participating in a ride.");
}

$carpoolID = $_SESSION['carpoolID'];
$userID    = $_SESSION['userID'];

$stmt = $conn->prepare("SELECT driverID FROM CARPOOL WHERE carpoolID = :cid");
$stmt->execute([':cid' => $carpoolID]);
$driver = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$driver) {
    die("Invalid carpool ID.");
}

$driverID = $driver['driverID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $feedbackText = trim($_POST['feedbackText'] ?? '');
    $rating       = intval($_POST['rating'] ?? 0);
    $userName     = trim($_POST['userName'] ?? '');

    if ($feedbackText && $rating >= 1 && $rating <= 5 && $userName) {
        $insert = $conn->prepare("
            INSERT INTO RATE (carpoolID, driverID, userID, feedback, rating, reviewerName)
            VALUES (:carpoolID, :driverID, :userID, :feedback, :rating, :reviewerName)
        ");
        $insert->execute([
            ':carpoolID'   => $carpoolID,
            ':driverID'    => $driverID,
            ':userID'      => $userID,
            ':feedback'    => $feedbackText,
            ':rating'      => $rating,
            ':reviewerName'=> $userName
        ]);

        echo "<script>alert('Thank you for your feedback!');</script>";
        header("Location: feedback.php");
    } else {
        echo "<script>alert('Please fill in all fields and select a valid rating.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>User Feedback – RideShare KPU</title>
  <link rel="stylesheet" href="../CSS/feedback.css" />
</head>
<body>
  <aside class="sidebar">
    <div class="logo-container">
      <img src="../images/kpu.jpg" alt="RideShare KPU Logo" class="logo-img" />
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

  <main class="main-content">
    <header class="header">
      <h1>User <span class="user-name">Feedback</span></h1>
      <div class="header-actions">
        <button class="btn-notify" title="Notifications" aria-label="Notifications">🔔</button>
        <img src="../images/user-avatar.jpg" alt="User Avatar" class="user-avatar" />
      </div>
    </header>

    <section class="user-feedback">
      <h2>Give Your Feedback</h2>

      <form id="feedbackForm" method="post" action="" aria-label="Feedback form">
        <label for="feedbackText">Your Feedback</label>
        <textarea id="feedbackText" name="feedbackText" rows="6" placeholder="Write your feedback here..." required></textarea>

        <label for="rating">Your Rating</label>
        <div class="rating" role="radiogroup" aria-label="Rating">
          <input type="radio" id="star5" name="rating" value="5" required />
          <label for="star5" title="5 stars">★</label>
          <input type="radio" id="star4" name="rating" value="4" />
          <label for="star4" title="4 stars">★</label>
          <input type="radio" id="star3" name="rating" value="3" />
          <label for="star3" title="3 stars">★</label>
          <input type="radio" id="star2" name="rating" value="2" />
          <label for="star2" title="2 stars">★</label>
          <input type="radio" id="star1" name="rating" value="1" />
          <label for="star1" title="1 star">★</label>
        </div>

        <label for="userName">Your Name</label>
        <input type="text" id="userName" name="userName" placeholder="Enter your name" required />

        <button type="submit"  name="submit" class="btn-submit">Submit Feedback</button>
      </form>

      <div class="thank-you-message" role="alert" aria-live="polite" style="display:none;">
        Thank you for your feedback!
      </div>
    </section>

    <footer class="dashboard-footer">
      <p>© 2025 RideShare KPU. All rights reserved.</p>
      <div class="social-links" aria-label="Social Media Links">
        <a href="#" title="Facebook" aria-label="Facebook">Facebook</a> |
        <a href="#" title="Twitter" aria-label="Twitter">Twitter</a> |
        <a href="#" title="Instagram" aria-label="Instagram">Instagram</a>
      </div>
    </footer>
  </main>

  <script>
    const form = document.getElementById('feedbackForm');
    const thankYou = document.querySelector('.thank-you-message');

    form.addEventListener('submit', (e) => {
      // Collect form data
      const feedback = form.feedbackText.value.trim();
      const rating = document.querySelector('input[name="rating"]:checked')?.value || '';
      const userName = form.userName.value.trim();

      if (!feedback || !rating || !userName) {
        alert("Please fill out all fields and select a rating.");
        e.preventDefault();
        return;
      }
    });
  </script>
</body>
</html>
