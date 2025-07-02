<?php
session_start();
include "../db_conn.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit();
}

// Fetch batch years for the dropdown
$batchQuery = "SELECT DISTINCT batch_year FROM students ORDER BY batch_year ASC";
$batchResult = $conn->query($batchQuery);

// Fetch students based on selected batch year or all students by default
$selectedBatch = isset($_GET['batch_year']) ? $conn->real_escape_string($_GET['batch_year']) : '';
$studentQuery = $selectedBatch 
    ? "SELECT student_id, name, contact_number, year_section, batch_year FROM students WHERE batch_year = '$selectedBatch'"
    : "SELECT student_id, name, contact_number, year_section, batch_year FROM students";
$studentResult = $conn->query($studentQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="../css/faculty.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert error">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Dropdown for Batch Year -->
        <form method="GET" class="batch-filter-form">
            <label for="batch_year">Filter by Batch Year:</label>
            <select name="batch_year" id="batch_year" onchange="this.form.submit()">
                <option value="">All Batch Years</option>
                <?php while ($batchRow = $batchResult->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($batchRow['batch_year']); ?>" <?php echo $selectedBatch == $batchRow['batch_year'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($batchRow['batch_year']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

        <!-- Upload Form -->
        <form action="import_students.php" method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
            <label for="student_excel">Upload Excel File:</label>
            <input type="file" name="student_excel" id="student_excel" accept=".xls, .xlsx" required>
            <button type="submit">Import Students</button>
        </form>

        <!-- Students Table -->
        <div class="student-table-container" style="margin-top: 20px;">
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Contact Number</th>
                        <th>Year & Section</th>
                        <th>Batch Year</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($studentResult->num_rows > 0): ?>
                        <?php while ($row = $studentResult->fetch_assoc()): ?>
                            <tr onclick="redirectToPerformance('<?php echo $row['student_id']; ?>')">
                                <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['year_section']); ?></td>
                                <td><?php echo htmlspecialchars($row['batch_year']); ?></td>
                                <td>
                                    <a href="edit_student.php?id=<?php echo $row['student_id']; ?>" class="edit-btn">Edit</a>
                                    <a href="delete_student.php?id=<?php echo $row['student_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                                    <a href="generate_account.php?id=<?php echo $row['student_id']; ?>" class="generate-btn">Generate Account</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No students found for the selected batch year.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function redirectToPerformance(studentId) {
            window.location.href = `student_performance.php?id=${studentId}`;
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
