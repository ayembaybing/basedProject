<?php
session_start();
include('../config/db.php');

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = md5($_POST['password']); // keep this simple for now

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->execute([$email, $pass]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user'] = $user;
        header("Location: ../views/dashboard.php");
    } else {
        echo "Invalid credentials.";
    }
}
?>
