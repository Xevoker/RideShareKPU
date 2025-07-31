
<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RideShare KPU – Logout</title>
  <link rel="stylesheet" href="../CSS/logout.css">
</head>
<body>
  <!-- Header -->
  <header>
    <div class="logo-container">
      <img src="../images/kpu.jpg" alt="KPU Logo" class="logo-img">
      <h1>RideShare <span>KPU</span></h1>
    </div>
    <nav>
      <ul>
        <li><a href="../Landing/ride.html">Home</a></li>
        <li><a href="../Landing/about.html">About</a></li>
        <li><a href="../Landing/benefits.html">Benefits</a></li>
        <li><a href="../Landing/work.html">How It Works</a></li>
        <li><a href="../Landing/locations.html">Locations</a></li>
        <li><a href="../Landing/faq.html">FAQ</a></li>
        <li><a href="../Landing/contact.html">Contact</a></li>
      </ul>
    </nav>
  </header>

  <!-- Logout Message -->
  <section class="logout">
    <div class="logout-card">
      <h2>You’ve been logged out</h2>
      <p>Thank you for using <strong>RideShare KPU</strong>. We hope to see you again soon!</p>
      <div class="buttons">
        <a href="../Access/login.php" class="btn primary">Log In Again</a>
        <a href="../Landing/ride.html" class="btn secondary">Back to Home</a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; 2025 RideShare KPU | Built by KPU Students | <a href="contact.html">Contact Us</a></p>
  </footer>
</body>
</html>
