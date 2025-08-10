<?php
//Session start and database, get userID
require '../PHP/sessioncheck.php';
require '../PHP/db.php';
$userID = $_SESSION['userID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Variables
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $contactMethod = $_POST['contactMethod'] ?? '';
    $location = $_POST['location'] ?? '';
    $vehicle = $_POST['vehicle'] ?? '';
    $linkedin = $_POST['linkedin'] ?? '';
    $emergencyName = $_POST['emergencyName'] ?? '';
    $emergencyPhone = $_POST['emergencyPhone'] ?? '';
    $favoriteMusic = $_POST['favoriteMusic'] ?? '';
    $carpoolPrefs = isset($_POST['carpoolPrefs']) ? implode(',', $_POST['carpoolPrefs']) : '';
    $bio = $_POST['bio'] ?? '';

    // Insert or update profile
    $stmt = $conn->prepare("
        INSERT INTO PROFILE (userID, dob, gender, contactMethod, location, vehicle, linkedin, emergencyName, emergencyPhone, favoriteMusic, carpoolPrefs, bio)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON CONFLICT(userID) DO UPDATE SET
            dob=excluded.dob,
            gender=excluded.gender,
            contactMethod=excluded.contactMethod,
            location=excluded.location,
            vehicle=excluded.vehicle,
            linkedin=excluded.linkedin,
            emergencyName=excluded.emergencyName,
            emergencyPhone=excluded.emergencyPhone,
            favoriteMusic=excluded.favoriteMusic,
            carpoolPrefs=excluded.carpoolPrefs,
            bio=excluded.bio
    ");
    $stmt->execute([$userID, $dob, $gender, $contactMethod, $location, $vehicle, $linkedin, $emergencyName, $emergencyPhone, $favoriteMusic, $carpoolPrefs, $bio]);
    header("Location: profile.php");
    exit();
}

// Fetch data from user
$stmtUser = $conn->prepare("SELECT firstName, lastName, email FROM USER WHERE userID = ?");
$stmtUser->execute([$userID]);
$userData = $stmtUser->fetch(PDO::FETCH_ASSOC);

// Fetch profile data
$stmtProfile = $conn->prepare("SELECT * FROM PROFILE WHERE userID = ?");
$stmtProfile->execute([$userID]);
$profileData = $stmtProfile->fetch(PDO::FETCH_ASSOC) ?: [];
$prefs = explode(',', $profileData['carpoolPrefs'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Edit Profile – RideShare</title>
  <link rel="stylesheet" href="../CSS/edit.css" />
</head>
<body>
  <aside class="sidebar">
    <div class="logo-container">
      <img src="../images/a.jpg" class="logo-img" alt="Logo" />
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
      <h1>Edit Profile</h1>
    </header>

    <section class="edit-profile-form">
      <form method="POST">
        <!-- Basic Info (read-only) -->
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" value="<?php echo htmlspecialchars($userData['firstName'] . ' ' . $userData['lastName']); ?>" readonly />
        </div>

        <div class="form-group">
          <label>Email Address</label>
          <input type="email" value="<?php echo htmlspecialchars($userData['email']); ?>" readonly />
        </div>

        <!-- Profile fields -->
        <div class="form-group">
          <label>Date of Birth</label>
          <input type="date" name="dob" value="<?php echo htmlspecialchars($profileData['dob'] ?? ''); ?>" />
        </div>

        <div class="form-group">
          <label>Gender</label>
          <select name="gender">
            <option value="">Select</option>
            <?php
            foreach (['Female', 'Male', 'Non-binary', 'Prefer not to say'] as $opt) {
                $sel = ($profileData['gender'] ?? '') === $opt ? 'selected' : '';
                echo "<option value=\"$opt\" $sel>$opt</option>";
            }
            ?>
          </select>
        </div>

        <div class="form-group">
          <label>Preferred Contact Method</label>
          <select name="contactMethod">
            <?php
            foreach (['Email', 'Phone'] as $opt) {
                $sel = ($profileData['contactMethod'] ?? 'Email') === $opt ? 'selected' : '';
                echo "<option value=\"$opt\" $sel>$opt</option>";
            }
            ?>
          </select>
        </div>

        <div class="form-group">
          <label>Campus Location</label>
          <select name="location">
            <?php
            foreach (['Surrey Campus', 'Langley Campus', 'Richmond Campus'] as $opt) {
                $sel = ($profileData['location'] ?? '') === $opt ? 'selected' : '';
                echo "<option value=\"$opt\" $sel>$opt</option>";
            }
            ?>
          </select>
        </div>

        <div class="form-group">
          <label>Vehicle (optional)</label>
          <input type="text" name="vehicle" value="<?php echo htmlspecialchars($profileData['vehicle'] ?? ''); ?>" />
        </div>

        <div class="form-group">
          <label>LinkedIn Profile URL</label>
          <input type="text" name="linkedin" value="<?php echo htmlspecialchars($profileData['linkedin'] ?? ''); ?>" />
        </div>

        <div class="form-group">
          <label>Emergency Contact Name</label>
          <input type="text" name="emergencyName" value="<?php echo htmlspecialchars($profileData['emergencyName'] ?? ''); ?>" />
        </div>

        <div class="form-group">
          <label>Emergency Contact Phone</label>
          <input type="tel" name="emergencyPhone" value="<?php echo htmlspecialchars($profileData['emergencyPhone'] ?? ''); ?>" />
        </div>

        <div class="form-group">
          <label>Favorite Music Genre</label>
          <select name="favoriteMusic">
            <option value="">Select</option>
            <?php
            foreach (['Pop', 'Rock', 'Hip Hop', 'Jazz', 'Classical', 'Country', 'Electronic', 'Other'] as $opt) {
                $sel = ($profileData['favoriteMusic'] ?? '') === $opt ? 'selected' : '';
                echo "<option value=\"$opt\" $sel>$opt</option>";
            }
            ?>
          </select>
        </div>

        <fieldset class="form-group">
          <legend>Carpool Preferences</legend>
          <?php
          foreach (['Non-Smoking', 'Pets Allowed', 'Music Allowed', 'Chatty Driver'] as $pref) {
              $chk = in_array($pref, $prefs) ? 'checked' : '';
              echo "<label><input type=\"checkbox\" name=\"carpoolPrefs[]\" value=\"$pref\" $chk /> $pref</label><br />";
          }
          ?>
        </fieldset>

        <div class="form-group">
          <label>Short Bio</label>
          <textarea name="bio" rows="4"><?php echo htmlspecialchars($profileData['bio'] ?? ''); ?></textarea>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-save">Save Changes</button>
          <a href="profile.php" class="btn-cancel">Cancel</a>
        </div>
      </form>
    </section>
  </main>
</body>
</html>
