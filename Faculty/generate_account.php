<?php
session_start();
include "../db_conn.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Student ID is required.";
    header("Location: Manage_Student.php");
    exit();
}

$student_id = $_GET['id'];

// Check if the student exists
$studentQuery = $conn->prepare("SELECT name FROM students WHERE student_id = ?");
$studentQuery->bind_param("i", $student_id);
$studentQuery->execute();
$studentResult = $studentQuery->get_result();

if ($studentResult->num_rows === 0) {
    $_SESSION['error'] = "Student not found.";
    header("Location: Manage_Student.php");
    exit();
}

$student = $studentResult->fetch_assoc();

// Check if the student already has an account
$checkAccountQuery = $conn->prepare("SELECT * FROM users WHERE username = ?");
$checkAccountQuery->bind_param("s", $student_id);
$checkAccountQuery->execute();
$accountResult = $checkAccountQuery->get_result();

if ($accountResult->num_rows > 0) {
    $_SESSION['error'] = "Account already exists for this student.";
    header("Location: Manage_Student.php");
    exit();
}

// Generate random password
$random_password = bin2hex(random_bytes(4)); // Generates an 8-character password
$hashed_password = password_hash($random_password, PASSWORD_DEFAULT);

// Create user account
$role = 'student';
$insertAccountQuery = $conn->prepare("INSERT INTO users (role, username, password, name) VALUES (?, ?, ?, ?)");
$insertAccountQuery->bind_param("ssss", $role, $student_id, $hashed_password, $student['name']);
$insertAccountQuery->execute();

if ($insertAccountQuery->affected_rows > 0) {
    $_SESSION['success'] = "Account created successfully!<br>Username: $student_id<br>Password: $random_password";
} else {
    $_SESSION['error'] = "Failed to create account.";
}

header("Location: Manage_Student.php");
?>
