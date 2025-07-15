<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
include('../config/db.php');

// Load departments and programs
$departments = $conn->query("SELECT * FROM departments")->fetchAll();
$programs = $conn->query("SELECT * FROM programs")->fetchAll();

// Load subjects
$subjects = $conn->query("
    SELECT s.*, d.name AS dept_name, p.name AS prog_name 
    FROM subjects s
    JOIN departments d ON s.department_id = d.department_id
    JOIN programs p ON s.program_id = p.program_id
")->fetchAll();
?>

<h2>Manage Subject Profiles</h2>

<form method="POST" action="../controllers/AdminController.php">
    <input type="text" name="subject_code" placeholder="Subject Code" required>
    <input type="text" name="name" placeholder="Subject Name" required>

    <select name="subject_type" required>
    <option value="">-- Select Subject Type --</option>
    <option value="Major">Major</option>
    <option value="Minor">Minor</option>
    <option value="NSTP">NSTP</option>
    <option value="PE">PE</option>
    <option value="Elective">Elective</option>
    </select>

    <select name="department_id" required>
        <option value="">-- Select Department --</option>
        <?php foreach ($departments as $d): ?>
            <option value="<?= $d['department_id'] ?>"><?= $d['name'] ?></option>
        <?php endforeach; ?>
    </select>

    <select name="program_id" required>
        <option value="">-- Select Program --</option>
        <?php foreach ($programs as $p): ?>
            <option value="<?= $p['program_id'] ?>"><?= $p['name'] ?></option>
        <?php endforeach; ?>
    </select>

    <br><br>
    Lecture Units: <input type="number" name="lecture_units" min="0" value="0">
    Comp Lab Units: <input type="number" name="comp_lab_units" min="0" value="0">
    Lab Units: <input type="number" name="lab_units" min="0" value="0">
    RLE Units: <input type="number" name="rle_units" min="0" value="0">
    Affiliation Units: <input type="number" name="affiliation_units" min="0" value="0">
    NSTP? <input type="checkbox" name="is_nstp" value="1">

    <br><br>
    <button type="submit" name="add_subject">Add Subject</button>
</form>

<h3>Existing Subjects</h3>
<table border="1" cellpadding="5">
    <tr><th>Code</th><th>Name</th><th>Type</th><th>Program</th><th>Dept</th><th>Units</th><th>Action</th></tr>
    <?php foreach ($subjects as $s): ?>
        <tr>
            <td><?= $s['subject_code'] ?></td>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td><?= htmlspecialchars($s['subject_type']) ?></td>
            <td><?= $s['prog_name'] ?></td>
            <td><?= $s['dept_name'] ?></td>
            <td>
                LEC: <?= $s['lecture_units'] ?>, 
                CLAB: <?= $s['comp_lab_units'] ?>, 
                LAB: <?= $s['lab_units'] ?>, 
                RLE: <?= $s['rle_units'] ?>, 
                AFF: <?= $s['affiliation_units'] ?><?= $s['is_nstp'] ? ', NSTP' : '' ?>
            </td>
            <td><a href="../controllers/AdminController.php?delete_subject=<?= $s['subject_id'] ?>" onclick="return confirm('Delete this subject?')">Delete</a></td>
        </tr>
    <?php endforeach; ?>
</table>

<br><a href="dashboard.php">Back to Dashboard</a>
