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

// Fetch the question details
$query = "SELECT * FROM exam_questions WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $question_id);
$stmt->execute();
$question = $stmt->get_result()->fetch_assoc();

if (!$question) {
    $_SESSION['error'] = "Question not found.";
    header("Location: Manage_Questions.php?exam_id=$exam_id");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_option = $_POST['correct_option'];

    $updateQuery = "UPDATE exam_questions 
                    SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ?
                    WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssssi", $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option, $question_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Question updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update question.";
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
    <title>Edit Question</title>
</head>
<body>
    <h1>Edit Question</h1>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Edit Question Form -->
    <form method="POST" action="">
        <label for="question_text">Question:</label>
        <textarea id="question_text" name="question_text" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>

        <label for="option_a">Option A:</label>
        <input type="text" id="option_a" name="option_a" required value="<?php echo htmlspecialchars($question['option_a']); ?>">

        <label for="option_b">Option B:</label>
        <input type="text" id="option_b" name="option_b" required value="<?php echo htmlspecialchars($question['option_b']); ?>">

        <label for="option_c">Option C:</label>
        <input type="text" id="option_c" name="option_c" required value="<?php echo htmlspecialchars($question['option_c']); ?>">

        <label for="option_d">Option D:</label>
        <input type="text" id="option_d" name="option_d" required value="<?php echo htmlspecialchars($question['option_d']); ?>">

        <label for="correct_option">Correct Option:</label>
        <select id="correct_option" name="correct_option" required>
            <option value="A" <?php echo $question['correct_option'] == 'A' ? 'selected' : ''; ?>>A</option>
            <option value="B" <?php echo $question['correct_option'] == 'B' ? 'selected' : ''; ?>>B</option>
            <option value="C" <?php echo $question['correct_option'] == 'C' ? 'selected' : ''; ?>>C</option>
            <option value="D" <?php echo $question['correct_option'] == 'D' ? 'selected' : ''; ?>>D</option>
        </select>

        <button type="submit">Update Question</button>
    </form>
</body>
</html>
