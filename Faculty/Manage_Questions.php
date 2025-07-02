<?php
session_start();
include "../db_conn.php";

// Check if the user is authorized
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit();
}

// Automatically fetch `exam_id` from URL
$exam_id = $_GET['exam_id'] ?? null;

if (!$exam_id) {
    $_SESSION['error'] = "Exam ID is missing.";
    header("Location: Examination_List.php");
    exit();
}

// Fetch exam details
$examQuery = "SELECT * FROM exams WHERE id = ?";
$stmt = $conn->prepare($examQuery);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$exam = $stmt->get_result()->fetch_assoc();

if (!$exam) {
    $_SESSION['error'] = "Exam not found.";
    header("Location: Examination_List.php");
    exit();
}

// Fetch questions for the exam
$questionsQuery = "SELECT * FROM exam_questions WHERE exam_id = ?";
$stmt = $conn->prepare($questionsQuery);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$questionsResult = $stmt->get_result();

// Handle form submission for adding a question
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_option = $_POST['correct_option'];

    $insertQuery = "INSERT INTO exam_questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("issssss", $exam_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Question added successfully.";
    } else {
        $_SESSION['error'] = "Failed to add question.";
    }

    header("Location: Manage_Questions.php?exam_id=$exam_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../css/manage_questions.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions for <?php echo htmlspecialchars($exam['subject']); ?></title>
</head>
<body>
    <h1>Manage Questions for "<?php echo htmlspecialchars($exam['subject']); ?>"</h1>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php elseif (isset($_SESSION['error'])): ?>
        <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Add Question Form -->
    <h2>Add New Question</h2>
    <form method="POST" action="">
        <label for="question_text">Question:</label>
        <textarea id="question_text" name="question_text" required></textarea>

        <label for="option_a">Option A:</label>
        <input type="text" id="option_a" name="option_a" required>

        <label for="option_b">Option B:</label>
        <input type="text" id="option_b" name="option_b" required>

        <label for="option_c">Option C:</label>
        <input type="text" id="option_c" name="option_c" required>

        <label for="option_d">Option D:</label>
        <input type="text" id="option_d" name="option_d" required>

        <label for="correct_option">Correct Option:</label>
        <select id="correct_option" name="correct_option" required>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
        </select>

        <button type="submit">Add Question</button>
    </form>

    <!-- Questions List -->
    <h2>Existing Questions</h2>
    <?php if ($questionsResult->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Question</th>
                    <th>Option A</th>
                    <th>Option B</th>
                    <th>Option C</th>
                    <th>Option D</th>
                    <th>Correct Option</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $questionNumber = 1; // Initialize the question number
                while ($row = $questionsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $questionNumber++; ?></td>
                        <td><?php echo htmlspecialchars($row['question_text']); ?></td>
                        <td><?php echo htmlspecialchars($row['option_a']); ?></td>
                        <td><?php echo htmlspecialchars($row['option_b']); ?></td>
                        <td><?php echo htmlspecialchars($row['option_c']); ?></td>
                        <td><?php echo htmlspecialchars($row['option_d']); ?></td>
                        <td><?php echo htmlspecialchars($row['correct_option']); ?></td>
                        <td>
                            <a href="edit_question.php?question_id=<?php echo $row['id']; ?>&exam_id=<?php echo $exam_id; ?>">Edit</a>

                            <a href="delete_question.php?question_id=<?php echo $row['id']; ?>&exam_id=<?php echo $exam_id; ?>" 
   onclick="return confirm('Are you sure you want to delete this question?');">Delete</a>

                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No questions found for this exam.</p>
    <?php endif; ?>
</body>
</html>
