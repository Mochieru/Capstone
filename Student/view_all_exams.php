<?php
session_start();
include "../db_conn.php";

// Check if user is logged in as a student
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: ../index.php");
    exit();
}

// Initialize variables
$exams = [];

try {
    // Fetch all upcoming exams
    $query = "SELECT subject, DATE_FORMAT(schedule, '%M %d, %Y') AS exam_date 
              FROM exams 
              WHERE schedule > NOW() 
              ORDER BY schedule ASC";
    $result = $conn->query($query);
    if ($result) {
        $exams = $result->fetch_all(MYSQLI_ASSOC);
    }
} catch (Exception $e) {
    error_log("Error fetching exams: " . $e->getMessage());
    die("An error occurred. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Exams</title>
    <link rel="stylesheet" href="css/student.css">
</head>
<body>
    <div class="main-content">
        <h1>All Upcoming Exams</h1>
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Exam Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($exams)): ?>
                    <?php foreach ($exams as $exam): ?>
                        <tr>
                            <td><?= htmlspecialchars($exam['subject']) ?></td>
                            <td><?= htmlspecialchars($exam['exam_date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">No upcoming exams.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
