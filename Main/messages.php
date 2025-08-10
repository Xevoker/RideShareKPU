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
  <title>Messages – RideShare</title>
  <link rel="stylesheet" href="../CSS/messages.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    /* Added message count badge style */
    .msg-count {
      background-color: #4caf50;
      color: white;
      border-radius: 12px;
      padding: 2px 8px;
      font-size: 0.75rem;
      font-weight: bold;
      margin-left: 6px;
      vertical-align: middle;
    }
    /* Optional: total messages counter style */
    #totalMessagesCount {
      margin: 10px 0;
      font-weight: 600;
      color: #333;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="logo-container">
      <img src="../images/a.jpg" class="logo-img" alt="Logo">
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
      <h1>Messages</h1>
      <div class="header-actions">
        <button class="btn-notify" title="Notifications"><i class="fas fa-bell"></i></button>
        <img src="../images/a.jpg" class="user-avatar" alt="User Avatar">
      </div>
    </header>

    <section class="messages-container">
      <!-- Conversation List -->
      <div class="conversation-list">
        <div class="search-bar">
          <input type="text" id="searchUser" placeholder="Search for someone..." />
          <i class="fas fa-search"></i>
        </div>
        <h2>Conversations</h2>
        <div id="totalMessagesCount">Total messages: 0</div> <!-- Added total messages -->
        <div class="conversations" id="conversations">
          <!-- Conversations are dynamically inserted here -->
        </div>
      </div>

      <!-- Chat Window -->
      <div class="chat-window">
        <div class="chat-header" id="chatHeader">
          <img src="../images/a.jpg" alt="User" class="chat-avatar" id="chatAvatar">
          <h3 id="chatName">Select a conversation</h3>
        </div>
        <div class="chat-messages" id="chatMessages">
          <p class="placeholder">Select a user to start chatting</p>
        </div>
        <form class="chat-input" id="chatForm" style="display:none;">
          <input type="text" id="messageInput" placeholder="Type a message..." required />
          <button type="submit"><i class="fas fa-paper-plane"></i></button>
        </form>
      </div>
    </section>
  </main>

  <script>
  
    const conversationsEl = document.getElementById('conversations');
    const chatMessagesEl = document.getElementById('chatMessages');
    const chatNameEl = document.getElementById('chatName');
    const chatAvatarEl = document.getElementById('chatAvatar');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const totalMessagesCountEl = document.getElementById('totalMessagesCount');

    let activeUser = null;
    let activeCarpoolID = null;
    let myUserID = <?php echo json_encode($_SESSION['userID']); ?>;

    // Load conversations
    async function loadConversations(filter = '') {
      try {
        const res = await fetch('../PHP/mconversation.php');
        const data = await res.json();

        conversationsEl.innerHTML = '';
        let totalMessages = 0;

        data
          .forEach(conv => {
            totalMessages += conv.totalCount;
            let lastMsg = conv.lastMsg ? conv.lastMsg : "No messages yet";
            let rideInfo = `${conv.departureDate} at ${conv.departureTime} → ${conv.destinationAddress}`;

            const div = document.createElement('div');
            div.className = 'conversation';
            div.innerHTML = `
              <img src="../images/a.jpg" class="conv-avatar">
              <div class="conv-info">
                <h3>${conv.otherName} <span class="msg-count">${conv.totalCount}</span></h3>
                <p>${rideInfo}</p>
                <p><strong>${lastMsg}</strong></p>
              </div>
            `;
            div.addEventListener('click', (event) => openConversation(conv, event));
            conversationsEl.appendChild(div);
          });

        totalMessagesCountEl.textContent = `Total messages: ${totalMessages}`;
      } catch (err) {
        console.error('Error loading conversations:', err);
      }
    }

    // Open a conversation
    function openConversation(conv, event) {
      activeCarpoolID = conv.carpoolID;
      document.querySelectorAll('.conversation').forEach(c => c.classList.remove('active'));
      event.currentTarget.classList.add('active');

      chatNameEl.textContent = conv.otherName;
      chatAvatarEl.src = '../images/a.jpg';
      chatForm.style.display = 'flex';
      renderMessages(activeCarpoolID);
    }

    // Render messages
    async function renderMessages(carpoolID) {
      try {
        const res = await fetch(`../PHP/mmessage.php?carpoolID=${carpoolID}`);
        const messages = await res.json();

        chatMessagesEl.innerHTML = '';
        messages.forEach(m => {
          const div = document.createElement('div');
          div.className = 'msg ' + (m.userID == myUserID ? 'sent' : 'received');
          div.innerHTML = `<strong>${m.senderName}</strong><p>${m.message}</p><span class="time">${m.timestamp.slice(11, 16)}</span>`;
          chatMessagesEl.appendChild(div);
        });
        chatMessagesEl.scrollTop = chatMessagesEl.scrollHeight;
      } catch (err) {
        console.error('Error loading messages:', err);
      }
    }

    // Send message
    chatForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const text = messageInput.value.trim();
      if (!text || !activeCarpoolID) return;

      try {
        const res = await fetch('../PHP/msend.php', {
          method: 'POST',
          body: JSON.stringify({ carpoolID: activeCarpoolID, text }),
          headers: { 'Content-Type': 'application/json' }
        });
        const data = await res.json();

        if (data.success) {
          messageInput.value = '';
          renderMessages(activeCarpoolID);
          loadConversations(document.getElementById('searchUser').value);
        }
      } catch (err) {
        console.error('Error sending message:', err);
      }
    });

    // Search
    document.getElementById('searchUser').addEventListener('input', (e) => {
      loadConversations(e.target.value);
    });

    // Initial load
    loadConversations();
  </script>
</body>
</html>
