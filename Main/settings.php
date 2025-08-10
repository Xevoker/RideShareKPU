<?php
//Session start and database
require '../PHP/sessioncheck.php';
require '../PHP/db.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Settings – RideShare</title>
  <link rel="stylesheet" href="../CSS/settings.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- Sidebar -->
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
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <header class="header">
      <h1>Settings</h1>
      <div class="header-actions">
        <button class="btn-notify" title="Notifications"><i class="fas fa-bell"></i></button>
        <img src="../images/a.jpg" class="user-avatar" alt="User Avatar" />
      </div>
    </header>

    <section class="settings-section">
      <h2><i class="fas fa-user-cog"></i> Account Settings</h2>
      <div class="settings-group">
        <label>Change Display Name</label>
        <input type="text" id="displayNameInput" placeholder="Enter new display name" />
        <button class="btn-action" id="saveNameBtn">Save Name</button>
      </div>
      <div class="settings-group">
        <label>Change Password</label>
        <input type="password" id="currentPassword" placeholder="Current password" />
        <input type="password" id="newPassword" placeholder="New password" />
        <button class="btn-action" id="savePasswordBtn">Update Password</button>
      </div>
    </section>

    <section class="settings-section">
      <h2><i class="fas fa-bell"></i> Notifications</h2>
      <div class="settings-group">
        <label><input type="checkbox" id="notifyRides" /> Email me about ride updates</label>
      </div>
      <div class="settings-group">
        <label><input type="checkbox" id="notifyPromotions" /> Email me about promotions</label>
      </div>
      <div class="settings-group">
        <label><input type="checkbox" id="notifyMessages" /> Push notifications for new messages</label>
      </div>
      <button class="btn-action" id="saveNotifBtn">Save Notification Preferences</button>
    </section>

    <section class="settings-section">
      <h2><i class="fas fa-lock"></i> Privacy</h2>
      <div class="settings-group">
        <label for="profileVisibility">Profile Visibility</label>
        <select id="profileVisibility">
          <option>Public</option>
          <option>Friends Only</option>
          <option>Private</option>
        </select>
      </div>
      <div class="settings-group">
        <label for="rideHistory">Show Ride History</label>
        <select id="rideHistory">
          <option>Visible to Passengers</option>
          <option>Only Me</option>
        </select>
      </div>
      <button class="btn-action" id="savePrivacyBtn">Save Privacy Settings</button>
    </section>

    <section class="settings-section danger">
      <h2><i class="fas fa-exclamation-triangle"></i> Danger Zone</h2>
      <button class="btn-danger" id="deactivateBtn">Deactivate Account</button>
      <button class="btn-danger" id="deleteBtn">Delete Account</button>
    </section>
  </main>

  <script>
    // Helpers to get/save users and current user
    function getUsers() {
      return JSON.parse(localStorage.getItem('users') || '[]');
    }
    function saveUsers(users) {
      localStorage.setItem('users', JSON.stringify(users));
    }
    function getCurrentUser() {
      return JSON.parse(localStorage.getItem('currentUser'));
    }
    function saveCurrentUser(user) {
      localStorage.setItem('currentUser', JSON.stringify(user));
    }

    /* window.addEventListener('DOMContentLoaded', () => {
      const currentUser = getCurrentUser();
      if (!currentUser) {
        alert('No user logged in. Redirecting to login page.');
        window.location.href = '../Access/login.php';
        return;
      } */

      // Populate form fields with user data
      document.getElementById('displayNameInput').value = currentUser.displayName || '';

      document.getElementById('notifyRides').checked = currentUser.notifyRides || false;
      document.getElementById('notifyPromotions').checked = currentUser.notifyPromotions || false;
      document.getElementById('notifyMessages').checked = currentUser.notifyMessages || false;

      document.getElementById('profileVisibility').value = currentUser.profileVisibility || 'Friends Only';
      document.getElementById('rideHistory').value = currentUser.rideHistory || 'Visible to Passengers';

      // Save Display Name
      document.getElementById('saveNameBtn').addEventListener('click', () => {
        const newName = document.getElementById('displayNameInput').value.trim();
        if (!newName) {
          alert('Display name cannot be empty.');
          return;
        }
        const users = getUsers();
        const userIndex = users.findIndex(u => u.email === currentUser.email);
        if (userIndex === -1) {
          alert('User not found.');
          return;
        }

        users[userIndex].displayName = newName;
        saveUsers(users);

        currentUser.displayName = newName;
        saveCurrentUser(currentUser);

        alert('Display name updated.');
      });

      // Save Password
      document.getElementById('savePasswordBtn').addEventListener('click', () => {
        const currentPass = document.getElementById('currentPassword').value.trim();
        const newPass = document.getElementById('newPassword').value.trim();

        if (!currentPass || !newPass) {
          alert('Please fill in both current and new password fields.');
          return;
        }

        if (currentPass !== currentUser.password) {
          alert('Current password is incorrect.');
          return;
        }

        if (newPass.length < 4) {
          alert('New password must be at least 4 characters long.');
          return;
        }

        const users = getUsers();
        const userIndex = users.findIndex(u => u.email === currentUser.email);
        if (userIndex === -1) {
          alert('User not found.');
          return;
        }

        // Update password
        users[userIndex].password = newPass;
        saveUsers(users);

        currentUser.password = newPass;
        saveCurrentUser(currentUser);

        // Clear input fields
        document.getElementById('currentPassword').value = '';
        document.getElementById('newPassword').value = '';

        alert('Password updated successfully!');
      });

      // Save Notifications
      document.getElementById('saveNotifBtn').addEventListener('click', () => {
        const users = getUsers();
        const userIndex = users.findIndex(u => u.email === currentUser.email);
        if (userIndex === -1) {
          alert('User not found.');
          return;
        }

        const notifyRides = document.getElementById('notifyRides').checked;
        const notifyPromotions = document.getElementById('notifyPromotions').checked;
        const notifyMessages = document.getElementById('notifyMessages').checked;

        users[userIndex].notifyRides = notifyRides;
        users[userIndex].notifyPromotions = notifyPromotions;
        users[userIndex].notifyMessages = notifyMessages;

        saveUsers(users);

        currentUser.notifyRides = notifyRides;
        currentUser.notifyPromotions = notifyPromotions;
        currentUser.notifyMessages = notifyMessages;

        saveCurrentUser(currentUser);

        alert('Notification preferences saved!');
      });

      // Save Privacy
      document.getElementById('savePrivacyBtn').addEventListener('click', () => {
        const users = getUsers();
        const userIndex = users.findIndex(u => u.email === currentUser.email);
        if (userIndex === -1) {
          alert('User not found.');
          return;
        }

        const profileVisibility = document.getElementById('profileVisibility').value;
        const rideHistory = document.getElementById('rideHistory').value;

        users[userIndex].profileVisibility = profileVisibility;
        users[userIndex].rideHistory = rideHistory;

        saveUsers(users);

        currentUser.profileVisibility = profileVisibility;
        currentUser.rideHistory = rideHistory;

        saveCurrentUser(currentUser);

        alert('Privacy settings saved!');
      });

      // Deactivate Account
      document.getElementById('deactivateBtn').addEventListener('click', () => {
        if (confirm('Are you sure you want to deactivate your account?')) {
          const users = getUsers();
          const userIndex = users.findIndex(u => u.email === currentUser.email);
          if (userIndex === -1) {
            alert('User not found.');
            return;
          }

          users[userIndex].deactivated = true;
          saveUsers(users);

          currentUser.deactivated = true;
          saveCurrentUser(currentUser);

          alert('Account deactivated. Your profile will be hidden.');
        }
      });

      // Delete Account
      document.getElementById('deleteBtn').addEventListener('click', () => {
        if (confirm('This will permanently delete your account. Continue?')) {
          let users = getUsers();
          users = users.filter(u => u.email !== currentUser.email);
          saveUsers(users);
          localStorage.removeItem('currentUser');
          alert('Account deleted permanently.');
          window.location.href = '../Access/signup.php'; // redirect to signup or login
        }
      });
  </script>
</body>
</html>
