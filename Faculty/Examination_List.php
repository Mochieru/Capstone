<?php
session_start();
include "../db_conn.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit();
}

// Handle form submission for adding exams
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $schedule = $_POST['schedule'];
    $status = $_POST['status'];

    $insertQuery = "INSERT INTO exams (subject, schedule, status) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sss", $subject, $schedule, $status);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Exam added successfully.";
    } else {
        $_SESSION['error'] = "Failed to add exam. Error: " . $stmt->error;
    }
    header("Location: Examination_List.php");
    exit();
}

// Fetch exams
$query = "SELECT * FROM exams ORDER BY schedule ASC";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error); // Debugging the query error
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examination List</title>
    <link rel="stylesheet" href="../css/faculty.css">
    <style>
        .container { max-width: 900px; margin: 20px auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .alert { padding: 10px; margin-bottom: 15px; }
        .success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .error { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        .form-section { margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Examination List</h1>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Add New Exam Form -->
        <div class="form-section">
            <h2>Create New Exam</h2>
            <form method="POST" action="">
                <div>
                    <label for="subject">Subject:</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                <div>
                    <label for="schedule">Schedule:</label>
                    <input type="datetime-local" id="schedule" name="schedule" required>
                </div>
                <div>
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="upcoming">Upcoming</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <button type="submit">Add Exam</button>
            </form>
        </div>

        <!-- Exam List -->
        <h2>Existing Exams</h2>
        <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Schedule</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['subject']); ?></td>
                        <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($row['schedule']))); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <a href="edit_exam.php?id=<?php echo $row['id']; ?>">Edit</a>
                            <a href="delete_exam.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this exam?');">Delete</a>
                            <a href="Manage_Questions.php?exam_id=<?php echo $row['id']; ?>">Manage Questions</a>
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
