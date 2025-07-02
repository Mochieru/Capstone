<?php
// Start the session if not already started
session_start();
include "db_conn.php"; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Assuming you're receiving form data via POST method
    $username = $_POST['username'];
    $password = $_POST['password']; // The raw password entered by the user
    $role = $_POST['role']; // User role (admin, faculty, student)
    $name = $_POST['name']; // Full name of the user

    // Validate input (optional)
    if (empty($username) || empty($password) || empty($role) || empty($name)) {
        header("Location: register.php?error=All fields are required");
        exit();
    }

    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL query to insert the user into the database
    $sql = "INSERT INTO users (username, password, role, name) VALUES ('$username', '$hashed_password', '$role', '$name')";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        // Registration successful
        header("Location: login.php?success=Registration successful! You can log in now.");
        exit();
    } else {
        // Query failed
        header("Location: register.php?error=Registration failed. Please try again.");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh">
        <div class="border p-4 rounded bg-white shadow-sm" style="max-width: 400px; width: 100%;">
            <h1 class="text-center mb-4">Register</h1>
            <?php if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php } ?>
            <form action="register.php" method="post">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="admin">Admin</option>
                        <option value="faculty">Faculty</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
            <p class="mt-3 text-center">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
