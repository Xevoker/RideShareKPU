<?php
session_start();
require_once '../PHP/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST"){
  // Variables
  $studentID = $_SESSION['studentID'];
  $email = $_SESSION['email'];
  $firstName = $_SESSION['firstName'];
  $lastName = $_SESSION['lastName'];
  $userType = $_SESSION['userType'];
  $street = $_SESSION['street'];
  $city = $_SESSION['city'];
  $postalCode = $_SESSION['postalCode'];
  $licenseNumber = isset($_SESSION['licenseNumber']) ? $_SESSION['licenseNumber'] : null;
  $phone = $_SESSION['phoneNumbers'];
  $phoneType = $_SESSION['phoneTypes'];
  $preferences = $_SESSION['preferences'] ?? null;
  $password = $_SESSION['password'];
  $confirmPassword = $_SESSION['confirmPassword'];
  $isActive = 1;
  $averageRating = 0;
  $code = $_POST['code'];

  if (trim($code) !== trim((string)$_SESSION['verify_code'])){
    echo "<script>alert('Wrong verification code.'); window.location.href = 'verify.php';</script>";
  }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $createdAt = $dateJoined = $updatedAt = date('Y-m-d H:i:s');
    // Insert user into the database

    $sql = "INSERT INTO USER (studentID, firstName, lastName, email, userType, licenseNumber,
        street, city, postalCode, preferences, dateJoined, isActive, averageRating,
        createdAt, updatedAt, userPassword)
        VALUES (  :studentID, :firstName, :lastName, :email, :userType, :licenseNumber,
        :street, :city, :postalCode, :preferences, :dateJoined, :isActive, :averageRating,
        :createdAt, :updatedAt, :userPassword)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $errorInfo = $conn->errorInfo();
        die("Error preparing SQL statement: " . implode(" | ", $errorInfo));
    }
    $stmt->execute([
    ':studentID'     => $studentID,
    ':firstName'     => $firstName,
    ':lastName'      => $lastName,
    ':email'         => $email,
    ':userPassword'  => $hashedPassword,
    ':userType'      => $userType,
    ':licenseNumber' => $licenseNumber,
    ':street'        => $street,
    ':city'          => $city,
    ':postalCode'    => $postalCode,
    ':preferences'   => $preferences,
    ':dateJoined'    => $dateJoined,
    ':isActive'      => $isActive,
    ':averageRating' => $averageRating,
    ':createdAt'     => $createdAt,
    ':updatedAt'     => $updatedAt,
    ]);

    $stmt = $conn->prepare("SELECT userID FROM USER WHERE studentID = :studentID");
    if (!$stmt) {
        $errorInfo = $conn->errorInfo();
        die("Error preparing SQL statement: " . implode(" | ", $errorInfo));
    }
    $stmt->execute([':studentID' => $studentID]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $id = $user['userID'];

    $sql2 = "INSERT INTO PHONE (userID, phoneNumber, phoneType)
        VALUES ( :userID, :phoneNumber, :phoneType)";

    $stmt = $conn->prepare($sql2);
    if (!$stmt) {
        $errorInfo = $conn->errorInfo();
        die("Error preparing SQL statement: " . implode(" | ", $errorInfo));
    }
    $stmt->execute([
    ':userID'        => $id,
    ':phoneNumber'   => $phone,
    ':phoneType'     => $phoneType,
    ]);


    header("Location: ../Access/login.php");
        exit();
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
      <h2>Verify Email</h2>
      <p class="intro-text">Enter the code to start sharing rides with your KPU community.</p>

      <form action="verify.php" method="POST" id="loginForm" class="login-form" novalidate>
        <div class="input-group">
          <label for="code">Code</label>
          <input type="text" id="code" name="code" placeholder="10023382" required />
        </div>

        <button type="submit" class="btn-login">Verify</button>
      </form>
    </div>
  </div>


</body>
</html>
