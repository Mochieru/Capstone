<?php
session_start();
include "../db_conn.php";

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['role'])) {

    // Function to sanitize user input
    function test_input($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    $username = test_input($_POST['username']);
    $password = test_input($_POST['password']);
    $role = test_input($_POST['role']);

    if (empty($username)) {
        header("Location: ../index.php?error=Username is required");
        exit();
    }

    if (empty($password)) {
        header("Location: ../index.php?error=Password is required");
        exit();
    }

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verify the password (assumes password_hash was used during registration)
        if (password_verify($password, $row['password']) && $row['role'] === $role) {
            // Set session variables
            $_SESSION['name'] = $row['name'];
            $_SESSION['id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['username'] = $row['username'];

            // Redirect based on role
            switch ($row['role']) {
                case 'admin':
                    header("Location: ../admin_dashboard.php");
                    break;
                case 'faculty':
                    header("Location: ../faculty_dashboard.php");
                    break;
                case 'student':
                    header("Location: ../student_dashboard.php");
                    break;
                default:
                    header("Location: ../index.php?error=Invalid role");
            }
            exit();
        } else {
            header("Location: ../index.php?error=Incorrect username, password, or role");
            exit();
        }
    } else {
        header("Location: ../index.php?error=Incorrect username or password");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
