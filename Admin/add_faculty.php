<?php
include "../db_conn.php";

// Fetch the last faculty ID
$query = "SELECT faculty_id FROM faculty ORDER BY faculty_id DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Generate a new unique faculty ID
if ($row) {
    $last_id = $row['faculty_id'];
    $id_number = (int)substr($last_id, 4) + 1;
    $new_id = "FAC-" . str_pad($id_number, 4, "0", STR_PAD_LEFT);
} else {
    $new_id = "FAC-0001"; // Default ID if no records exist
}

// Get form data
$faculty_name = $_POST['faculty_name'];
$faculty_email = $_POST['faculty_email'];
$faculty_department = $_POST['faculty_department'];

// Insert new faculty into the database
$query = "INSERT INTO faculty (faculty_id, name, email, department) 
          VALUES ('$new_id', '$faculty_name', '$faculty_email', '$faculty_department')";
if (mysqli_query($conn, $query)) {
    header("Location: Manage_Faculty.php?success=Faculty added successfully");
} else {
    header("Location: Manage_Faculty.php?error=Failed to add faculty");
}
exit();
?>
