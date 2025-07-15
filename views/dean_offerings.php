<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Dean') {
    header("Location: login.php");
    exit;
}

include('../config/db.php');
$user_id = $_SESSION['user']['user_id'];

// Get program assigned to this Dean
$stmt = $conn->prepare("SELECT program_id FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$program_id = $stmt->fetchColumn();

// Get offerings created by Dean
$offerings = $conn->prepare("SELECT * FROM subject_offerings WHERE created_by = ?");
$offerings->execute([$user_id]);
$my_offerings = $offerings->fetchAll();
?>

<h2>Manage Subject Offerings</h2>

<form method="POST" action="../controllers/DeanController.php">
    <input type="hidden" name="program_id" value="<?= $program_id ?>">
    Year Level: 
    <select name="year_level" required>
        <option value="">-- Select --</option>
        <option value="1">1st Year</option>
        <option value="2">2nd Year</option>
        <option value="3">3rd Year</option>
        <option value="4">4th Year</option>
    </select>
    Semester:
    <select name="semester" required>
        <option value="1st">1st</option>
        <option value="2nd">2nd</option>
        <option value="Summer">Summer</option>
    </select>
    <button type="submit" name="create_offering">Create Offering</button>
</form>

<h3>Your Offerings</h3>
<table border="1" cellpadding="5">
    <tr><th>ID</th><th>Year</th><th>Semester</th><th>Status</th><th>Reason</th><th>Action</th></tr>
    <?php foreach ($my_offerings as $o): ?>
        <tr>
            <td><?= $o['offering_id'] ?></td>
            <td><?= $o['year_level'] ?></td>
            <td><?= $o['semester'] ?></td>
            <td><?= $o['status'] ?></td>
            <td><?= $o['status'] ?></td>
<td><?= htmlspecialchars($o['disapproval_reason'] ?? '') ?></td>
            <td>
    <a href="dean_offering_subjects.php?id=<?= $o['offering_id'] ?>">Add Subjects</a>
    <?php if ($o['status'] === 'Draft'): ?>
        <form method="POST" action="../controllers/DeanController.php" style="display:inline;" onsubmit="return confirm('Submit this offering for approval?');">
    <input type="hidden" name="submit_offering" value="<?= $o['offering_id'] ?>">
    <button type="submit">Submit</button>
</form>
    <?php endif; ?>
</td>
        </tr>
    <?php endforeach; ?>
</table>

<br><a href="dashboard.php">Back to Dashboard</a>
