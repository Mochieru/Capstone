<?php
include 'check-login.php'; // Ensure the user is logged in

// Check if the user is an admin before showing the members list
if ($_SESSION['role'] != 'admin') {
    header("Location: home.php"); // Redirect to home if not an admin
    exit();
}

include 'db_conn.php';  // Include database connection

$sql = "SELECT * FROM users ORDER BY id ASC";  // Fetch all users
$res = mysqli_query($conn, $sql);  // Execute the query
?>

<!DOCTYPE html>
<html>
<head>
    <title>Members</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Members List</h1>

        <?php if (mysqli_num_rows($res) > 0) { ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($res)) {
                        echo "<tr>
                                <td>" . $i++ . "</td>
                                <td>" . htmlspecialchars($row['name']) . "</td>
                                <td>" . htmlspecialchars($row['username']) . "</td>
                                <td>" . htmlspecialchars($row['role']) . "</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p>No members found.</p>
        <?php } ?>
    </div>
</body>
</html>
