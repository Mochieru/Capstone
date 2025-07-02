<?php
session_start();
include "../db_conn.php"; // Database connection

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Faculty</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>


     <div class="sidebar">
  <div class="logo" style="display: flex; align-items: center;">
    <img src="/Project/images/LOGOhd_NoLetters.png" alt="PAEES LOGO" width="65" height="65" style="margin-right: 15px;">
    <span style="font-size: 45px; font-weight: bold; color: #262A39;">
      PAAES
    </span>
  </div>
  <div class="nav-menu">
    <a href="/Project/admin_dashboard.php" class="nav-item">
      <i class="fas fa-home"></i>
      <span>Dashboard</span>
    </a>
    <a href="/Project/Admin/Manage_Faculty.php" class="nav-item active">
      <i class="fa-solid fa-users"></i>
      <span>Manage Faculty</span>
    </a>
    <a href="/Project/Admin/Manage_Announcement.php" class="nav-item">
      <i class="fa-solid fa-bullhorn"></i>
      <span>Manage Announcement</span>
    </a>
    <a href="/Project/Admin/View_Results.php" class="nav-item">
      <i class="fa-solid fa-calendar"></i>
      <span>View Student Results</span>
    </a>
  </div>
  <button class="premium-btn">
    <a href="/Project/logout.php">Logout</a>
  </button>
</div>


    <div class="main-content">
        <div class="header">
            <h1>Manage Faculty</h1>
        </div>

        <div class="content">
            <!-- Add Faculty Form -->
            <div class="add-faculty-form">
                <h2>Add Faculty</h2>
                <form action="add_faculty.php" method="POST">
                    <label for="faculty-name">Name:</label>
                    <input type="text" id="faculty-name" name="faculty_name" placeholder="Enter name" required>
                    
                    <label for="faculty-email">Email:</label>
                    <input type="email" id="faculty-email" name="faculty_email" placeholder="Enter email" required>
                    
                    <label for="faculty-department">Department:</label>
                    <select id="faculty-department" name="faculty_department" required>
                        <option value="">Select Department</option>
                        <option value="Aeronautical Engineering">Aeronautical Engineering</option>
                        <option value="Flight Operations">Flight Operations</option>
                        <option value="Aircraft Maintenance">Aircraft Maintenance</option>
                    </select>
                    
                    <button type="submit" class="btn">Add Faculty</button>
                </form>
            </div>

            <!-- Faculty List -->
            <div class="faculty-table-section">
                <h2>Faculty Members</h2>
                <table class="faculty-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Faculty ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
$query = "SELECT * FROM faculty";
$result = mysqli_query($conn, $query);
$index = 1;
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>{$index}</td>
            <td>{$row['faculty_id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['department']}</td>
            <td>
                <a href='edit_faculty.php?id={$row['faculty_id']}' class='btn edit'>
                  <i class='fa-solid fa-pen'></i> Edit
                </a>
                <a href='delete_faculty.php?faculty_id={$row['faculty_id']}' class='btn delete' onclick='return confirm(\"Are you sure?\");'>
                  <i class='fa-solid fa-trash'></i> Delete
                </a>
            </td>
          </tr>";
    $index++;
}
?>
</tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
