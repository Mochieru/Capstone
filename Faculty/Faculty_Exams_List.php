<?php
session_start();
include "../db_conn.php";

// Ensure the user is a faculty member
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'faculty' || !isset($_SESSION['faculty_id'])) {
    $_SESSION['error'] = "Faculty ID is missing. Please log in.";
    header("Location: ../login.php");
    exit();
}

// Faculty ID from session
$faculty_id = $_SESSION['faculty_id'];

// Fetch exams managed by this faculty
$examsQuery = "
    SELECT id, subject, exam_date 
    FROM exams 
    WHERE faculty_id = ? 
    ORDER BY exam_date DESC";
$stmt = $conn->prepare($examsQuery);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$exams = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Exams</title>
    <link rel="stylesheet" href="../css/exams_list.css">
</head>
<body>
    <div class="container">
        <h1>My Exams</h1>
        <p>Select an exam to view its results.</p>

        <?php if ($exams && $exams->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Exam Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $exams->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['subject']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['exam_date'])); ?></td>
                            <td>
                                <a href="Results.php?exam_id=<?php echo $row['id']; ?>">View Results</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No exams found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
