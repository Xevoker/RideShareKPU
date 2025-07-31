<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: ../Access/login.php");
    exit();
}
include '../PHP/db.php';

  $stmt = $conn->prepare("SELECT userID, studentID, firstName, lastName, email, userType, licenseNumber, street, city, postalCode, preferences, dateJoined, isActive, averageRating, createdAt, updatedAt, userPassword
  FROM USER WHERE userID = :userID");
  if (!$stmt) {
        $errorInfo = $conn->errorInfo();
        die("Error preparing SQL statement: " . implode(" | ", $errorInfo));
    }
    $stmt->execute([':userID' => $_SESSION['userID']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

  $stmt2 = $conn->prepare("SELECT phoneNumber
  FROM PHONE WHERE userID = :userID");
  if (!$stmt2) {
        $errorInfo = $conn->errorInfo();
        die("Error preparing SQL statement: " . implode(" | ", $errorInfo));
    }
    $stmt2->execute([':userID' => $_SESSION['userID']]);
    $user2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    $userID         = $user['userID'];
    $studentID      = $user['studentID'];
    $firstName      = $user['firstName'];
    $lastName       = $user['lastName'];
    $phoneNumber    = $user2['phoneNumber'];
    $email          = $user['email'];
    $userType       = $user['userType'];
    $licenseNumber  = $user['licenseNumber'];
    $street         = $user['street'];
    $city           = $user['city'];
    $postalCode     = $user['postalCode'];
    $preferences    = $user['preferences'];
    $dateJoined     = $user['dateJoined'];
    $isActive       = $user['isActive'];
    $averageRating  = $user['averageRating'];
    $createdAt      = $user['createdAt'];
    $updatedAt      = $user['updatedAt'];
    $userPassword   = $user['userPassword'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>User Profile – RideShare</title>
  <link rel="stylesheet" href="../CSS/profile.css" />
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    /* Hide the default file input */
    #profilePicInput {
      display: none;
    }
    /* Cursor pointer on profile image to indicate clickable */
    #profileImage {
      cursor: pointer;
      transition: transform 0.3s ease;
    }
    #profileImage:hover {
      transform: scale(1.05);
      box-shadow: 0 0 15px #cc3333aa;
    }
  </style>
</head>
<body>
  <!-- SIDEBAR -->
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

  <!-- MAIN -->
  <main class="main-content">
    <header class="header">
      <h1>Profile <?php echo htmlspecialchars($firstName) ?> <?php echo htmlspecialchars($lastName) ?></span></h1>
      <div class="header-actions">
        <button class="btn-notify" title="Notifications"><i class="fas fa-bell"></i></button>
        <img src="../images/a.jpg" class="user-avatar" alt="User Avatar" />
      </div>
    </header>

    <div id="deactivated-banner" class="deactivated-banner">Your account is deactivated. Reactivate in Settings.</div>

    <!-- PROFILE CARD -->
    <section class="profile-card">
      <img src="../images/a.jpg" alt="User Avatar" class="profile-img" id="profileImage" title="Click to change profile picture" />
      <input type="file" id="profilePicInput" accept="image/*" />

      <div class="profile-details">
        <h2 class="profile-name"><?php echo htmlspecialchars($firstName) ?> <?php echo htmlspecialchars($lastName) ?></h2>
        <p class="profile-role">🚗 Verified Driver & Community Helper</p>
        <button id="saveBioBtn" class="btn-action">Save Bio</button>
      </div>
    </section>

    <!-- PERSONAL INFO -->
    <section class="profile-info">
      <h2>Personal Information</h2>
      <div class="info-grid">
        <div class="info-item"><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></div>
<div class="info-item"><strong>Phone:</strong> <?php echo htmlspecialchars($phoneNumber ?? 'N/A'); ?></div>
<div class="info-item"><strong>City:</strong> <?php echo htmlspecialchars($city ?? 'N/A'); ?></div>
<div class="info-item"><strong>Member Since:</strong> <?php echo date('F Y', strtotime($dateJoined)); ?></div>
      </div>

      <!-- Edit Profile Button -->
      <div class="edit-profile-btn-wrapper">
        <a href="edit.html" class="btn-action edit-profile-btn">Edit Profile</a>
      </div>
      
      <a href="ride history.html" class="btn-action view-history-btn">View Ride History</a>
    </section>

    <!-- SETTINGS -->
    <section class="profile-settings">
      <h2>Account Settings</h2>
      <a href="logout.html" class="btn-action">Logout</a>
    </section>
  </main>
</body>
</html>
