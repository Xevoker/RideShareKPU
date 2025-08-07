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
    #profilePicInput { display: none; }
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

  <main class="main-content">
    <header class="header">
      <h1>Profile <span class="user-name"></span></h1>
      <div class="header-actions">
        <button class="btn-notify" title="Notifications"><i class="fas fa-bell"></i></button>
        <img src="../images/a.jpg" class="user-avatar" alt="User Avatar" />
      </div>
    </header>

    <div id="deactivated-banner" class="deactivated-banner" style="display:none;">
      Your account is deactivated. Reactivate in Settings.
    </div>

    <section class="profile-card">
      <img src="../images/a.jpg" alt="User Avatar" class="profile-img" id="profileImage" title="Click to change profile picture" />
      <input type="file" id="profilePicInput" accept="image/*" />

      <div class="profile-details">
  
        <p class="profile-role">🚗 Verified Driver & Community Helper</p>
        <p class="profile-bio" contenteditable="true" id="editableBio" aria-label="User biography"></p>
        <button id="saveBioBtn" class="btn-action" style="display:none;">Save Bio</button>
      </div>
    </section>

    <section class="profile-info">
      <h2>Personal Information</h2>
      <div class="info-grid">
        <div class="info-item"><strong>Email:</strong> <span id="infoEmail"></span></div>
        <div class="info-item"><strong>Phone:</strong> <span id="infoPhone"></span></div>
        <div class="info-item"><strong>Location:</strong> <span id="infoLocation"></span></div>
        <div class="info-item"><strong>Member Since:</strong> <span id="infoMemberSince"></span></div>
        <div class="info-item"><strong>Preferred Contact:</strong> <span id="infoPreferredContact"></span></div>
        <div class="info-item"><strong>Vehicle:</strong> <span id="infoVehicle"></span></div>
        <div class="info-item"><strong>Date of Birth:</strong> <span id="infoDOB"></span></div>
        <div class="info-item"><strong>Gender:</strong> <span id="infoGender"></span></div>
        <div class="info-item"><strong>LinkedIn:</strong> <a href="#" id="infoLinkedIn" target="_blank">Not set</a></div>
        <div class="info-item"><strong>Emergency Contact Name:</strong> <span id="infoEmergencyName"></span></div>
        <div class="info-item"><strong>Emergency Contact Phone:</strong> <span id="infoEmergencyPhone"></span></div>
        <div class="info-item"><strong>Carpool Preferences:</strong> <span id="infoCarpoolPrefs"></span></div>
      </div>
      <div class="edit-profile-btn-wrapper">
        <a href="edit.html" class="btn-action edit-profile-btn">Edit Profile</a>
      </div>
      <a href="ride history.html" class="btn-action view-history-btn">View Ride History</a>
    </section>

    <section class="achievements">
      <h2>Achievements</h2>
      <ul id="achievementsList">
        <li>🌟 Completed over 80 rides safely</li>
        <li>🏅 Awarded "Top Driver" for 2024</li>
        <li>💬 4.9/5 Average passenger rating</li>
        <li>🚦 Zero cancellations in last 12 months</li>
      </ul>
    </section>

    <section class="profile-updates">
      <h2>Latest Updates</h2>
      <ul id="profileUpdatesList">
        <li>🚗 Hosted a successful carpool from Surrey to Langley on July 20, 2025.</li>
        <li>📝 Updated bio with new interests on July 18, 2025.</li>
        <li>🌐 Connected LinkedIn account on July 12, 2025.</li>
        <li>📸 Changed profile picture on July 10, 2025.</li>
      </ul>
    </section>

    <section class="profile-settings">
      <h2>Account Settings</h2>
      <a href="change password.html" class="btn-action">Change Password</a>
      <a href="privacy.html" class="btn-action">Privacy Options</a>
      <a href="logout.html" class="btn-action">Logout</a>
    </section>
  </main>
<script>
  const safe = (txt, fb = 'N/A') => (txt && txt.trim() !== '') ? txt : fb;
  const formatDate = (dateStr) => {
    if (!dateStr) return 'N/A';
    const d = new Date(dateStr);
    return isNaN(d) ? 'N/A' : d.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
  };

  let profile;

  window.addEventListener('DOMContentLoaded', () => {
    const profileData = localStorage.getItem('currentUser');

    // if (!profileData || profileData === "null") {
    //   window.location.href = "login.html";
    //   return;
    // }

    // try {
    //   profile = JSON.parse(profileData);
    // } catch (e) {
    //   console.error("Failed to parse currentUser from localStorage", e);
    //   localStorage.removeItem('currentUser');
    //   window.location.href = "login.html";
    //   return;
    // }

    // Use profile
    const fullName = safe(profile.fullname || profile.fullName || profile.displayName || profile.username, 'User');
    document.querySelector('.user-name').textContent = fullName;
    document.querySelector('.profile-name').textContent = fullName;

    document.getElementById('deactivated-banner').style.display = profile.deactivated ? 'block' : 'none';
    document.getElementById('infoEmail').textContent = safe(profile.email);
    document.getElementById('infoPhone').textContent = safe(profile.phone);
    document.getElementById('infoLocation').textContent = safe(profile.location);
    document.getElementById('infoPreferredContact').textContent = safe(profile.preferredContact);
    document.getElementById('infoVehicle').textContent = safe(profile.vehicle, 'None');
    document.getElementById('infoDOB').textContent = formatDate(profile.dob);
    document.getElementById('infoGender').textContent = safe(profile.gender);
    document.getElementById('infoEmergencyName').textContent = safe(profile.emergencyName);
    document.getElementById('infoEmergencyPhone').textContent = safe(profile.emergencyPhone);
    document.getElementById('infoCarpoolPrefs').textContent =
      Array.isArray(profile.carpoolPrefs) && profile.carpoolPrefs.length > 0
        ? profile.carpoolPrefs.join(', ')
        : 'None';

    const linkedin = document.getElementById('infoLinkedIn');
    if (profile.linkedin && profile.linkedin.trim() !== '') {
      linkedin.textContent = profile.linkedin;
      linkedin.href = profile.linkedin.startsWith('http') ? profile.linkedin : 'https://' + profile.linkedin;
    }

    document.getElementById('editableBio').textContent = profile.bio || '';
    if (profile.profilePic) {
      document.getElementById('profileImage').src = profile.profilePic;
    }

    // PROFILE IMAGE HANDLER
    const profileImage = document.getElementById('profileImage');
    const profilePicInput = document.getElementById('profilePicInput');

    profileImage.addEventListener('click', () => {
      profilePicInput.click();
    });

    profilePicInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = function (event) {
        const base64Img = event.target.result;
        profileImage.src = base64Img;

        profile.profilePic = base64Img;
        localStorage.setItem('currentUser', JSON.stringify(profile));

        const users = JSON.parse(localStorage.getItem('users') || '[]');
        const userIndex = users.findIndex(u => u.email === profile.email);
        if (userIndex !== -1) {
          users[userIndex].profilePic = base64Img;
          localStorage.setItem('users', JSON.stringify(users));
        }

        alert('Profile picture updated!');
      };
      reader.readAsDataURL(file);
    });

    // BIO HANDLER
    const bioElem = document.getElementById('editableBio');
    const saveBtn = document.getElementById('saveBioBtn');

    bioElem.addEventListener('input', () => {
      saveBtn.style.display = 'inline-block';
    });

    saveBtn.addEventListener('click', () => {
      profile.bio = bioElem.textContent.trim();
      localStorage.setItem('currentUser', JSON.stringify(profile));

      const users = JSON.parse(localStorage.getItem('users') || '[]');
      const userIndex = users.findIndex(u => u.email === profile.email);
      if (userIndex !== -1) {
        users[userIndex].bio = profile.bio;
        localStorage.setItem('users', JSON.stringify(users));
      }

      alert('Profile bio saved!');
      saveBtn.style.display = 'none';
    });

    console.log("Current user:", profile);
  });
</script>
</body>
</html>
