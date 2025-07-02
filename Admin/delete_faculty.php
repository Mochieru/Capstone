<?php
include "../db_conn.php";

if (isset($_GET['faculty_id'])) {
    $faculty_id = $_GET['faculty_id'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM faculty WHERE faculty_id = ?");
    $stmt->bind_param("s", $faculty_id);

    if ($stmt->execute()) {
        header("Location: Manage_Faculty.php?success=Faculty deleted successfully");
        exit();
    } else {
        header("Location: Manage_Faculty.php?error=Failed to delete faculty");
        exit();
    }
} else {
    header("Location: Manage_Faculty.php?error=Invalid request");
    exit();
}
