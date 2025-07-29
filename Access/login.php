<?php
session_start();
session_unset();
session_destroy();
session_start();
require_once '../PHP/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST"){
  $studentID = $_POST['studentID'] ?? '';
  $password = $_POST['password'] ?? '';

  if (!$studentID || !$password) {
      die("Student ID and password are required.");
  }

  $stmt = $conn->prepare("SELECT userID, userPassword, firstName FROM USER WHERE studentID = :studentID");
  if (!$stmt) {
      $errorInfo = $conn->errorInfo();
      die("Error preparing SQL statement: " . implode(" | ", $errorInfo));
  }
  $stmt->execute([':studentID' => $studentID]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  $id = $user['userID'];
  $password_hash = $user['userPassword'];
  $firstName = $user['firstName'];
  if ($id && password_verify($password, $password_hash)) {
      
      // Successful login
      $_SESSION['userID'] = $id;
      $_SESSION['firstName'] = $firstName;
      header("Location: ../Main/dashboard.php");
      exit;
  } else {
      // Redirect back to login form with error message in URL
      header("Location: login.php");
      exit();
  }
      $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login – RideShare KPU</title>
  <link rel="stylesheet" href="../CSS/login.css" />
</head>
<body>
  <div class="login-container">
    <div class="form-box">
      <div class="logo">
        <img src="../images/kpu.jpg" alt="KPU Logo" />
        <h1>RideShare <span>KPU</span></h1>
      </div>
      <h2>Welcome Back</h2>
      <p class="intro-text">Log in to continue sharing rides with your KPU community.</p>

      <form action="login.php" method="POST" id="loginForm" class="login-form" novalidate>
        <div class="input-group">
          <label for="email">Student ID</label>
          <input type="text" id="studentID" name="studentID" placeholder="100233829" required />
        </div>

        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter your password" required />
        </div>

        <button type="submit" class="btn-login">Log In</button>
      </form>

      <p class="forgot-password">
        <a href="#" id="forgotPasswordLink">Forgot Password?</a>
      </p>

      <p class="signup-text">
        Don’t have an account? <a href="signup.html">Sign Up</a>
      </p>
    </div>
  </div>


</body>
</html>
