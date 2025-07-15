<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
include('../config/db.php');

// Get departments
$stmt = $conn->query("SELECT * FROM departments");
$departments = $stmt->fetchAll();
?>

<h2>Manage Departments</h2>

<form method="POST" action="../controllers/AdminController.php">
    <input type="text" name="department_name" placeholder="New Department Name" required>
    <button type="submit" name="add_department">Add</button>
</form>

<table border="1" cellpadding="5">
    <tr><th>ID</th><th>Name</th><th>Action</th></tr>
    <?php foreach ($departments as $d): ?>
        <tr>
            <td><?= $d['department_id'] ?></td>
            <td><?= htmlspecialchars($d['name']) ?></td>
            <td><a href="../controllers/AdminController.php?delete=<?= $d['department_id'] ?>" onclick="return confirm('Delete this department?')">Delete</a></td>
        </tr>
    <?php endforeach; ?>
</table>

<br><a href="dashboard.php">Back to Dashboard</a>
