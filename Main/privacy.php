<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Privacy Options – RideShare KPU</title>
  <link rel="stylesheet" href="../CSS/privacy.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="logo-container">
      <img src="../images/a.jpg" alt="Logo">
      <h2>Ride<span>Share</span></h2>
    </div>
    <nav>
      <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="ride history.php">My Rides</a></li>
        <li><a href="messages.php">Messages</a></li>
        <li><a href="settings.php" class="active">Settings</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <header class="header">
      <h1>🛡️ Privacy Options</h1>
      <div class="header-actions">
        <button class="btn-notify" title="Notifications"><i class="fas fa-bell"></i></button>
        <img src="../images/a.jpg" class="user-avatar" alt="User Avatar">
      </div>
    </header>

    <section class="privacy-card">
      <h2>Control Your Privacy</h2>
      <form id="privacyForm">
        <div class="toggle-option">
          <label>
            <input type="checkbox" id="showEmail" checked>
            Show my email to other verified members
          </label>
        </div>
        <div class="toggle-option">
          <label>
            <input type="checkbox" id="showPhone">
            Show my phone number on ride details
          </label>
        </div>
        <div class="toggle-option">
          <label>
            <input type="checkbox" id="allowMessages" checked>
            Allow direct messages from other members
          </label>
        </div>
        <div class="toggle-option">
          <label>
            <input type="checkbox" id="shareStats">
            Share my ride statistics publicly
          </label>
        </div>

        <button type="submit" class="btn-action">Save Privacy Settings</button>
      </form>
      <p class="note">Adjust these settings anytime to control what information other KPU members can see.</p>
    </section>
  </main>

  <script>
    // Load saved settings
    window.addEventListener('DOMContentLoaded', () => {
      const saved = JSON.parse(localStorage.getItem('privacyOptions'));
      if (saved) {
        document.getElementById('showEmail').checked = !!saved.showEmail;
        document.getElementById('showPhone').checked = !!saved.showPhone;
        document.getElementById('allowMessages').checked = !!saved.allowMessages;
        document.getElementById('shareStats').checked = !!saved.shareStats;
      }
    });

    // Save on submit
    document.getElementById('privacyForm').addEventListener('submit', (e) => {
      e.preventDefault();
      const options = {
        showEmail: document.getElementById('showEmail').checked,
        showPhone: document.getElementById('showPhone').checked,
        allowMessages: document.getElementById('allowMessages').checked,
        shareStats: document.getElementById('shareStats').checked
      };
      localStorage.setItem('privacyOptions', JSON.stringify(options));
      alert('✅ Privacy settings saved successfully!');
    });
  </script>
</body>
</html>
