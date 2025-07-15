<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
include('../config/db.php');

// Get all departments for dropdown
$dept_stmt = $conn->query("SELECT * FROM departments");
$departments = $dept_stmt->fetchAll();

// Get all programs with department name
$prog_stmt = $conn->query("
    SELECT p.program_id, p.name AS program_name, d.name AS department_name 
    FROM programs p
    JOIN departments d ON p.department_id = d.department_id
");
$programs = $prog_stmt->fetchAll();
?>

<h2>Manage Programs</h2>

<form method="POST" action="../controllers/AdminController.php">
    <input type="text" name="program_name" placeholder="Program Name (e.g., BSIT)" required>
    <select name="department_id" required>
        <option value="">-- Select Department --</option>
        <?php foreach ($departments as $d): ?>
            <option value="<?= $d['department_id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="add_program">Add</button>
</form>

<table border="1" cellpadding="5">
    <tr><th>ID</th><th>Program</th><th>Department</th><th>Action</th></tr>
    <?php foreach ($programs as $p): ?>
        <tr>
            <td><?= $p['program_id'] ?></td>
            <td><?= htmlspecialchars($p['program_name']) ?></td>
            <td><?= htmlspecialchars($p['department_name']) ?></td>
            <td><a href="../controllers/AdminController.php?delete_program=<?= $p['program_id'] ?>" onclick="return confirm('Delete this program?')">Delete</a></td>
        </tr>
    <?php endforeach; ?>
</table>

<br><a href="dashboard.php">Back to Dashboard</a>
