<?php
session_start();
include "../db_conn.php";

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

            // Get data from the row
            $student_id = $conn->real_escape_string($row[0]);
            $name = $conn->real_escape_string($row[1]);
            $contact_number = $conn->real_escape_string($row[2]);
            $year_section = $conn->real_escape_string($row[3]);
            $batch_year = $conn->real_escape_string($row[4]);

            // Insert into database
            $query = "INSERT INTO students (student_id, name, contact_number, year_section, batch_year)
                      VALUES ('$student_id', '$name', '$contact_number', '$year_section', '$batch_year')";
            $conn->query($query);
        }

        $_SESSION['success'] = "Students imported successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error processing file: " . $e->getMessage();
    }
}

header("Location: Manage_Student.php");
exit();
?>
