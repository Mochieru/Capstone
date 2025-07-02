<?php
session_start();
include "../db_conn.php"; // Adjust path as needed

// Check if user is logged in as a student
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}

// Initialize variables
$availableExams = [];
$student_id = $_SESSION['id'];

try {
    // Fetch available exams for the student
    $examQuery = "SELECT id, subject, DATE_FORMAT(schedule, '%M %d, %Y') AS exam_date, duration 
                  FROM exams 
                  WHERE schedule > NOW() 
                  AND id NOT IN (
                      SELECT exam_id FROM exam_submissions WHERE student_id = ?
                  )";
    if ($stmt = $conn->prepare($examQuery)) {
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $availableExams = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }
} catch (Exception $e) {
    die("An error occurred: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Exam</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../css/student.css">
</head>
<body>
    <div class="container">
        <h2>Available Exams</h2>
        <?php if (!empty($availableExams)): ?>
            <ul class="exam-list">
                <?php foreach ($availableExams as $exam): ?>
                    <li>
                        <h3><?php echo htmlspecialchars($exam['subject']); ?></h3>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($exam['exam_date']); ?></p>
                        <p><strong>Duration:</strong> <?php echo htmlspecialchars($exam['duration']); ?> minutes</p>
                        <form action="start_exam.php" method="post">
                            <input type="hidden" name="exam_id" value="<?php echo htmlspecialchars($exam['id']); ?>">
                            <button type="submit" class="btn">Take Exam</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No available exams at this time.</p>
        <?php endif; ?>
    </div>
</body>
</html>
