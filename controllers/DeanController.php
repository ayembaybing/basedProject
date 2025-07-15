<?php
session_start();
include('../config/db.php');

// ✅ SUBMIT OFFERING TO REGISTRAR
if (isset($_POST['submit_offering'])) {
    $offering_id = $_POST['offering_id'];
    $user_id = $_SESSION['user']['user_id'];

    // Check if offering exists and belongs to this Dean
    $check = $conn->prepare("SELECT * FROM subject_offerings WHERE offering_id = ? AND created_by = ?");
    $check->execute([$offering_id, $user_id]);
    $offering = $check->fetch();

    if (!$offering) {
        $_SESSION['error'] = "Offering not found or unauthorized.";
        header("Location: ../views/dean_offerings.php");
        exit;
    }

    // Check if at least one subject is added
    $count = $conn->prepare("SELECT COUNT(*) FROM offering_subjects WHERE offering_id = ?");
    $count->execute([$offering_id]);
    if ($count->fetchColumn() == 0) {
        $_SESSION['error'] = "No subjects added to this offering.";
        header("Location: ../views/dean_offering_subjects.php?id=$offering_id");
        exit;
    }

    // Submit offering to registrar
    $submit = $conn->prepare("UPDATE subject_offerings SET status = 'Pending', submitted_at = NOW(), disapproval_reason = NULL WHERE offering_id = ?");
    $submit->execute([$offering_id]);

    $_SESSION['success'] = "Offering submitted to Registrar.";
    header("Location: ../views/dean_offerings.php");
    exit;
}

// ✅ CREATE NEW OFFERING
if (isset($_POST['create_offering'])) {
    $stmt = $conn->prepare("INSERT INTO subject_offerings 
        (program_id, year_level, semester, created_by, status) 
        VALUES (?, ?, ?, ?, 'Draft')");
    $stmt->execute([
        $_POST['program_id'],
        $_POST['year_level'],
        $_POST['semester'],
        $_SESSION['user']['user_id']
    ]);
    $_SESSION['success'] = "Offering created. Add subjects before submitting.";
    header("Location: ../views/dean_offerings.php");
    exit;
}

// ✅ ADD SUBJECT TO OFFERING
if (isset($_POST['add_subject_to_offering'])) {
    $offering_id = $_POST['offering_id'];
    $subject_id = $_POST['subject_id'];

    // Prevent duplicate subject
    $check = $conn->prepare("SELECT * FROM offering_subjects WHERE offering_id = ? AND subject_id = ?");
    $check->execute([$offering_id, $subject_id]);

    if ($check->rowCount() > 0) {
        $_SESSION['error'] = "Subject already added to this offering.";
        header("Location: ../views/dean_offering_subjects.php?id=$offering_id");
        exit;
    }

    // Add subject to offering
    $stmt = $conn->prepare("INSERT INTO offering_subjects (offering_id, subject_id) VALUES (?, ?)");
    $stmt->execute([$offering_id, $subject_id]);

    $_SESSION['success'] = "Subject added successfully.";
    header("Location: ../views/dean_offering_subjects.php?id=$offering_id");
    exit;
}

// ✅ REMOVE SUBJECT FROM OFFERING
if (isset($_GET['remove_subject']) && isset($_GET['offering_id'])) {
    $subject_id = $_GET['remove_subject'];
    $offering_id = $_GET['offering_id'];

    $stmt = $conn->prepare("DELETE FROM offering_subjects WHERE offering_id = ? AND subject_id = ?");
    $stmt->execute([$offering_id, $subject_id]);

    $_SESSION['success'] = "Subject removed successfully.";
    header("Location: ../views/dean_offering_subjects.php?id=$offering_id");
    exit;
}
?>
