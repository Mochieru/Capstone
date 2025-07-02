<?php
session_start();
include "../db_conn.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
$subject = isset($_GET['subject']) ? $_GET['subject'] : '';

if (!$student_id || !$subject) {
    die("Invalid parameters.");
}

$stmt = $conn->prepare("
    SELECT sr.*, s.name 
    FROM student_results sr
    JOIN students s ON sr.student_id = s.student_id
    WHERE sr.student_id = ? AND sr.subject = ?
    ORDER BY sr.date_taken
");
$stmt->bind_param("is", $student_id, $subject);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("No results found for this student and subject.");
}

$student_name = '';
$attempts = [];
while ($row = $result->fetch_assoc()) {
    $student_name = $row['name'];
    $attempts[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Detailed Results for <?= htmlspecialchars($student_name) ?> - <?= htmlspecialchars($subject) ?></title>
    <link rel="stylesheet" href="../css/admin.css" />
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="sidebar">
  <!-- Your sidebar code here -->
</div>

<div class="main-content" style="padding: 20px;">
    <h1>Detailed Results for <?= htmlspecialchars($student_name) ?> - <?= htmlspecialchars($subject) ?></h1>

    <table>
        <thead>
            <tr>
                <th>Attempt #</th>
                <th>Score</th>
                <th>Total Score</th>
                <th>Percentage</th>
                <th>Date Taken</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attempts as $i => $attempt): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($attempt['score']) ?></td>
                <td><?= htmlspecialchars($attempt['total_score']) ?></td>
                <td><?= round(($attempt['score'] / $attempt['total_score']) * 100, 2) ?>%</td>
                <td><?= date('M d, Y H:i', strtotime($attempt['date_taken'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <canvas id="attemptChart" style="max-width: 600px; margin-top: 30px;"></canvas>

    <script>
        const ctx = document.getElementById('attemptChart').getContext('2d');
        const labels = <?= json_encode(array_map(fn($a) => date('M d, Y', strtotime($a['date_taken'])), $attempts)) ?>;
        const percentages = <?= json_encode(array_map(fn($a) => round(($a['score'] / $a['total_score']) * 100, 2), $attempts)) ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Score Percentage',
                    data: percentages,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });
    </script>

    <p><a href="View_Results.php">← Back to Results</a></p>
</div>

</body>
</html>
