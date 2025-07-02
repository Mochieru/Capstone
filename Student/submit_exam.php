<?php
session_start();
include "../db_conn.php"; // Adjust path as needed

// Check if user is logged in as a student
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: ../index.php");
    exit();
}

// Validate POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['exam_id']) && isset($_POST['answers'])) {
    $exam_id = intval($_POST['exam_id']);
    $student_id = $_SESSION['id'];
    $answers = $_POST['answers'];

    try {
        // Verify the exam exists
        $examQuery = "SELECT id FROM exams WHERE id = ?";
        $stmt = $conn->prepare($examQuery);
        $stmt->bind_param("i", $exam_id);
        $stmt->execute();
        $examResult = $stmt->get_result();

        if ($examResult->num_rows === 0) {
            die("Invalid exam.");
        }

        // Insert submission
        foreach ($answers as $question_id => $selected_option) {
            $question_id = intval($question_id);
            $selected_option = htmlspecialchars($selected_option);

            $insertQuery = "INSERT INTO exam_submissions (student_id, exam_id, question_id, selected_option) 
                            VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("iiis", $student_id, $exam_id, $question_id, $selected_option);
            $stmt->execute();
        }

        echo "Exam submitted successfully!";
    } catch (Exception $e) {
        die("An error occurred: " . htmlspecialchars($e->getMessage()));
    }
} else {
    die("Invalid submission.");
}
?>
