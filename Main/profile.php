<?php
//Session start and database
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: ../Access/login.php");
    exit();
}
include '../PHP/db.php';

  // Get data from USER 
  $stmt = $conn->prepare("SELECT userID, studentID, firstName, lastName, email, userType, licenseNumber, street, city, postalCode, preferences, dateJoined, isActive, averageRating, createdAt, updatedAt, userPassword
  FROM USER WHERE userID = :userID");
  if (!$stmt) {
        $errorInfo = $conn->errorInfo();
        die("Error preparing SQL statement: " . implode(" | ", $errorInfo));
    }
    $stmt->execute([':userID' => $_SESSION['userID']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
  
  // Get data from PHONE 
  $stmt2 = $conn->prepare("SELECT phoneNumber
  FROM PHONE WHERE userID = :userID");
  if (!$stmt2) {
        $errorInfo = $conn->errorInfo();
        die("Error preparing SQL statement: " . implode(" | ", $errorInfo));
    }
    $stmt2->execute([':userID' => $_SESSION['userID']]);
    $user2 = $stmt2->fetch(PDO::FETCH_ASSOC);

    // Gets the user rating
    $stmt3 = $conn->prepare("SELECT AVG(rating) AS totalrating FROM RATE WHERE driverID = :userID");
    $stmt3->execute([':userID' => $_SESSION['userID']]);
    $avg = $stmt3->fetch(PDO::FETCH_ASSOC);

    // Get data from PROFILE
    $stmtProfile = $conn->prepare("
    SELECT location, contactMethod, vehicle, dob, gender, linkedin, emergencyName, emergencyPhone, favoriteMusic, carpoolPrefs, bio
    FROM PROFILE 
    WHERE userID = :userID
    ");
    $stmtProfile->execute([':userID' => $_SESSION['userID']]);
    $profile = $stmtProfile->fetch(PDO::FETCH_ASSOC);

    // Variables
    $userID         = $user['userID']?? 'N/A';
    $studentID      = $user['studentID']?? 'N/A';
    $firstName      = $user['firstName']?? 'N/A';
    $lastName       = $user['lastName']?? 'N/A';
    $phoneNumber    = $user2['phoneNumber']?? 'N/A';
    $email          = $user['email']?? 'N/A';
    $createdAt      = $user['createdAt']?? 'N/A';
    $rating         = $avg['totalrating']?? 'N/A';
    $location       = $profile['location'] ?? 'N/A';
    $contactMethod  = $profile['contactMethod'] ?? 'N/A';
    $vehicle        = $profile['vehicle'] ?? 'N/A';
    $dob            = $profile['dob'] ?? 'N/A';
    $gender         = $profile['gender'] ?? 'N/A';
    $linkedin       = $profile['linkedin'] ?? 'N/A';
    $emergencyName  = $profile['emergencyName'] ?? 'N/A';
    $emergencyPhone = $profile['emergencyPhone'] ?? 'N/A';
    $carpoolPrefs   = $profile['carpoolPrefs'] ?? 'N/A';

    $stmt = $conn->prepare("SELECT address FROM ADDRESS WHERE userID = :userID");
    $stmt->execute([':userID' => $_SESSION['userID']]);
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        
        <p class="profile-role"><?php echo htmlspecialchars($firstName); ?> <?php echo htmlspecialchars($lastName); ?></p>
        <p class="profile-role">🚗 Verified Driver & Community Helper</p>
        <p class="profile-role"><?php echo htmlspecialchars($rating); ?> Star Rating</p>
        <p class="profile-bio" contenteditable="true" id="editableBio" aria-label="User biography"></p>
        <button id="saveBioBtn" class="btn-action" style="display:none;">Save Bio</button>
      </div>
    </section>

    <section class="profile-info">
      <h2>Personal Information</h2>
      <div class="info-grid">
        <div class="info-item"><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></div>
        <div class="info-item"><strong>Student ID:</strong> <br> <?php echo htmlspecialchars($studentID); ?></div>
        <div class="info-item"><strong>Phone:</strong><br> <?php echo htmlspecialchars($phoneNumber); ?></span></div>
        <div class="info-item"><strong>Location:</strong><br><br> <?php echo htmlspecialchars($location); ?> </div>
        <div class="info-item"><strong>Member Since:</strong><br><?php echo htmlspecialchars($createdAt); ?></div>
        <div class="info-item"><strong>Preferred Contact:</strong><br> <?php echo htmlspecialchars($contactMethod); ?></div>
        <div class="info-item"><strong>Vehicle:</strong><br> <?php echo htmlspecialchars($vehicle); ?></div>
        <div class="info-item"><strong>Date of Birth:</strong><br> <?php echo htmlspecialchars($dob); ?></div>
        <div class="info-item"><strong>Gender:</strong><br> <?php echo htmlspecialchars($gender); ?></div>
        <div class="info-item"><strong>LinkedIn:</strong> <a href="#" id="infoLinkedIn" target="_blank"><br> <?php echo htmlspecialchars($linkedin); ?></a></div>
        <div class="info-item"><strong>Emergency Contact Name:</strong><br> <?php echo htmlspecialchars($emergencyName); ?></div>
        <div class="info-item"><strong>Emergency Contact Phone:</strong><br> <?php echo htmlspecialchars($emergencyPhone); ?></div>
        <div class="info-item"><strong>Carpool Preferences:</strong><br> <?php echo htmlspecialchars($carpoolPrefs); ?></div>
      </div>
      <div class="info-grid">
      <div class="info-item">
        <strong>Address History:</strong><br>
        <?php
        if ($addresses) {
            foreach ($addresses as $row) {
                echo htmlspecialchars($row['address']) . "<br>";
            }
        } else {
            echo "No addresses found.";
        }
        ?>
        </div>
      </div>
      <div class="edit-profile-btn-wrapper">
        <a href="edit.php" class="btn-action edit-profile-btn">Edit Profile</a>
      </div>
      <a href="ride history.php" class="btn-action view-history-btn">View Ride History</a>
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
      <a href="change password.php" class="btn-action">Change Password</a>
      <a href="privacy.php" class="btn-action">Privacy Options</a>
      <a href="logout.php" class="btn-action">Logout</a>
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
    //   window.location.href = "login.php";
    //   return;
    // }

    // try {
    //   profile = JSON.parse(profileData);
    // } catch (e) {
    //   console.error("Failed to parse currentUser from localStorage", e);
    //   localStorage.removeItem('currentUser');
    //   window.location.href = "login.php";
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
