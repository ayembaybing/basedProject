<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
echo "<h2>Welcome, {$user['name']}!</h2>";
echo "<p>Role: {$user['role']}</p>";
echo '<a href="../logout.php">Logout</a>';
?>

<?php if ($user['role'] === 'Admin'): ?>
    <p><a href="admin_departments.php">Manage Departments</a></p>
    <p><a href="admin_programs.php">Manage Programs</a></p>
    <p><a href="admin_users.php">Manage Users</a></p>
    <p><a href="admin_subjects.php">Manage Subjects</a></p>
<?php endif; ?>

<?php if ($user['role'] === 'Dean'): ?>
    <p><a href="dean_offerings.php">Manage Subject Offerings</a></p>
<?php endif; ?>

<?php if ($_SESSION['user']['role'] === 'Registrar'): ?>
    <p><a href="registrar_offerings.php">Review Subject Offerings</a></p>
<?php endif; ?>