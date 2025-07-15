<?php
$host = 'localhost';
$db = 'subject_offering_system';
$user = 'root';
$pass = ''; // change if your XAMPP has password

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
?>