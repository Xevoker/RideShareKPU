<?php
// Session start and database
require '../PHP/sessioncheck.php';
require '../PHP/db.php';
require_once('../vendor/autoload.php'); // TCPDF autoload

$userID = $_SESSION['userID'];

// Get offered rides
$stmtOffered = $conn->prepare("
    SELECT carpoolID, originAddress AS `from`, destinationAddress AS `to`,
           departureDate AS `date`, departureTime AS `time`,
           availableSeats AS `seats`, notes, createdDate AS postedAt
    FROM CARPOOL
    WHERE driverID = ?
    ORDER BY departureDate DESC, departureTime DESC
");
$stmtOffered->execute([$userID]);
$offered = $stmtOffered->fetchAll(PDO::FETCH_ASSOC);
foreach ($offered as &$ride) {
    $ride['type'] = 'Offered';
}

// Get joined rides
$stmtJoined = $conn->prepare("
    SELECT c.carpoolID, c.originAddress AS `from`, c.destinationAddress AS `to`,
           c.departureDate AS `date`, c.departureTime AS `time`,
           availableSeats AS `seats`,  notes AS notes, c.createdDate AS postedAt
    FROM RIDE r
    INNER JOIN CARPOOL c ON r.carpoolID = c.carpoolID
    WHERE r.riderID = ?
    ORDER BY c.departureDate DESC, c.departureTime DESC
");
$stmtJoined->execute([$userID]);
$joined = $stmtJoined->fetchAll(PDO::FETCH_ASSOC);
foreach ($joined as &$ride) {
    $ride['type'] = 'Joined';
}

// Get name and studentID
$stmtInfo = $conn->prepare("
    SELECT firstName, lastName, studentID
    FROM USER
    WHERE userID = ?
");
$stmtInfo->execute([$userID]);
$name = $stmtInfo->fetch(PDO::FETCH_ASSOC);

// Merge results
$allRides = array_merge($offered, $joined);
usort($allRides, function($a, $b) {
    return strtotime($b['date'] . ' ' . $b['time']) - strtotime($a['date'] . ' ' . $a['time']);
});

$fname = $name['firstName'];
$lname = $name['lastName'];
$studentID = $name['studentID'];
// ---- Generate PDF ----

// Create TCPDF object
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Carpool App');
$pdf->SetTitle('Ride History');
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();

// PDF Title
$html = '<h2 style="text-align:center;">Ride History </h2>';
$html .= '<h3 style="text-align:center; font-weight: normal; margin-bottom: 20px;">';
$html .= 'Full Name: ' . $fname . ' ' . $lname ;
$html .= '<br>' ;
$html .= 'Student Number: ' . $studentID;
$html .= '</h3>';

// Table header
$html .= '
<style>
    table {
        border-collapse: collapse;
        width: 100%;
        font-family: helvetica;
        font-size: 10px;
    }
    th {
        background-color: #f2f2f2;
        border: 1px solid #000;
        padding: 5px;
    }
    td {
        border: 1px solid #000;
        padding: 5px;
    }
</style>
<table>
<tr>
    <th>Type</th>
    <th>From</th>
    <th>To</th>
    <th>Date</th>
    <th>Time</th>
    <th>Seats</th>
    <th>Notes</th>
</tr>
';

// Table rows
foreach ($allRides as $ride) {
    $html .= '<tr>
        <td>' . htmlspecialchars($ride['type']) . '</td>
        <td>' . htmlspecialchars($ride['from']) . '</td>
        <td>' . htmlspecialchars($ride['to']) . '</td>
        <td>' . htmlspecialchars($ride['date']) . '</td>
        <td>' . htmlspecialchars($ride['time']) . '</td>
        <td>' . htmlspecialchars($ride['seats']) . '</td>
        <td>' . htmlspecialchars($ride['notes']) . '</td>
    </tr>';
}

$html .= '</table>';

// Output to PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('ride_history.pdf', 'D');