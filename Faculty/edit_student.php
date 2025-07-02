<?php
session_start();
include "../db_conn.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit();
}

$student_id = $_GET['id'] ?? '';

if (!$student_id) {
    $_SESSION['error'] = "Invalid student ID.";
    header("Location: Manage_Student.php");
    exit();
}

// Fetch student data
$query = "SELECT * FROM students WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Student not found.";
    header("Location: Manage_Student.php");
    exit();
}

$student = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact_number = $_POST['contact_number'];
    $year_section = $_POST['year_section'];
    $batch_year = $_POST['batch_year'];

    $updateQuery = "UPDATE students SET name = ?, contact_number = ?, year_section = ?, batch_year = ? WHERE student_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssss", $name, $contact_number, $year_section, $batch_year, $student_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Student updated successfully.";
        header("Location: Manage_Student.php");
    } else {
        $_SESSION['error'] = "Failed to update student.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
</head>
<body>
    <h1>Edit Student</h1>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required><br>

        <label>Contact Number:</label>
        <input type="text" name="contact_number" value="<?php echo htmlspecialchars($student['contact_number']); ?>" required><br>

        <label>Year & Section:</label>
        <input type="text" name="year_section" value="<?php echo htmlspecialchars($student['year_section']); ?>" required><br>

        <label>Batch Year:</label>
        <input type="text" name="batch_year" value="<?php echo htmlspecialchars($student['batch_year']); ?>" required><br>

        <button type="submit">Update Student</button>
    </form>
</body>
</html>
