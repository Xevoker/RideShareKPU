<?php
include '../PHP/sessioncheck.php';
include '../PHP/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_SESSION['userID'];
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // 1. Fetch current hashed password
    $stmt = $conn->prepare("SELECT password FROM USER WHERE userID = ?");
    $stmt->execute([$userID]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || !password_verify($currentPassword, $row['password'])) {
        $changeMessage = "Current password is incorrect.";
    } elseif ($newPassword !== $confirmPassword) {
        $changeMessage = "New passwords do not match.";
    } elseif (strlen($newPassword) < 8) {
        $changeMessage = "Password must be at least 8 characters.";
    } else {
        // 2. Hash and update
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $conn->prepare("UPDATE USER SET password = ? WHERE userID = ?");
        $updateStmt->execute([$hashedPassword, $userID]);
        $changeMessage = "✅ Password successfully updated!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Change Password – RideShare KPU</title>
  <link rel="stylesheet" href="../CSS/change password.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <aside class="sidebar">
    <div class="logo-container">
      <img src="../images/a.jpg" class="logo-img" alt="Logo">
      <h2>Ride<span>Share</span></h2>
    </div>
    <nav>
      <ul>
        <li><a href="dashboard.html">Dashboard</a></li>
        <li><a href="profile.html">Profile</a></li>
        <li><a href="ride history.html">My Rides</a></li>
        <li><a href="messages.html">Messages</a></li>
        <li><a href="settings.html">Settings</a></li>
      </ul>
    </nav>
  </aside>

  <main class="main-content">
    <header class="header">
      <h1>🔒 Change Password</h1>
      <div class="header-actions">
        <button class="btn-notify" title="Notifications"><i class="fas fa-bell"></i></button>
        <img src="../images/a.jpg" class="user-avatar" alt="User Avatar">
      </div>
    </header>

    <section class="change-pass-card">
      <h2>Update Your Password</h2>
      <form action="change password.php" method="POST" id="changePassForm">
        <label for="currentPassword">Current Password</label>
        <input type="password" id="currentPassword" placeholder="Enter current password" required>

        <label for="newPassword">New Password</label>
        <input type="password" id="newPassword" placeholder="Enter new password" required>

        <label for="confirmPassword">Confirm New Password</label>
        <input type="password" id="confirmPassword" placeholder="Re-enter new password" required>

        <button type="submit" class="btn-action">Save Changes</button>
      </form>

      <p class="forgot-pass">
        <a href="forgot password.html">Forgot your password?</a>
      </p>

      <p class="note">Make sure your new password is at least 8 characters long and secure.</p>
    </section>
  </main>
</body>
</html>
