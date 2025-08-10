<?php
//Session start and database
require '../PHP/sessioncheck.php';
require '../PHP/db.php';

$userID = $_SESSION['userID'];

//Grabs a name, latest messagem total messages, and date, time , destination for conversation card
$sql = "
    SELECT 
        c.carpoolID,
        (u.firstName || ' ' || u.lastName) AS otherName,
        MAX(m.message) AS lastMsg,
        COUNT(m2.messageID) AS totalCount,
        c.departureDate,
        c.departureTime,
        c.destinationAddress
    FROM RIDE r
    JOIN CARPOOL c 
        ON c.carpoolID = r.carpoolID
    JOIN USER u 
        ON (
            (c.driverID = :uid AND u.userID = r.riderID) 
            OR 
            (r.riderID = :uid AND u.userID = c.driverID)
        )
    LEFT JOIN Message m 
        ON m.carpoolID = c.carpoolID
        AND m.timestamp = (
            SELECT MAX(timestamp) 
            FROM Message 
            WHERE carpoolID = c.carpoolID
        )
    LEFT JOIN Message m2 
        ON m2.carpoolID = c.carpoolID
    WHERE c.driverID = :uid 
       OR r.riderID = :uid
    GROUP BY c.carpoolID, otherName, c.departureDate, c.departureTime, c.destinationAddress
    ORDER BY MAX(m.timestamp) DESC
";
$stmt = $conn->prepare($sql);
$stmt->execute(['uid' => $userID]);
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($conversations);
?>