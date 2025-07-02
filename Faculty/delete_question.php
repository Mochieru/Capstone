<?php
session_start();
include "../db_conn.php";

// Check if user is authorized
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit();
}

// Check for question_id and exam_id
$question_id = $_GET['question_id'] ?? null;
$exam_id = $_GET['exam_id'] ?? null;

if (!$question_id || !$exam_id) {
    $_SESSION['error'] = "Invalid question or exam ID.";
    header("Location: Manage_Questions.php?exam_id=$exam_id");
    exit();
}

// Delete the question
$query = "DELETE FROM exam_questions WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $question_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Question deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete question.";
}

header("Location: Manage_Questions.php?exam_id=$exam_id");
exit();
?>
