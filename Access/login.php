<?php
//Destoy the session to ensure no data overlap and restarts the session
session_start();
session_unset();
session_destroy();
session_start();
require_once '../PHP/db.php';// database connection


if ($_SERVER["REQUEST_METHOD"] === "POST"){
  $studentID = $_POST['studentID'] ?? '';
  $password = $_POST['password'] ?? '';

  //Checks inputs
  if (!$studentID || !$password) {
      die("Student ID and password are required.");
  }

  // Grabs the user information based on the inputed studentid
  $stmt = $conn->prepare("SELECT userID, userPassword, firstName FROM USER WHERE studentID = :studentID");
  if (!$stmt) {
      $errorInfo = $conn->errorInfo();
      die("Error preparing SQL statement: " . implode(" | ", $errorInfo));
  }
  $stmt->execute([':studentID' => $studentID]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  //Assigns data into values
  $id = $user['userID'];
  $password_hash = $user['userPassword'];
  $firstName = $user['firstName'];
  //Checks password
  if ($id && password_verify($password, $password_hash)) {
      
      // Successful login
      $_SESSION['userID'] = $id;
      $_SESSION['firstName'] = $firstName;
      //Get the date
      $today = date('Y-m-d');

      // Update all rides before today to 'complete'
      $sql = "UPDATE CARPOOL SET status = 'complete' 
          WHERE departureDate < :today 
          AND status != 'complete'";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':today' => $today]);

      //Gets the carpoolID from existing rides
      $sql_driver = "SELECT carpoolID FROM CARPOOL
                   WHERE driverID = :userID AND status = 'offered'
                   LIMIT 1";
      $stmt_driver = $conn->prepare($sql_driver);
      $stmt_driver->execute([':userID' => $id]);
      $driverRide = $stmt_driver->fetch(PDO::FETCH_ASSOC);

      //If driver
      if ($driverRide) {
          $_SESSION['carpoolID'] = $driverRide['carpoolID'];
      } else {
          //Else checks if user is a rider
          $sql_rider = "SELECT carpoolID FROM RIDE
                        WHERE riderID = :userID
                        ORDER BY carpoolID DESC
                        LIMIT 1";
          $stmt_rider = $conn->prepare($sql_rider);
          $stmt_rider->execute([':userID' => $id]);
          $riderRide = $stmt_rider->fetch(PDO::FETCH_ASSOC);
          if ($riderRide) {
              $_SESSION['carpoolID'] = $riderRide['carpoolID'];
          }
      }

      require_once '../PHP/history.php';
      //Move to the main page
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
        Don’t have an account? <a href="signup.php">Sign Up</a>
      </p>
    </div>
  </div>


</body>
</html>
