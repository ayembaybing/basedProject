<?php
session_start();
include('../config/db.php');

require_once '../config/db.php'; // Or your actual DB include
$pdo = $conn; // Rename to match your variable, if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_offering'])) {
    $offering_id = $_POST['submit_offering'];
    
    $stmt = $pdo->prepare("UPDATE subject_offerings SET status = 'Pending', submitted_at = NOW() WHERE offering_id = ?");
    $stmt->execute([$offering_id]);

    // Optionally log action
    // Optionally notify Registrar here

    header("Location: ../views/dean_offerings.php");
    exit;
}

// Create Offering
if (isset($_POST['create_offering'])) {
    $stmt = $conn->prepare("INSERT INTO subject_offerings 
        (program_id, year_level, semester, created_by) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['program_id'],
        $_POST['year_level'],
        $_POST['semester'],
        $_SESSION['user']['user_id']
    ]);
    header("Location: ../views/dean_offerings.php");
    exit;
}

// Add subject to offering
if (isset($_POST['add_subject_to_offering'])) {
    $offering_id = $_POST['offering_id'];
    $subject_id = $_POST['subject_id'];

    // Check if already exists
    $check = $conn->prepare("SELECT * FROM offering_subjects WHERE offering_id = ? AND subject_id = ?");
    $check->execute([$offering_id, $subject_id]);

    if ($check->rowCount() > 0) {
        $_SESSION['error'] = "Subject already added to this offering.";
        header("Location: ../views/dean_offering_subjects.php?id=$offering_id");
        exit;
    }

    // Insert if not duplicate
    $stmt = $conn->prepare("INSERT INTO offering_subjects (offering_id, subject_id) VALUES (?, ?)");
    $stmt->execute([$offering_id, $subject_id]);

    header("Location: ../views/dean_offering_subjects.php?id=$offering_id");
    exit;
}

// Remove subject from offering
if (isset($_GET['remove_subject']) && isset($_GET['offering_id'])) {
    $subject_id = $_GET['remove_subject'];
    $offering_id = $_GET['offering_id'];

    $stmt = $conn->prepare("DELETE FROM offering_subjects WHERE offering_id = ? AND subject_id = ?");
    $stmt->execute([$offering_id, $subject_id]);

    header("Location: ../views/dean_offering_subjects.php?id=$offering_id");
    exit;
}

// Submit offering
if (isset($_GET['submit_offering'])) {
    $offering_id = $_GET['submit_offering'];

    // Make sure offering belongs to logged-in Dean
    $stmt = $conn->prepare("SELECT * FROM subject_offerings WHERE offering_id = ? AND created_by = ?");
    $stmt->execute([$offering_id, $_SESSION['user']['user_id']]);
    
    if ($stmt->rowCount() > 0) {
        $conn->prepare("UPDATE subject_offerings SET status = 'Submitted' WHERE offering_id = ?")
             ->execute([$offering_id]);
    }

    header("Location: ../views/dean_offerings.php");
    exit;
}

?>

