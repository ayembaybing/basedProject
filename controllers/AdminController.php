<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('../config/db.php');

// Ensure only Admins can use this
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    die('Unauthorized access');
}

// ADD department
if (isset($_POST['add_department'])) {
    $name = $_POST['department_name'];
    $stmt = $conn->prepare("INSERT INTO departments (name) VALUES (?)");
    $stmt->execute([$name]);
    header("Location: ../views/admin_departments.php");
    exit;
}

// DELETE department
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM departments WHERE department_id = ?");
    $stmt->execute([$id]);
    header("Location: ../views/admin_departments.php");
    exit;
} // ✅ this closing brace was missing

// ADD program
if (isset($_POST['add_program'])) {
    $name = $_POST['program_name'];
$dept_id = $_POST['department_id'];

$stmt = $conn->prepare("INSERT INTO programs (name, department_id) VALUES (?, ?)");
$stmt->execute([$name, $dept_id]);

header("Location: ../views/admin_programs.php");
exit;
}

// DELETE program
if (isset($_GET['delete_program'])) {
    $id = $_GET['delete_program'];
    $stmt = $conn->prepare("DELETE FROM programs WHERE program_id = ?");
    $stmt->execute([$id]);
    header("Location: ../views/admin_programs.php");
    exit;
}

// ADD user
if (isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // For now we use md5 for simplicity
    $role = $_POST['role'];
    $department_id = !empty($_POST['department_id']) ? $_POST['department_id'] : null;
    $program_id = !empty($_POST['program_id']) ? $_POST['program_id'] : null;

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, department_id, program_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password, $role, $department_id, $program_id]);

    header("Location: ../views/admin_users.php");
    exit;
}

// DELETE user
if (isset($_GET['delete_user'])) {
    $id = $_GET['delete_user'];
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$id]);
    header("Location: ../views/admin_users.php");
    exit;
}

// ADD subject
if (isset($_POST['add_subject'])) {
    $stmt = $conn->prepare("INSERT INTO subjects 
    (subject_code, name, subject_type, department_id, program_id, lecture_units, comp_lab_units, lab_units, rle_units, affiliation_units, is_nstp)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");


    $stmt->execute([
    $_POST['subject_code'],
    $_POST['name'],
    $_POST['subject_type'],  // 👈 add this
    $_POST['department_id'],
    $_POST['program_id'],
    $_POST['lecture_units'],
    $_POST['comp_lab_units'],
    $_POST['lab_units'],
    $_POST['rle_units'],
    $_POST['affiliation_units'],
    isset($_POST['is_nstp']) ? 1 : 0
]);
    header("Location: ../views/admin_subjects.php");
    exit;
}

// DELETE subject
if (isset($_GET['delete_subject'])) {
    $id = $_GET['delete_subject'];
    $stmt = $conn->prepare("DELETE FROM subjects WHERE subject_id = ?");
    $stmt->execute([$id]);
    header("Location: ../views/admin_subjects.php");
    exit;
}


?>
