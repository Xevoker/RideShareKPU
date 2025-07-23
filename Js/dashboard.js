// ============================
// DASHBOARD LOGIC
// ============================

// --- Get current logged in user (saved in login.html after login) ---
let currentUserName = "Guest";
const savedProfile = JSON.parse(localStorage.getItem("rideShareProfile"));
if (savedProfile && savedProfile.fullName) {
  currentUserName = savedProfile.fullName;
} else {
  const savedSession = JSON.parse(localStorage.getItem("rideShareSession"));
  if (savedSession && savedSession.username) {
    currentUserName = savedSession.username;
  }
}

// --- Load existing arrays from storage or init empty ---
let rides = JSON.parse(localStorage.getItem("rides")) || [];
let activities = JSON.parse(localStorage.getItem("rideShareActivities")) || [];
let messages = JSON.parse(localStorage.getItem("messages")) || [];

// ============================
// UTILITIES
// ============================
function timeAgo(date) {
  const seconds = Math.floor((new Date() - new Date(date)) / 1000);
  if (seconds < 5) return "Just now";
  if (seconds < 60) return `${seconds} seconds ago`;
  if (seconds < 3600) return `${Math.floor(seconds / 60)} minutes ago`;
  if (seconds < 86400) return `${Math.floor(seconds / 3600)} hours ago`;
  return `${Math.floor(seconds / 86400)} days ago`;
}

// ============================
// SAVE HELPERS
// ============================
function saveRides() {
  localStorage.setItem("rides", JSON.stringify(rides));
}
function saveActivities() {
  localStorage.setItem("rideShareActivities", JSON.stringify(activities));
}
function saveMessages() {
  localStorage.setItem("messages", JSON.stringify(messages));
}

// ============================
// RENDER STATS
// ============================
function updateStatsCards() {
  const statsCards = document.querySelectorAll(".stats-cards .card");
  if (!statsCards.length) return;

  const now = new Date();
  const offeredCount = rides.filter(
    (r) => r.driver === currentUserName && r.status === "offered" && new Date(r.date) >= now
  ).length;

  const takenCount = rides.filter(
    (r) => Array.isArray(r.passengers) && r.passengers.includes(currentUserName) && new Date(r.date) >= now
  ).length;

  const totalRides = offeredCount + takenCount;

  if (statsCards[0]) statsCards[0].querySelector(".number").textContent = totalRides;
  if (statsCards[1]) statsCards[1].querySelector(".number").textContent = offeredCount;
  if (statsCards[2]) statsCards[2].querySelector(".number").textContent = takenCount;
}

// ============================
// RENDER UPCOMING RIDES
// ============================
function renderUpcomingRides() {
  const tbody = document.querySelector(".rides-table tbody");
  if (!tbody) return;

  const now = new Date();
  const upcoming = rides.filter((r) => {
    const d = new Date(r.date);
    return (
      d >= now &&
      (
        (r.status === "offered" && r.driver === currentUserName) ||
        (Array.isArray(r.passengers) && r.passengers.includes(currentUserName))
      )
    );
  });

  tbody.innerHTML = "";

  if (upcoming.length === 0) {
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;">No upcoming rides found.</td></tr>`;
    return;
  }

  upcoming.forEach((ride, idx) => {
    const dateStr = new Date(ride.date).toLocaleDateString(undefined, {
      year: "numeric",
      month: "long",
      day: "numeric"
    });
    const statusText = ride.status ? ride.status.charAt(0).toUpperCase() + ride.status.slice(1) : "Offered";
    const statusClass = ride.status ? ride.status.toLowerCase() : "offered";

    const row = `
      <tr>
        <td>${dateStr}</td>
        <td>${ride.from}</td>
        <td>${ride.to}</td>
        <td>${ride.driver || "-"}</td>
        <td>${ride.seats || "-"}</td>
        <td><span class="status ${statusClass}">${statusText}</span></td>
        <td><a href="details.html?rideId=${ride.id || idx}" class="btn-details">Details</a></td>
      </tr>
    `;
    tbody.insertAdjacentHTML("beforeend", row);
  });
}

// ============================
// RENDER RECENT ACTIVITY
// ============================
function renderRecentActivities() {
  const container = document.getElementById("recentActivityList");
  if (!container) return;

  container.innerHTML = "";
  if (activities.length === 0) {
    container.innerHTML = "<p>No recent activity.</p>";
    return;
  }

  activities.slice(0, 10).forEach((act) => {
    const div = document.createElement("div");
    div.className = "activity-item";
    div.innerHTML = `<p><strong>${act.text}</strong></p><span class="time">${timeAgo(act.date)}</span>`;
    container.appendChild(div);
  });
}

// ============================
// ADD ACTIVITY HELPERS
// ============================
function addActivity(text) {
  activities.unshift({ text, date: new Date().toISOString() });
  if (activities.length > 20) activities.pop();
  saveActivities();
  renderRecentActivities();
  updateStatsCards();
}

// ============================
// INIT
// ============================
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".user-name").forEach((el) => (el.textContent = currentUserName));

  updateStatsCards();
  renderUpcomingRides();
  renderRecentActivities();
});

// ============================
// Sync across tabs
// ============================
window.addEventListener("storage", (event) => {
  if (event.key === "rides") {
    rides = JSON.parse(localStorage.getItem("rides")) || [];
    updateStatsCards();
    renderUpcomingRides();
  }
  if (event.key === "rideShareActivities") {
    activities = JSON.parse(localStorage.getItem("rideShareActivities")) || [];
    renderRecentActivities();
  }
  if (event.key === "messages") {
    messages = JSON.parse(localStorage.getItem("messages")) || [];
    updateStatsCards();
  }
});

// ============================
// Auto refresh every 5 seconds
// ============================
setInterval(() => {
  rides = JSON.parse(localStorage.getItem("rides")) || [];
  activities = JSON.parse(localStorage.getItem("rideShareActivities")) || [];
  updateStatsCards();
  renderUpcomingRides();
  renderRecentActivities();
}, 5000);
