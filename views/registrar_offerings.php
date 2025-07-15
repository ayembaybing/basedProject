<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Registrar') {
    header("Location: login.php");
    exit;
}

include('../config/db.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch all offerings except 'Draft'
$stmt = $conn->prepare("
    SELECT so.*, p.name AS program_name, u.name AS dean_name
    FROM subject_offerings so
    JOIN programs p ON so.program_id = p.program_id
    JOIN users u ON so.created_by = u.user_id
    WHERE so.status IN ('Pending', 'Approved', 'Disapproved')
    ORDER BY so.offering_id DESC
");
$stmt->execute();
$offerings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch subject list for each offering
$subjects_by_offering = [];
foreach ($offerings as $offering) {
    $sub_stmt = $conn->prepare("
        SELECT s.subject_code, s.name 
        FROM offering_subjects os
        JOIN subjects s ON os.subject_id = s.subject_id
        WHERE os.offering_id = ?
    ");
    $sub_stmt->execute([$offering['offering_id']]);
    $subjects_by_offering[$offering['offering_id']] = $sub_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<h2>Subject Offerings (Submitted)</h2>

<?php if (isset($_SESSION['error'])): ?>
    <p style="color:red;"><strong><?= $_SESSION['error'] ?></strong></p>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <p style="color:green;"><strong><?= $_SESSION['success'] ?></strong></p>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<table border="1" cellpadding="5">
<tr>
    <th>ID</th>
    <th>Program</th>
    <th>Year</th>
    <th>Semester</th>
    <th>Dean</th>
    <th>Status</th>
    <th>Action / Details</th>
</tr>

<?php foreach ($offerings as $o): ?>
    <?php
        $rowColor = '';
        if ($o['status'] === 'Approved') $rowColor = '#d4edda';       // green
        elseif ($o['status'] === 'Disapproved') $rowColor = '#f8d7da'; // red
        elseif ($o['status'] === 'Pending') $rowColor = '#fff3cd';     // yellow
    ?>
<tr style="background-color: <?= $rowColor ?>;">
    <td><?= $o['offering_id'] ?></td>
    <td><?= $o['program_name'] ?></td>
    <td><?= $o['year_level'] ?></td>
    <td><?= $o['semester'] ?></td>
    <td><?= htmlspecialchars($o['dean_name']) ?></td>
    <td><?= $o['status'] ?></td>
    <td>
        <?php if ($o['status'] === 'Pending'): ?>
            <form method="POST" action="../controllers/RegistrarController.php" style="margin-bottom:5px;">
                <input type="hidden" name="offering_id" value="<?= $o['offering_id'] ?>">
                <input type="text" name="disapproval_reason" placeholder="Reason (if disapproving)">
                <button type="submit" name="action" value="approve">Approve</button>
                <button type="submit" name="action" value="disapprove" onclick="return confirm('Disapprove this offering?')">Disapprove</button>
            </form>
        <?php elseif ($o['status'] === 'Disapproved'): ?>
            <p><strong>Reason:</strong> <?= htmlspecialchars($o['disapproval_reason'] ?? '-') ?></p>
        <?php else: ?>
            <em>No actions needed</em>
        <?php endif; ?>

        <details>
            <summary style="cursor:pointer;">View Subjects</summary>
            <?php if (!empty($subjects_by_offering[$o['offering_id']])): ?>
                <ul>
                    <?php foreach ($subjects_by_offering[$o['offering_id']] as $sub): ?>
                        <li><strong><?= $sub['subject_code'] ?></strong> - <?= htmlspecialchars($sub['name']) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p style="color:gray;">No subjects assigned.</p>
            <?php endif; ?>
        </details>
    </td>
</tr>
<?php endforeach; ?>
</table>

<br><a href="dashboard.php">Back to Dashboard</a>
