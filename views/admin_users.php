<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
include('../config/db.php');

// Fetch depts and programs
$departments = $conn->query("SELECT * FROM departments")->fetchAll();
$programs = $conn->query("SELECT * FROM programs")->fetchAll();

// Fetch all users
$users = $conn->query("
    SELECT u.*, d.name AS dept_name, p.name AS prog_name 
    FROM users u 
    LEFT JOIN departments d ON u.department_id = d.department_id 
    LEFT JOIN programs p ON u.program_id = p.program_id
")->fetchAll();
?>

<h2>Manage Users</h2>

<form method="POST" action="../controllers/AdminController.php">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>

    <select name="role" id="roleSelect" onchange="adjustFields()" required>
        <option value="">-- Select Role --</option>
        <option value="Dean">Dean</option>
        <option value="Registrar">Registrar</option>
        <option value="Finance">Finance</option>
    </select>

    <select name="department_id" id="departmentField" style="display:none;">
        <option value="">-- Select Department --</option>
        <?php foreach ($departments as $d): ?>
            <option value="<?= $d['department_id'] ?>"><?= $d['name'] ?></option>
        <?php endforeach; ?>
    </select>

    <select name="program_id" id="programField" style="display:none;">
        <option value="">-- Select Program --</option>
        <?php foreach ($programs as $p): ?>
            <option value="<?= $p['program_id'] ?>"><?= $p['name'] ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit" name="add_user">Add User</button>
</form>

<script>
function adjustFields() {
    let role = document.getElementById("roleSelect").value;
    document.getElementById("departmentField").style.display = (role === "Registrar" || role === "Finance") ? "inline" : "none";
    document.getElementById("programField").style.display = (role === "Dean") ? "inline" : "none";
}
</script>

<table border="1" cellpadding="5" style="margin-top:20px;">
    <tr><th>Name</th><th>Email</th><th>Role</th><th>Department</th><th>Program</th><th>Action</th></tr>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= $u['role'] ?></td>
            <td><?= $u['dept_name'] ?? '-' ?></td>
            <td><?= $u['prog_name'] ?? '-' ?></td>
            <td><a href="../controllers/AdminController.php?delete_user=<?= $u['user_id'] ?>" onclick="return confirm('Delete this user?')">Delete</a></td>
        </tr>
    <?php endforeach; ?>
</table>

<br><a href="dashboard.php">Back to Dashboard</a>
