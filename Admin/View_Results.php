<?php
session_start();
include "../db_conn.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch all student results
$result = $conn->query("
    SELECT sr.student_id, s.name, sr.subject, sr.score, sr.total_score, sr.date_taken
    FROM student_results sr
    INNER JOIN students s ON sr.student_id = s.student_id
    ORDER BY sr.date_taken DESC
");

// General Analytics: Average score 1st attempt per subject
$avg_first_attempt_query = $conn->query("
    SELECT subject, AVG(score / total_score) AS avg_percentage FROM (
        SELECT sr.student_id, sr.subject, sr.score, sr.total_score,
            ROW_NUMBER() OVER (PARTITION BY sr.student_id, sr.subject ORDER BY sr.date_taken) AS attempt_num
        FROM student_results sr
    ) AS ranked
    WHERE attempt_num = 1
    GROUP BY subject
");

// Fetch all avg_first_attempt results into array for reuse
$avg_first_attempt = [];
if ($avg_first_attempt_query) {
    while ($row = $avg_first_attempt_query->fetch_assoc()) {
        $avg_first_attempt[] = $row;
    }
}

// General Analytics: Average improvement 2nd attempt vs 1st attempt per subject
$avg_improvement_query = $conn->query("
    SELECT r1.subject, AVG((r2.score / r2.total_score) - (r1.score / r1.total_score)) AS avg_improvement FROM
    (SELECT sr.student_id, sr.subject, sr.score, sr.total_score,
        ROW_NUMBER() OVER (PARTITION BY sr.student_id, sr.subject ORDER BY sr.date_taken) AS attempt_num
     FROM student_results sr) r1
    JOIN
    (SELECT sr.student_id, sr.subject, sr.score, sr.total_score,
        ROW_NUMBER() OVER (PARTITION BY sr.student_id, sr.subject ORDER BY sr.date_taken) AS attempt_num
     FROM student_results sr) r2
    ON r1.student_id = r2.student_id AND r1.subject = r2.subject AND r1.attempt_num = 1 AND r2.attempt_num = 2
    GROUP BY r1.subject
");

// Fetch all avg_improvement results into array for reuse
$avg_improvement = [];
if ($avg_improvement_query) {
    while ($row = $avg_improvement_query->fetch_assoc()) {
        $avg_improvement[] = $row;
    }
}

// Count improved vs not improved students on 2nd attempt
$improvement_counts_query = $conn->query("
    SELECT
        SUM(CASE WHEN (r2.score / r2.total_score) > (r1.score / r1.total_score) THEN 1 ELSE 0 END) AS improved_count,
        SUM(CASE WHEN (r2.score / r2.total_score) <= (r1.score / r1.total_score) THEN 1 ELSE 0 END) AS not_improved_count
    FROM
    (SELECT sr.student_id, sr.subject, sr.score, sr.total_score,
        ROW_NUMBER() OVER (PARTITION BY sr.student_id, sr.subject ORDER BY sr.date_taken) AS attempt_num
     FROM student_results sr) r1
    JOIN
    (SELECT sr.student_id, sr.subject, sr.score, sr.total_score,
        ROW_NUMBER() OVER (PARTITION BY sr.student_id, sr.subject ORDER BY sr.date_taken) AS attempt_num
     FROM student_results sr) r2
    ON r1.student_id = r2.student_id AND r1.subject = r2.subject AND r1.attempt_num = 1 AND r2.attempt_num = 2
");

$counts = $improvement_counts_query ? $improvement_counts_query->fetch_assoc() : ['improved_count' => 0, 'not_improved_count' => 0];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>View Student Results</title>
    <link rel="stylesheet" href="../css/admin.css" />
    <style>
      table { width: 100%; border-collapse: collapse; }
      th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
      tr:hover { background-color: #f0f0f0; cursor: pointer; }
    </style>
     <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    />
</head>
<body>


   <div class="sidebar">
  <div class="logo" style="display: flex; align-items: center;">
    <img src="/Project/images/LOGOhd_NoLetters.png" alt="PAEES LOGO" width="65" height="65" style="margin-right: 15px;">
    <span style="font-size: 45px; font-weight: bold; color: #262A39;">
      PAAES
    </span>
  </div>
  <div class="nav-menu">
    <a href="/Project/admin_dashboard.php" class="nav-item">
      <i class="fas fa-home"></i>
      <span>Dashboard</span>
    </a>
    <a href="/Project/Admin/Manage_Faculty.php" class="nav-item">
      <i class="fa-solid fa-users"></i>
      <span>Manage Faculty</span>
    </a>
    <a href="/Project/Admin/Manage_Announcement.php" class="nav-item">
      <i class="fa-solid fa-bullhorn"></i>
      <span>Manage Announcement</span>
    </a>
    <a href="/Project/Admin/View_Results.php" class="nav-item active">
      <i class="fa-solid fa-calendar"></i>
      <span>View Student Results</span>
    </a>
  </div>
  <button class="premium-btn">
    <a href="/Project/logout.php">Logout</a>
  </button>
  
</div>




<div class="main-content" style="padding: 20px;">
  <div class="header">
        <div class="welcome-section" style="display: flex; align-items: center; justify-content: center">
          <img src="../images/PHILSCA Logo.png" alt="PAEES LOGO" width="65" height="100" style="margin-right: 10px" />
          <span style="font-size: 14px; color: #262a39; line-height: 20px; text-align: center">
            Philippine State College of Aeronautics <br />
            Piccio Garden, Pasay City <br />
            Republic of the Philippines <br />
            Institute of Engineering & Technology
          </span>
          <hr />
          <h1 class="welcome-title" style="text-align: center">Admin Dashboard - Manage Announcements</h1>
        </div>
        <div class="header-right">
          <div class="user-profile">
            <img src="https://i.pravatar.cc/100?img=8" alt="Admin" class="profile-pic" />
          </div>
        </div>
    <h1>View Student Results</h1>
    

    <h2>Overall Results</h2>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Subject</th>
                <th>Score</th>
                <th>Total Score</th>
                <th>Percentage</th>
                <th>Date Taken</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr onclick="window.location='detailed_result.php?student_id=<?= $row['student_id'] ?>&subject=<?= urlencode($row['subject']) ?>'">
                        <td><?= htmlspecialchars($row['student_id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['subject']) ?></td>
                        <td><?= htmlspecialchars($row['score']) ?></td>
                        <td><?= htmlspecialchars($row['total_score']) ?></td>
                        <td><?= round(($row['score'] / $row['total_score']) * 100, 2) ?>%</td>
                        <td><?= date('M d, Y H:i', strtotime($row['date_taken'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align: center;">No results found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 40px;">
        <h2>General Analytics</h2>

        <h3>Average Score on First Attempt (by Subject)</h3>
        <ul>
        <?php foreach ($avg_first_attempt as $row): ?>
            <li><?= htmlspecialchars($row['subject']) ?>: <?= round($row['avg_percentage'] * 100, 2) ?>%</li>
        <?php endforeach; ?>
        </ul>

        <h3>Average Improvement from 1st to 2nd Attempt (by Subject)</h3>
        <ul>
        <?php foreach ($avg_improvement as $row): ?>
            <li><?= htmlspecialchars($row['subject']) ?>: <?= round($row['avg_improvement'] * 100, 2) ?>%</li>
        <?php endforeach; ?>
        </ul>

        <h3>Improvement Counts</h3>
        <p>Number of students who improved on 2nd attempt: <?= $counts['improved_count'] ?></p>
        <p>Number of students who did NOT improve: <?= $counts['not_improved_count'] ?></p>
    </div>
</div>
<button onclick="window.location.href='/Project/Admin/yearly.php'" style="margin: 20px 0; padding: 10px 20px; font-size: 16px;">
  View Old Results & Yearly Comparison
</button>

<div style="margin-top: 40px;">
    <h2>General Analytics</h2>

    <h3>Average Score on First Attempt (by Subject)</h3>
    <div style="width: 800px; height: 400px; margin: auto;">
        <canvas id="avgFirstAttemptChart"></canvas>
    </div>

    <h3>Average Improvement from 1st to 2nd Attempt (by Subject)</h3>
    <div style="width: 800px; height: 400px; margin: auto; margin-top: 20px;">
        <canvas id="avgImprovementChart"></canvas>
    </div>

    <h3>Improvement Counts</h3>
    <div style="width: 800px; height: 400px; margin: auto; margin-top: 20px;">
        <canvas id="improvementCountsChart"></canvas>
    </div>
</div>

<script>
    // Data preparation for graphs using PHP arrays
    const avgFirstAttemptData = <?= json_encode($avg_first_attempt) ?>;
    const avgImprovementData = <?= json_encode($avg_improvement) ?>;
    const improvementCounts = <?= json_encode($counts) ?>;

    // Average Score on First Attempt Chart
    new Chart(document.getElementById('avgFirstAttemptChart'), {
        type: 'bar',
        data: {
            labels: avgFirstAttemptData.map(row => row.subject),
            datasets: [{
                label: 'Average Score (%)',
                data: avgFirstAttemptData.map(row => (row.avg_percentage * 100).toFixed(2)),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Average Improvement Chart
    new Chart(document.getElementById('avgImprovementChart'), {
        type: 'bar',
        data: {
            labels: avgImprovementData.map(row => row.subject),
            datasets: [{
                label: 'Average Improvement (%)',
                data: avgImprovementData.map(row => (row.avg_improvement * 100).toFixed(2)),
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Improvement Counts Pie Chart
    new Chart(document.getElementById('improvementCountsChart'), {
        type: 'pie',
        data: {
            labels: ['Improved', 'Not Improved'],
            datasets: [{
                data: [improvementCounts.improved_count, improvementCounts.not_improved_count],
                backgroundColor: ['rgba(54, 162, 235, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false
        }
    });
</script>

</body>
</html>
