<?php
session_start();
session_unset();
session_destroy();
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
if ($_SERVER["REQUEST_METHOD"] === "POST"){
  $_SESSION['studentID'] = $_POST['studentID'];
  $_SESSION['email'] = $_POST['email'];
  $_SESSION['firstName'] = $_POST['firstName'];
  $_SESSION['lastName'] = $_POST['lastName'];
  $_SESSION['userType'] = $_POST['userType'];
  $_SESSION['street'] = $_POST['street'];
  $_SESSION['city'] = $_POST['city'];
  $_SESSION['postalCode'] = $_POST['postalCode'];
  $_SESSION['licenseNumber'] = $_POST['licenseNumber'] ?? null;
  $_SESSION['phoneNumber'] = $_POST['phoneNumbers'][0];
  $_SESSION['phoneType'] = $_POST['phoneTypes'][0];
  $_SESSION['preferences'] = $_POST['preferences'] ?? null;
  $_SESSION['password'] = $_POST['password']; 
  $_SESSION['confirmPassword'] = $_POST['confirmPassword']; 
  $email = $_POST['email'];
$mail = new PHPMailer(true);

try {
    $code = rand(10000000, 99999999); // 8-digit code
    $_SESSION['verify_code'] = $code;

    // SMTP Setup
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ridesharekpu@gmail.com';       // Your Gmail
    $mail->Password = 'jabcgdnvvihknpgi';             // App Password (no spaces!)
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Email Settings
    $mail->setFrom('ridesharekpu@gmail.com', 'KPU RideShare');
    $mail->addAddress($email); // User's email address

    $mail->isHTML(true);
    $mail->Subject = 'Your KPU RideShare Verification Code';
    $mail->Body    = "Your verification code is: <b>$code</b>";
    $mail->AltBody = "Your verification code is: $code";

    $mail->send();
    header("Location: verify.php");
    exit();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign Up – RideShare KPU</title>
  <link rel="stylesheet" href="../CSS/signup.css" />
</head>
<body>
  <div class="signup-container">
    <div class="form-box">
      <div class="logo">
        <img src="../images/kpu.jpg" alt="KPU Logo" />
        <h1>RideShare <span>KPU</span></h1>
      </div>
      <h2>Create Your Account</h2>
      <p class="intro-text">Join our trusted KPU carpooling community and start saving today!</p>

    <form action="signup.php" method="POST" class="signup-form" id="signupForm" novalidate>
      <h2>Join RideShare KPU</h2>

      <fieldset>
        <legend>KPU Student Verification</legend>
        <div class="input-group">
          <label for="studentID">KPU Student ID</label>
          <input type="text" id="studentID" name="studentID" pattern="[0-9]{9}" maxlength="9" required placeholder="100123456">
          <small>9-digit student ID number</small>
        </div>
        <div class="input-group">
          <label for="email">KPU Email Address</label>
          <input type="email" id="email" name="email" required pattern=".*@student\.kpu\.ca$" placeholder="firstname.lastname@student.kpu.ca">
          <small>Must use your official KPU email</small>
        </div>
      </fieldset>

      <fieldset>
        <legend>Personal Information</legend>
        <div class="input-group">
          <div class="input-group">
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" required>
          </div>
          <div class="input-group">
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" required>
          </div>
        </div>
        <div class="input-group">
          <label for="userType">Account Type</label>
          <select id="userType" name="userType" required>
            <option value="BOTH">Driver & Rider</option>
            <option value="DRIVER">Driver Only</option>
            <option value="RIDER">Rider Only</option>
          </select>
        </div>
        <div class="input-group">
          <label for="street">Street Address</label>
          <input type="text" id="street" name="street" placeholder="123 Main Street">
        </div>
        <div class="input-group">
          <div class="input-group">
            <label for="city">City</label>
            <input type="text" id="city" name="city" placeholder="Surrey">
          </div>
          <div class="input-group">
            <label for="postalCode">Postal Code</label>
            <input type="text" id="postalCode" name="postalCode" pattern="[A-Za-z][0-9][A-Za-z] [0-9][A-Za-z][0-9]" placeholder="V3T 4H8">
          </div>
        </div>
      </fieldset>

      <fieldset id="driverSection" style="display:none;">
        <legend>Driver Information</legend>
        <div class="input-group">
          <label for="licenseNumber">Driver's License Number</label>
          <input type="text" id="licenseNumber" name="licenseNumber" placeholder="1234567">
        </div>
      </fieldset>

      <fieldset>
        <legend>Contact Information</legend>
        <div class="form-row">
          <div class="input-group">
            <label for="phone1">Phone Number</label>
            <input type="tel" id="phone1" name="phoneNumbers[]" required placeholder="(604) 123-4567">
          </div>
          <div class="input-group">
            <label for="phoneType1">Type</label>
            <select id="phoneType1" name="phoneTypes[]">
              <option value="MOBILE">Mobile</option>
              <option value="HOME">Home</option>
              <option value="WORK">Work</option>
            </select>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Account Security</legend>
        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" minlength="8" required placeholder="Minimum 8 characters">
        </div>
        <div class="input-group">
          <label for="confirmPassword">Confirm Password</label>
          <input type="password" id="confirmPassword" name="confirmPassword" required>
        </div>
      </fieldset>

        <button type="submit" class="btn-signup">Sign Up Now</button>
      </form>

      <p class="login-text">Already have an account? <a href="login.html">Log in</a></p>
    </div>
  </div>
<script>
  const form = document.getElementById('signupForm');
  const studentIdInput = document.getElementById('studentid');
  const emailInput = document.getElementById('email');

  form.addEventListener('submit', function(e) {

    const pwd = form.password.value;
    const confirmPwd = form.confirmPassword.value;

    // Basic validations
    if (pwd !== confirmPwd) {
      alert("Passwords do not match. Please try again.");
      e.preventDefault();
      return;
    }
  });
</script>
</body>
</html>