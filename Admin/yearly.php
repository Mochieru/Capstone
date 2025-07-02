<?php
session_start();
include "../db_conn.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// 1. General yearly average scores (all subjects combined)
$generalResult = $conn->query("
    SELECT YEAR(date_taken) AS year, AVG(score / total_score) AS avg_percentage
    FROM student_results
    GROUP BY year
    ORDER BY year ASC
");
$generalData = $generalResult->fetch_all(MYSQLI_ASSOC);

// 2. Detailed average scores grouped by year and subject
$detailedResult = $conn->query("
    SELECT YEAR(date_taken) AS year, subject, AVG(score / total_score) AS avg_percentage
    FROM student_results
    GROUP BY year, subject
    ORDER BY year ASC, subject ASC
");

$rawData = $detailedResult->fetch_all(MYSQLI_ASSOC);

// Organize detailed data for table/chart
$data = [];
$subjects = [];
$years = [];

foreach ($rawData as $row) {
    $year = $row['year'];
    $subject = $row['subject'];
    $avg = $row['avg_percentage'];

    $data[$year][$subject] = $avg;
    if (!in_array($subject, $subjects)) {
        $subjects[] = $subject;
    }
    if (!in_array($year, $years)) {
        $years[] = $year;
    }
}

sort($years);
sort($subjects);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Yearly Results and Comparison</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h1>Yearly Results & Comparison</h1>

<h2>1. General Average Score Per Year (All Subjects Combined)</h2>
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Year</th>
            <th>Average Score (%)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($generalData as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['year']) ?></td>
                <td><?= round($row['avg_percentage'] * 100, 2) ?>%</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div style="width: 700px; height: 400px; margin-top: 20px;">
    <canvas id="generalAvgChart"></canvas>
</div>

<hr style="margin: 40px 0;" />

<h2>2. Detailed Average Score Per Year by Subject</h2>
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Subject</th>
            <?php foreach ($years as $year): ?>
                <th><?= htmlspecialchars($year) ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($subjects as $subject): ?>
            <tr>
                <td><?= htmlspecialchars($subject) ?></td>
                <?php foreach ($years as $year): ?>
                    <td>
                        <?php 
                        if (isset($data[$year][$subject])) {
                            echo round($data[$year][$subject] * 100, 2) . '%';
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div style="width: 900px; height: 450px; margin-top: 30px;">
    <canvas id="yearlySubjectChart"></canvas>
</div>

<script>
const generalData = <?= json_encode($generalData) ?>;
const years = <?= json_encode($years) ?>;
const subjects = <?= json_encode($subjects) ?>;
const detailedData = <?= json_encode($data) ?>;

// General chart for all subjects combined
const ctxGeneral = document.getElementById('generalAvgChart').getContext('2d');
new Chart(ctxGeneral, {
    type: 'line',
    data: {
        labels: generalData.map(row => row.year),
        datasets: [{
            label: 'Average Score (%)',
            data: generalData.map(row => +(row.avg_percentage * 100).toFixed(2)),
            borderColor: 'rgba(54, 162, 235, 1)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: false,
        scales: {
            y: { beginAtZero: true, max: 100, title: {display: true, text: 'Average Score (%)'} },
            x: { title: {display: true, text: 'Year'} }
        }
    }
});

// Detailed per-subject chart
const datasets = subjects.map(subject => ({
    label: subject,
    data: years.map(year => (detailedData[year] && detailedData[year][subject]) ? +(detailedData[year][subject] * 100).toFixed(2) : null),
    borderColor: getRandomColor(),
    backgroundColor: 'transparent',
    spanGaps: true,
    tension: 0.3
}));

function getRandomColor() {
    const r = Math.floor(Math.random() * 155) + 100;
    const g = Math.floor(Math.random() * 155) + 100;
    const b = Math.floor(Math.random() * 155) + 100;
    return `rgba(${r}, ${g}, ${b}, 1)`;
}

const ctxDetailed = document.getElementById('yearlySubjectChart').getContext('2d');
new Chart(ctxDetailed, {
    type: 'line',
    data: {
        labels: years,
        datasets: datasets
    },
    options: {
        responsive: false,
        plugins: {
            title: { display: true, text: 'Average Scores per Subject Over the Years (%)' },
            tooltip: { mode: 'index', intersect: false },
            legend: { position: 'bottom' }
        },
        interaction: { mode: 'nearest', axis: 'x', intersect: false },
        scales: {
            y: { beginAtZero: true, max: 100, title: {display: true, text: 'Average Score (%)'} },
            x: { title: {display: true, text: 'Year'} }
        }
    }
});
</script>

</body>
</html>
