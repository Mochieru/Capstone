<?php 
session_start();
include "db_conn.php";

// Check if user is logged in
if (isset($_SESSION['username']) && isset($_SESSION['id'])) {
    $role = $_SESSION['role']; // Get user role

    // Redirect to specific pages based on role
    switch ($role) {
        case 'admin':
            // Admin content
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Admin Dashboard</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
            </head>
            <body>
                <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh">
                    <div class="card" style="width: 18rem;">
                        <img src="img/admin-default.png" class="card-img-top" alt="admin image">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?= htmlspecialchars($_SESSION['name']) ?></h5>
                            <a href="logout.php" class="btn btn-dark">Logout</a>
                        </div>
                    </div>
                    <div class="p-3">
                        <?php 
                        include 'php/members.php';
                        if (isset($res) && mysqli_num_rows($res) > 0) { ?>
                            <h1 class="display-4 fs-1">Members</h1>
                            <table class="table" style="width: 32rem;">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Username</th>
                                        <th scope="col">Role</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $i = 1;
                                    while ($rows = mysqli_fetch_assoc($res)) { ?>
                                        <tr>
                                            <th scope="row"><?= $i ?></th>
                                            <td><?= htmlspecialchars($rows['name']) ?></td>
                                            <td><?= htmlspecialchars($rows['username']) ?></td>
                                            <td><?= htmlspecialchars($rows['role']) ?></td>
                                        </tr>
                                        <?php $i++; } ?>
                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            </body>
            </html>
            <?php
            break;

        case 'faculty':
            // Faculty-specific content
            header("Location: faculty_dashboard.php");
            exit();

        case 'student':
            // Student-specific content
            header("Location: student_dashboard.php");
            exit();

        default:
            // Invalid role handling
            header("Location: index.php?error=Invalid role");
            exit();
    }
} else {
    // Redirect to login if not logged in
    header("Location: index.php");
    exit();
}
