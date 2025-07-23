<?php
require_once 'db.php';
if ($_SERVER["REQUEST_METHOD"] === "POST"){
    // Variables
    $studentID = $_POST['studentID'];
    $email = $_POST['email'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $userType = $_POST['userType'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $postalCode = $_POST['postalCode'];
    $licenseNumber = isset($_POST['licenseNumber']) ? $_POST['licenseNumber'] : null;
    $phone = $_POST['phoneNumbers'][0];
    $phoneType = $_POST['phoneTypes'][0]; 
    $preferences = $_POST['preferences'] ?? null;
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $isActive = 1;
    $averageRating = 0;

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


    header("Location: ../Access/login.html");
        exit();
}
?>