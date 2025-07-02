<?php
session_start();
include "../db_conn.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit();
}

$exam_id = $_GET['id'] ?? '';

if (!$exam_id) {
    $_SESSION['error'] = "Invalid exam ID.";
    header("Location: Examination_List.php");
    exit();
}

// Fetch exam details
$query = "SELECT * FROM exams WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Exam not found.";
    header("Location: Examination_List.php");
    exit();
}

$exam = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $schedule = $_POST['schedule'];
    $status = $_POST['status'];

    $updateQuery = "UPDATE exams SET subject = ?, schedule = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssi", $subject, $schedule, $status, $exam_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Exam updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update exam.";
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
    <title>Edit Exam</title>
    <link rel="stylesheet" href="../css/faculty.css">
    <style>
        .container { max-width: 600px; margin: 20px auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select, button { width: 100%; padding: 8px; }
        button { margin-top: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Exam</h1>

        <!-- Display Errors -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="subject">Subject:</label>
                <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($exam['subject']); ?>" required>
            </div>
            <div class="form-group">
                <label for="schedule">Schedule:</label>
                <input type="datetime-local" id="schedule" name="schedule" value="<?php echo date('Y-m-d\TH:i', strtotime($exam['schedule'])); ?>" required>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="upcoming" <?php echo $exam['status'] === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                    <option value="ongoing" <?php echo $exam['status'] === 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                    <option value="completed" <?php echo $exam['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>
            <button type="submit">Update Exam</button>
        </form>
    </div>
</body>
</html>
