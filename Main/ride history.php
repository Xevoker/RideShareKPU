<?php
//Session start and database
require '../PHP/sessioncheck.php';
require '../PHP/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Ride History – RideShare</title>
  <link rel="stylesheet" href="../CSS/ride history.css" />
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="logo-container">
      <img src="../images/kpu.jpg" class="logo-img" alt="Logo">
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

  <!-- Main Content -->
  <main class="main-content">
    <header class="header">
      <h1>Ride History</h1>
      <div class="header-actions">
        <button class="btn-notify" title="Notifications">
          <i class="fas fa-bell"></i>
        </button>
        <img src="../images/a.jpg" class="user-avatar" alt="User Avatar">
      </div>
    </header>

    <section class="history-intro">
      <p>
        Below are rides you’ve **offered** or **joined**.  
        Filter by status or month using the dropdowns.
      </p>
      <div class="filters">
        <label>
          Status:
          <select id="statusFilter">
            <option value="all">All</option>
            <option value="offered">Offered</option>
            <option value="joined">Joined</option>
          </select>
        </label>
        <label>
          Month:
          <select id="monthFilter">
            <option value="all">All</option>
          </select>
        </label>
        <label>
          <div class="actions">
          <a href="../PHP/ride-pdf.php" target="_blank">
              <button class="btn-action">
                  Download Ride History PDF
              </button>
            </a>
          </div>
        </label>
      </div>
    </section>

    <section class="ride-list" id="rideList">
      <!-- Dynamic rides will be injected here -->
    </section>

    <div class="actions">
      <a href="profile.php" class="btn-action">Back to Profile</a>
    </div>
  </main>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const rideList = document.getElementById("rideList");
  const statusFilter = document.getElementById("statusFilter");
  const monthFilter = document.getElementById("monthFilter");

  fetch("../PHP/ride_data.php")
    .then(res => res.json())
    .then(allRides => {
      // Fill month dropdown
      const uniqueMonths = [...new Set(allRides.map(r => r.date.slice(0, 7)))];
      monthFilter.innerHTML = `<option value="all">All</option>` +
        uniqueMonths.map(m => `<option value="${m}">${formatMonth(m)}</option>`).join("");

      renderRides(allRides);

      statusFilter.addEventListener("change", () => renderRides(allRides));
      monthFilter.addEventListener("change", () => renderRides(allRides));
  });

  function renderRides(allRides) {
    rideList.innerHTML = "";
    const statusVal = statusFilter.value;
    const monthVal = monthFilter.value;

    const filtered = allRides.filter(r => {
      const matchesStatus = statusVal === "all" || r.type === statusVal;
      const matchesMonth = monthVal === "all" || r.date.startsWith(monthVal);
      return matchesStatus && matchesMonth;
    });

    if (filtered.length === 0) {
      rideList.innerHTML = `<p style="color:#990000;font-weight:600;">No rides found.</p>`;
      return;
    }

    filtered.forEach(r => {
      const item = document.createElement("div");
      item.className = `ride-item ${r.type}`;
      item.dataset.date = r.date.slice(0, 7);
      item.innerHTML = `
        <div class="ride-date">${formatDate(r.date)}</div>
        <div class="ride-details">
          <h3><i class="fas fa-car"></i> ${r.from} ➝ ${r.to}</h3>
          <p class="status ${r.type}">
            ${r.type === "offered" ? "🚗 Offered" : "✅ Joined"}
          </p>
          <p><strong>Time:</strong> ${r.time}</p>
          <p><strong>Seats:</strong> ${r.seats}</p>
          ${r.notes ? `<p><strong>Notes:</strong> ${r.notes}</p>` : ""}
          <p class="posted-time"><em>Posted at: ${r.postedAt}</em></p>
        </div>
      `;
      rideList.appendChild(item);
    });
  }

  function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
  }

  function formatMonth(monthStr) {
    const [year, month] = monthStr.split("-");
    const d = new Date(year, month - 1);
    return d.toLocaleDateString(undefined, { year: 'numeric', month: 'long' });
  }

  loadRides();
});
</script>
</body>
</html>
