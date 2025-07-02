<?php
session_start();
include "../db_conn.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit();
}

$student_id = $_GET['id'] ?? '';

if (!$student_id) {
    $_SESSION['error'] = "Invalid student ID.";
    header("Location: Manage_Student.php");
    exit();
}

// Delete student
$query = "DELETE FROM students WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Student deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete student.";
}

header("Location: Manage_Student.php");
$conn->close();
?>
