<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Registrar') {
    header("Location: login.php");
    exit;
}
include('../config/db.php');

// Get all submitted offerings
$stmt = $conn->prepare("
    SELECT so.*, p.name AS program_name, u.username 
    FROM subject_offerings so
    JOIN programs p ON so.program_id = p.program_id
    JOIN users u ON so.created_by = u.user_id
    WHERE status = 'Submitted'
");
$stmt->execute();
$offerings = $stmt->fetchAll();
?>

<h2>Submitted Subject Offerings</h2>
<table border="1" cellpadding="5">
<tr>
    <th>ID</th><th>Program</th><th>Year</th><th>Semester</th><th>Dean</th><th>Actions</th>
</tr>
<?php foreach ($offerings as $o): ?>
<tr>
    <td><?= $o['offering_id'] ?></td>
    <td><?= $o['program_name'] ?></td>
    <td><?= $o['year_level'] ?></td>
    <td><?= $o['semester'] ?></td>
    <td><?= $o['username'] ?></td>
    <td>
        <form method="POST" action="../controllers/RegistrarController.php" style="display:inline;">
            <input type="hidden" name="offering_id" value="<?= $o['offering_id'] ?>">
            <input type="text" name="disapproval_reason" placeholder="Reason (if disapprove)">
            <button type="submit" name="action" value="approve">Approve</button>
            <button type="submit" name="action" value="disapprove" onclick="return confirm('Are you sure?')">Disapprove</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>

<br><a href="dashboard.php">Back to Dashboard</a>
