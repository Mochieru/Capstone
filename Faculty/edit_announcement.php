<?php
session_start();
include "../db_conn.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit();
}

// Fetch the announcement details
$announcement_id = $_GET['id'] ?? '';
if (!$announcement_id) {
    $_SESSION['error'] = "Invalid announcement ID.";
    header("Location: Faculty_Announcement.php");
    exit();
}

$query = "SELECT * FROM announcements WHERE announcement_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->bind_param("i", $announcement_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Announcement not found.";
    header("Location: Faculty_Announcement.php");
    exit();
}

$announcement = $result->fetch_assoc();

// Handle form submission for editing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $message = $_POST['message'];
    $deadline = $_POST['deadline'];
    $subject = $_POST['subject'];
    $details = $_POST['details'];

    $updateQuery = "UPDATE announcements SET title = ?, message = ?, deadline = ?, subject = ?, details = ?, last_updated = NOW() WHERE announcement_id = ?";
    $stmt = $conn->prepare($updateQuery);
    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("sssssi", $title, $message, $deadline, $subject, $details, $announcement_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Announcement updated successfully.";
        header("Location: Faculty_Announcement.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to update announcement.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Announcement</title>
    <link rel="stylesheet" href="../css/faculty.css">
</head>
<body>
    <div class="container">
        <h1>Edit Announcement</h1>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Edit Announcement Form -->
        <form method="POST" action="">
            <div>
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($announcement['title']); ?>" required>
            </div>
            <div>
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($announcement['message']); ?></textarea>
            </div>
            <div>
                <label for="deadline">Deadline:</label>
                <input type="datetime-local" id="deadline" name="deadline" value="<?php echo date('Y-m-d\TH:i', strtotime($announcement['deadline'])); ?>" required>
            </div>
            <div>
                <label for="subject">Subject:</label>
                <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($announcement['subject']); ?>" required>
            </div>
            <div>
                <label for="details">Details:</label>
                <textarea id="details" name="details" rows="5" required><?php echo htmlspecialchars($announcement['details']); ?></textarea>
            </div>
            <button type="submit">Update Announcement</button>
        </form>
    </div>
</body>
</html>
