<?php
session_start();
include "../db_conn.php";

if (!isset($_SESSION['faculty_id'])) {
    echo "Faculty ID is not set in the session.";
    exit();
}

$faculty_id = $_SESSION['faculty_id'];

// Fetch exams assigned to this faculty member
$query = "SELECT id, subject FROM exams WHERE faculty_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Exams List</title>
</head>
<body>
    <h1>Exams Assigned to You</h1>
    <?php if ($result && $result->num_rows > 0): ?>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <a href="Manage_Questions.php?exam_id=<?= $row['id'] ?>">
                        <?= htmlspecialchars($row['subject']) ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No exams found.</p>
    <?php endif; ?>
</body>
</html>
