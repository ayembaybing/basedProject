<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Dean') {
    header("Location: login.php");
    exit;
}
include('../config/db.php');

$offering_id = $_GET['id'];

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
// Load offering details
$stmt = $conn->prepare("SELECT * FROM subject_offerings WHERE offering_id = ?");
$stmt->execute([$offering_id]);
$offering = $stmt->fetch();

// Load all subjects for this offering's program
$subjects = $conn->prepare("SELECT * FROM subjects WHERE program_id = ?");
$subjects->execute([$offering['program_id']]);
$subject_list = $subjects->fetchAll();

// Get current subjects added
$added = $conn->prepare("
    SELECT s.* FROM offering_subjects os
    JOIN subjects s ON os.subject_id = s.subject_id
    WHERE os.offering_id = ?
");
$added->execute([$offering_id]);
$added_subjects = $added->fetchAll();
?>

<h2>Subjects for Offering ID #<?= $offering_id ?></h2>

<?php if (isset($error)): ?>
    <p style="color: red;"><strong><?= $error ?></strong></p>
<?php endif; ?>

<form method="POST" action="../controllers/DeanController.php">
    <input type="hidden" name="offering_id" value="<?= $offering_id ?>">
    <select name="subject_id" required>
        <option value="">-- Select Subject --</option>
        <?php foreach ($subject_list as $s): ?>
            <option value="<?= $s['subject_id'] ?>"><?= $s['subject_code'] ?> - <?= $s['name'] ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="add_subject_to_offering">Add Subject</button>
</form>

<?php if ($offering['status'] === 'Draft'): ?>
    <form method="POST" action="../controllers/DeanController.php">
        <input type="hidden" name="offering_id" value="<?= $_GET['id'] ?>">
        <button type="submit" name="submit_offering" onclick="return confirm('Are you sure you want to submit this offering to the Registrar?')">Submit Offering</button>
    </form>
<?php endif; ?>

<h3>Added Subjects</h3>
<table border="1" cellpadding="5">
    <tr><th>Code</th><th>Name</th><th>Action</th></tr>
    <?php foreach ($added_subjects as $s): ?>
        <tr>
            <td><?= $s['subject_code'] ?></td>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td>
                <a href="../controllers/DeanController.php?remove_subject=<?= $s['subject_id'] ?>&offering_id=<?= $offering_id ?>" onclick="return confirm('Remove this subject?')">Remove</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>


<br><a href="dean_offerings.php">Back</a>
