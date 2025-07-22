<?php
try {
    $conn = new PDO("sqlite:" . __DIR__ . "/../data.db");
    // Set error mode to exceptions
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("SQLite connection failed: " . $e->getMessage());
}
?>