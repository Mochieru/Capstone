<?php
session_start();

$db_conn_path = "db_conn.php";
if (file_exists($db_conn_path)) {
    include $db_conn_path;
} else {
    die("Database connection file not found.");
}

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit();
}

// Include PhpSpreadsheet library
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['student_excel'])) {
    $file = $_FILES['student_excel']['tmp_name'];

    try {
        // Load the spreadsheet
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        // Skip the header row
        for ($i = 1; $i < count($data); $i++) {
            $row = $data[$i];
            // Insert into database logic...
        }

        $_SESSION['success'] = "Students imported successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error processing file: " . $e->getMessage();
    }
}

header("Location: Manage_Student.php");
exit();
