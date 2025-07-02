
<?php
session_start();
include "db_conn.php";

// Check if user is logged in as a student
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

// Initialize variables to prevent undefined variable warnings
$upcomingExams = [];
$recentGrades = [];
$announcements = [];
$student_id = $_SESSION['id'] ?? null;

if ($student_id) {
    try {
        // Fetch upcoming exams
        $examQuery = "SELECT subject, DATE_FORMAT(schedule, '%M %d, %Y') AS exam_date 
                      FROM exams 
                      WHERE schedule > NOW() 
                      ORDER BY schedule ASC 
                      LIMIT 5";
        $result = $conn->query($examQuery);
        if ($result) {
            $upcomingExams = $result->fetch_all(MYSQLI_ASSOC);
        }

        // Fetch recent grades
        $gradesQuery = "SELECT subject, score, total_score 
                        FROM student_results 
                        WHERE student_id = ? 
                        ORDER BY date_taken DESC 
                        LIMIT 5";
        $gradesStmt = $conn->prepare($gradesQuery);
        if ($gradesStmt) {
            $gradesStmt->bind_param("i", $student_id);
            $gradesStmt->execute();
            $gradesResult = $gradesStmt->get_result();
            $recentGrades = $gradesResult->fetch_all(MYSQLI_ASSOC);
        }

        // Fetch announcements
        $announcementQuery = "SELECT title, message 
                              FROM announcements 
                              WHERE status = 'Active' 
                              ORDER BY date_posted DESC 
                              LIMIT 5";
        $announcementResult = $conn->query($announcementQuery);
        if ($announcementResult) {
            $announcements = $announcementResult->fetch_all(MYSQLI_ASSOC);
        }
    } catch (Exception $e) {
        error_log("Error fetching dashboard data: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAEES Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="css/student.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="../images/LOGOhd_NoLetters.png" alt="PAEES Logo">
            <span>PAAES</span>
        </div>
        <div class="nav-menu">
            <a href="#" class="nav-item active"><i class="fas fa-home"></i> Home</a>
            <a href="/Project/Student/take_exam.php" class="nav-item"><i class="fa-solid fa-pen"></i> Take Exam</a>
            <a href="calendar.php" class="nav-item"><i class="fa-solid fa-calendar"></i> Calendar</a>
            <a href="/Project/Student/view_all_exams.php" class="nav-item"><i class="fa-solid fa-book"></i> View All Exams</a>
        </div>
        <a href="logout.php" class="premium-btn">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="welcome-section">
                <img src="../images/PHILSCA Logo.png" alt="PHILSCA Logo">
                <span>
                    Philippine State College of Aeronautics <br> Piccio Garden, Pasay City <br>
                    Republic of the Philippines <br> Institute of Engineering & Technology
                </span>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-container">
            <!-- Upcoming Exams -->
            <div class="exam-card">
                <h3>Upcoming Exams</h3>
                <ul>
                    <?php if ($upcomingExams): ?>
                        <?php foreach ($upcomingExams as $exam): ?>
                            <li><strong><?= htmlspecialchars($exam['subject']) ?></strong> - <?= htmlspecialchars($exam['exam_date']) ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No upcoming exams.</li>
                    <?php endif; ?>
                </ul>
                <a href="/Project/Student/take_exam.php" class="btn">Take Exam</a>
            </div>

            <!-- Recent Grades -->
            <div class="grades-card">
                <h3>Recent Grades</h3>
                <ul>
                    <?php if ($recentGrades): ?>
                        <?php foreach ($recentGrades as $grade): ?>
                            <li>
                                <strong><?= htmlspecialchars($grade['subject']) ?>:</strong> 
                                <?= htmlspecialchars($grade['score']) ?>/<?= htmlspecialchars($grade['total_score']) ?>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No recent grades found.</li>
                    <?php endif; ?>
                </ul>
                <a href="grades.php" class="btn">View All Grades</a>
            </div>

            <!-- Announcements -->
            <div class="announcements-card">
                <h3>Important Announcements</h3>
                <ul>
                    <?php if ($announcements): ?>
                        <?php foreach ($announcements as $announcement): ?>
                            <li><strong><?= htmlspecialchars($announcement['title']) ?>:</strong> <?= htmlspecialchars($announcement['message']) ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No active announcements.</li>
                    <?php endif; ?>
                </ul>
                <a href="announcements.php" class="btn">View Announcements</a>
            </div>
        </div>
    </div>
</body>
</html>
