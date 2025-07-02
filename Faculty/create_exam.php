<?php
session_start();
include "../db_conn.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $schedule = $_POST['schedule'];
    $status = $_POST['status'];

    // Insert exam details into the database
    $insertExamQuery = "INSERT INTO exams (subject, schedule, status) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertExamQuery);
    $stmt->bind_param("sss", $subject, $schedule, $status);

    if ($stmt->execute()) {
        $exam_id = $stmt->insert_id; // Get the ID of the newly created exam

        // Insert questions into the database
        foreach ($_POST['questions'] as $question) {
            $insertQuestionQuery = "INSERT INTO exam_questions (exam_id, question_text) VALUES (?, ?)";
            $stmt = $conn->prepare($insertQuestionQuery);
            $stmt->bind_param("is", $exam_id, $question);
            $stmt->execute();
        }

        $_SESSION['success'] = "Exam created successfully.";
    } else {
        $_SESSION['error'] = "Failed to create exam.";
    }
    header("Location: Examination_List.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Exam</title>
    <link rel="stylesheet" href="../css/faculty.css">
    <style>
        .container { max-width: 600px; margin: 20px auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select, textarea, button { width: 100%; padding: 8px; }
        button { margin-top: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
        .add-question-btn { margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Exam</h1>

        <!-- Display Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="subject">Subject:</label>
                <input type="text" id="subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="schedule">Schedule:</label>
                <input type="datetime-local" id="schedule" name="schedule" required>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="upcoming">Upcoming</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="form-group">
                <label>Questions:</label>
                <div id="questions-container">
                    <div>
                        <input type="text" name="questions[]" placeholder="Enter a question" required>
                    </div>
                </div>
                <button type="button" class="add-question-btn" onclick="addQuestion()">Add Another Question</button>
            </div>
            <button type="submit">Create Exam</button>
        </form>
    </div>

    <script>
        function addQuestion() {
            const container = document.getElementById('questions-container');
            const div = document.createElement('div');
            div.innerHTML = '<input type="text" name="questions[]" placeholder="Enter a question" required>';
            container.appendChild(div);
        }
    </script>
</body>
</html>
