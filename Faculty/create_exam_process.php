<?php
session_start();
include "../db_conn.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $schedule = $_POST['schedule'];
    $status = $_POST['status'];

    $query = "INSERT INTO exams (subject, schedule, status) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $subject, $schedule, $status);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Exam created successfully.";
    } else {
        $_SESSION['error'] = "Failed to create exam.";
    }

    header("Location: Examination_List.php");
    exit();
}
?>
