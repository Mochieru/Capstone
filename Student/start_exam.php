<?php
session_start();
include "../db_conn.php"; // Adjust path to database connection file

// Check if user is logged in as a student
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: ../index.php");
    exit();
}

// Check if exam_id is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['exam_id'])) {
    $exam_id = intval($_POST['exam_id']);
} else {
    die("Invalid access. Please select an exam.");
}

try {
    // Fetch exam details
    $examQuery = "SELECT id, subject, DATE_FORMAT(schedule, '%M %d, %Y') AS exam_date, duration 
                  FROM exams 
                  WHERE id = ?";
    $stmt = $conn->prepare($examQuery);
    $stmt->bind_param("i", $exam_id);
    $stmt->execute();
    $examResult = $stmt->get_result();
    $examDetails = $examResult->fetch_assoc();

    if (!$examDetails) {
        die("Exam not found.");
    }

    // Fetch exam questions
    $questionsQuery = "SELECT id, question_text, option_a, option_b, option_c, option_d 
                       FROM exam_questions 
                       WHERE exam_id = ?";
    $stmt = $conn->prepare($questionsQuery);
    $stmt->bind_param("i", $exam_id);
    $stmt->execute();
    $questionsResult = $stmt->get_result();
    $questions = $questionsResult->fetch_all(MYSQLI_ASSOC);

    if (!$questions) {
        die("No questions found for this exam.");
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
    <title>Take Exam - <?php echo htmlspecialchars($examDetails['subject']); ?></title>
    <link rel="stylesheet" href="../css/student.css">
</head>
<body>
    <div class="main-content">
        <h1>Exam: <?php echo htmlspecialchars($examDetails['subject']); ?></h1>
        <p>Scheduled Date: <?php echo htmlspecialchars($examDetails['exam_date']); ?></p>
        <p>Duration: <?php echo htmlspecialchars($examDetails['duration']); ?> minutes</p>

        <form action="submit_exam.php" method="post">
            <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
            <ol>
                <?php foreach ($questions as $index => $question): ?>
                    <li>
                        <p><?php echo htmlspecialchars($question['question_text']); ?></p>
                        <div>
                            <label>
                                <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="A">
                                <?php echo htmlspecialchars($question['option_a']); ?>
                            </label>
                        </div>
                        <div>
                            <label>
                                <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="B">
                                <?php echo htmlspecialchars($question['option_b']); ?>
                            </label>
                        </div>
                        <div>
                            <label>
                                <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="C">
                                <?php echo htmlspecialchars($question['option_c']); ?>
                            </label>
                        </div>
                        <div>
                            <label>
                                <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="D">
                                <?php echo htmlspecialchars($question['option_d']); ?>
                            </label>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
            <button type="submit">Submit Exam</button>
        </form>
    </div>
</body>
</html>
