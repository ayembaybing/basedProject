<?php
session_start();
include('../config/db.php');

// ✅ Ensure user is Registrar
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Registrar') {
    die('Unauthorized access.');
}

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $offering_id = $_POST['offering_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE subject_offerings SET status = 'Approved', disapproval_reason = NULL WHERE offering_id = ?");
        $stmt->execute([$offering_id]);

        $_SESSION['success'] = "Offering #$offering_id has been approved.";

    } elseif ($action === 'disapprove') {
        $reason = trim($_POST['disapproval_reason']);

        if (empty($reason)) {
            $_SESSION['error'] = "Reason is required for disapproval.";
            header("Location: ../views/registrar_offerings.php");
            exit;
        }

        $stmt = $conn->prepare("UPDATE subject_offerings SET status = 'Disapproved', disapproval_reason = ? WHERE offering_id = ?");
        $stmt->execute([$reason, $offering_id]);

        $_SESSION['success'] = "Offering #$offering_id has been disapproved.";
    } else {
        $_SESSION['error'] = "Invalid action.";
    }

    header("Location: ../views/registrar_offerings.php");
    exit;
}
