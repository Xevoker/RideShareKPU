<?php
require '../PHP/sessioncheck.php';
require '../PHP/db.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Join Now - KPU RideShare</title>
  <link rel="stylesheet" href="../CSS/join.css" />
</head>
<body>
  <main class="container" role="main" aria-label="Join KPU RideShare form">
    <h1>Join Now</h1>
    
    <p class="intro-text" style="color:#6A11CB; font-weight:600; margin-bottom:30px; user-select:none;">
      Connect with fellow KPU students and start sharing rides today! Whether you want to <strong>drive</strong> or <strong>ride</strong>, this is your first step.
    </p>

    <form class="join-form" id="joinForm" method="POST" novalidate aria-describedby="form-desc">
      
      <p id="form-desc" class="sr-only">
        Please fill out your full name, email, and select if you want to be a driver or passenger.
      </p>

      <label for="fullName">Full Name</label>
      <input
        type="text"
        id="fullName"
        name="fullName"
        placeholder="Your full name"
        autocomplete="name"
        required
        aria-required="true"
        aria-describedby="nameHelp"
      />
      <small id="nameHelp" class="sr-only">Enter your full legal name.</small>

      <label for="email">Email Address</label>
      <input
        type="email"
        id="email"
        name="email"
        placeholder="you@example.com"
        autocomplete="email"
        required
        aria-required="true"
        aria-describedby="emailHelp"
      />
      <small id="emailHelp" class="sr-only">Enter a valid email address.</small>

      <label for="rideRole">I want to</label>
      <select id="rideRole" name="rideRole" required aria-required="true" aria-describedby="roleHelp">
        <option value="" disabled selected>Select an option</option>
        <option value="driver">Drive</option>
        <option value="passenger">Ride</option>
      </select>
      <small id="roleHelp" class="sr-only">Choose if you want to offer rides or find rides.</small>

      <button type="submit" class="btn-join" aria-label="Join RideShare as driver or passenger">Join Now</button>
    </form>

    <p style="margin-top: 28px; font-size: 0.9rem; color: #3a1d00; user-select:none;">
      By joining, you agree to our <a href="#" style="color:#d16520; text-decoration: underline;">Terms of Service</a> and <a href="#" style="color:#d16520; text-decoration: underline;">Privacy Policy</a>.
    </p>
  </main>

  <script>
    const joinForm = document.getElementById('joinForm');
    const rideRole = document.getElementById('rideRole');

    joinForm.addEventListener('submit', function (e) {
      e.preventDefault(); // prevent form default submit

      // Basic client validation (enhance as needed)
      if (!joinForm.fullName.value.trim()) {
        alert('Please enter your full name.');
        joinForm.fullName.focus();
        return;
      }
      if (!joinForm.email.value.trim()) {
        alert('Please enter your email address.');
        joinForm.email.focus();
        return;
      }
      if (!rideRole.value) {
        alert('Please select whether you want to drive or ride.');
        rideRole.focus();
        return;
      }

      if (rideRole.value === 'driver') {
        window.location.href = 'offer.php';
      } else if (rideRole.value === 'passenger') {
        window.location.href = 'find.php';
      }
    });
  </script>
  
  <style>
    /* Screen reader only helper */
    .sr-only {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      margin: -1px;
      overflow: hidden;
      clip: rect(0,0,0,0);
      white-space: nowrap;
      border: 0;
    }
  </style>
</body>
</html>

  <script>
    const joinForm = document.getElementById('joinForm');
    const rideRole = document.getElementById('rideRole');

    joinForm.addEventListener('submit', function (e) {
      e.preventDefault(); // stop default submit
      const selectedRole = rideRole.value;

      if (selectedRole === 'driver') {
        // Redirect to offer.php
        window.location.href = 'offer.php';
      } else if (selectedRole === 'passenger') {
        // Redirect to find.php
        window.location.href = 'find.php';
      } else {
        alert('Please select whether you want to drive or ride.');
      }
    });
  </script>
</body>
</html>
