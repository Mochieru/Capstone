<?php
session_start();
include "../db_conn.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit();
}

// Fetch announcements
$query = "SELECT a.*, e.subject AS exam_subject, e.schedule AS exam_schedule 
          FROM announcements a 
          LEFT JOIN exams e ON a.exam_id = e.id 
          ORDER BY a.date_posted DESC";
$result = $conn->query($query);

// Handle form submission for new announcements
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $message = $_POST['message'];
    $deadline = $_POST['deadline'];
    $details = $_POST['details'];
    $exam_id = $_POST['exam_id'] ?: null; // Null if no exam is selected

    $insertQuery = "INSERT INTO announcements (title, message, deadline, details, exam_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssssi", $title, $message, $deadline, $details, $exam_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Announcement added successfully.";
    } else {
        $_SESSION['error'] = "Failed to add announcement.";
    }
    header("Location: Faculty_Announcement.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Announcements</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Faculty Announcements</h1>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Add Announcement Form -->
        <?php
        // Fetch exams for the dropdown
        $examQuery = "SELECT id, subject, schedule FROM exams WHERE status = 'upcoming'";
        $examResult = $conn->query($examQuery);
        ?>
        <form method="POST" action="">
            <h2>Add New Announcement</h2>
            <div>
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div>
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="5" required></textarea>
            </div>
            <div>
                <label for="exam_id">Link to Exam:</label>
                <select id="exam_id" name="exam_id">
                    <option value="">No Exam</option>
                    <?php while ($exam = $examResult->fetch_assoc()): ?>
                        <option value="<?php echo $exam['id']; ?>">
                            <?php echo htmlspecialchars($exam['subject']) . " - " . date('Y-m-d H:i', strtotime($exam['schedule'])); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="deadline">Deadline:</label>
                <input type="datetime-local" id="deadline" name="deadline" required>
            </div>
            <div>
                <label for="details">Details:</label>
                <textarea id="details" name="details" rows="5" required></textarea>
            </div>
            <button type="submit">Add Announcement</button>
        </form>

        <!-- Display Announcements -->
        <h2>Existing Announcements</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Exam</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['message']); ?></td>
                        <td>
                            <?php if ($row['exam_subject']): ?>
                                <?php echo htmlspecialchars($row['exam_subject']) . " (" . date('Y-m-d H:i', strtotime($row['exam_schedule'])) . ")"; ?>
                            <?php else: ?>
                                No Exam Linked
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['deadline']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <a href="edit_announcement.php?id=<?php echo $row['announcement_id']; ?>">Edit</a>
                            <a href="delete_announcement.php?id=<?php echo $row['announcement_id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
