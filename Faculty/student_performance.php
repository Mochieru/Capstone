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

// Fetch student details
$query = "SELECT * FROM students WHERE student_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->bind_param("s", $student_id);
$stmt->execute();
$studentResult = $stmt->get_result();

if ($studentResult->num_rows === 0) {
    $_SESSION['error'] = "Student not found.";
    header("Location: Manage_Student.php");
    exit();
}

$student = $studentResult->fetch_assoc();

// Fetch performance data
$performanceQuery = "SELECT subject, score, total_score, date_taken FROM student_results WHERE student_id = ?";
$stmt = $conn->prepare($performanceQuery);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->bind_param("s", $student_id);
$stmt->execute();
$performanceResult = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
     <link rel="stylesheet" href="../css/faculty.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($student['name']); ?>'s Performance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: auto;
        }
        .student-details {
            margin-bottom: 20px;
        }
        .student-details p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
       <!-- Sidebar -->
    <div class="sidebar">
       <div class="logo" style="display: flex; align-items: center;">
            <img src="../images/LOGOhd_NoLetters.png" alt="PAEES LOGO" width="65" height="65" style="margin-right: 15px;">
            <span style="font-size: 45px; font-weight: bold; color: #262A39;">PAAES</span>
        </div>
        <div class="nav-menu">
            <a href="../faculty_dashboard.php" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="Manage_Student.php" class="nav-item active">
                <i class="fa-solid fa-users"></i>
                <span>Manage Students</span>
            </a>
            <a href="Faculty_Announcement.php" class="nav-item">
                <i class="fa-solid fa-bullhorn"></i>
                <span>Announcements</span>
            </a>
            <a href="Examination_List.php" class="nav-item">
                <i class="fas fa-sliders-h"></i>
                <span>Examinations</span>
            </a>
            <a href="Results.php" class="nav-item">
                <i class="fas fa-chart-line"></i>
                <span>Results</span>
            </a>
            <a href="Feedbacks.php" class="nav-item">
                <i class="fas fa-comment-dots"></i>
                <span>Feedback</span>
            </a>
            <a href="Manage_Account.php" class="nav-item">
                <i class="fa-solid fa-user"></i>
                <span>Manage Account</span>
            </a>
            <a href="Calendar.php" class="nav-item">
                <i class="fa-solid fa-calendar-days"></i>
                <span>Calendar</span>
            </a>
        </div>
        <button class="premium-btn">
            <a href="logout.php">Logout</a>
        </button>
    </div>
   <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="welcome-section">
                <img src="../images/PHILSCA Logo.png" alt="PHILSCA LOGO" width="65" height="100" style="margin-right: 10px;">
                <div>
                    <span style="font-size: 14px; color: #262A39;">
                        Philippine State College of Aeronautics <br>
                        Piccio Garden, Pasay City <br>
                        Republic of the Philippines <br>
                        Institute of Engineering & Technology
                    </span>
                    <h1 class="welcome-title" style="text-align: center;">Manage Students</h1>
                </div>
            </div>
            <div class="header-right">
                <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" class="profile-pic">
            </div>
        </div>
    <div class="container">
        <h1><?php echo htmlspecialchars($student['name']); ?>'s Performance</h1>

        <!-- Student Details -->
        <div class="student-details">
            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($student['contact_number']); ?></p>
            <p><strong>Year & Section:</strong> <?php echo htmlspecialchars($student['year_section']); ?></p>
            <p><strong>Batch Year:</strong> <?php echo htmlspecialchars($student['batch_year']); ?></p>
        </div>

        <!-- Performance Records -->
        <h2>Performance Records</h2>
        <?php if ($performanceResult->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Score</th>
                        <th>Total Score</th>
                        <th>Date Taken</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($performance = $performanceResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($performance['subject']); ?></td>
                            <td><?php echo htmlspecialchars($performance['score']); ?></td>
                            <td><?php echo htmlspecialchars($performance['total_score']); ?></td>
                            <td><?php echo htmlspecialchars($performance['date_taken']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No performance records found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
