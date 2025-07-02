<?php
session_start();
include "db_conn.php"; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs to prevent SQL injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Fetch the user record from the database using prepared statements
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();

        // Verify the entered password against the stored hash
        if (password_verify($password, $row['password'])) {
            // Set session variables upon successful login
            $_SESSION['name'] = $row['name'];
            $_SESSION['id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['username'] = $row['username'];

            // Redirect to the appropriate dashboard based on the role
            switch ($_SESSION['role']) {
                case 'admin':
                    header("Location: admin_dashboard.php");
                    break;
                case 'faculty':
                    header("Location: faculty_dashboard.php");
                    break;
                case 'student':
                    header("Location: student_dashboard.php");
                    break;
                default:
                    // Handle unknown roles
                    header("Location: index.php?error=Unknown role detected");
                    break;
            }
            exit();
        } else {
            // Incorrect password
            header("Location: index.php?error=Incorrect Username or Password");
            exit();
        }
    } else {
        // Username not found
        header("Location: index.php?error=Incorrect Username or Password");
        exit();
    }
} else {
    // Redirect back to login if accessed without POST
    header("Location: index.php");
    exit();
}
?>
