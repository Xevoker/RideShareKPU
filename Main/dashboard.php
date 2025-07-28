<?php
  $userName = $_SESSION['firstName']
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

</head>
<body>

  <aside class="sidebar">
    <div class="logo-container">
      <img src="../images/kpu.jpg" alt="KPU Logo" class="logo-img" />
      <h2>RideShare <span>KPU</span></h2>
    </div>
    <nav>
      <ul>
        <li><a href="dashboard.html" class="active" aria-label="Dashboard">📊 Dashboard</a></li>
        <li><a href="join.html" aria-label="Join Ride">🚗 Join Ride</a></li>
        <li><a href="join.html" aria-label="Offer Ride">🛣 Offer Ride</a></li>
        <li><a href="profile.html" aria-label="Profile">👤 Profile</a></li>
        <li><a href="messages.html" aria-label="Messages">💬 Messages</a></li>
        <li><a href="ride history.html" aria-label="Ride History">📜 Ride History</a></li>
        <li><a href="ride.html" aria-label="Home Page">🏠 Home Page</a></li>
        <li><a href="logout.html" aria-label="Logout">🔓 Logout</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="main-content">

    <!-- Header -->
    <header class="header">
      <h1>Welcome back, <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>! 👋</h1>
      <div class="header-actions">
        <button class="btn-notify" title="Notifications">🔔</button>
        <img src="../images/a.jpg" alt="User Avatar" class="user-avatar" />
      </div>
    </header>

    
    