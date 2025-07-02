<?php
session_start();
include "db_conn.php"; // Ensure the database connection is included

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PAEES Admin Dashboard</title>
     <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  </head>
  <body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="logo" style="display: flex; align-items: center;">
    <img src="/Project/images/LOGOhd_NoLetters.png" alt="PAEES LOGO" width="65" height="65" style="margin-right: 15px;">
    <span style="font-size: 45px; font-weight: bold; color: #262A39;">
      PAAES
    </span>
  </div>
  <div class="nav-menu">
    <a href="/Project/admin_dashboard.php" class="nav-item active">
      <i class="fas fa-home"></i>
      <span>Dashboard</span>
    </a>
    <a href="/Project/Admin/Manage_Faculty.php" class="nav-item">
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

    <!-- Main Content -->
    <div class="main-content">
      <!-- Header -->
      <div class="header">
        <div class="welcome-section" style="display: flex; align-items: center; justify-content: center;">
          <img src="images/PHILSCA Logo.png" alt="PAEES LOGO" width="65" height="100" style="margin-right: 10px;">
          <span style="font-size: 14px; color: #262A39; line-height: 20px;">
            Philippine State College of Aeronautics <br> Piccio Garden, Pasay City <br>
            Republic of the Philippines <br> Institute of Engineering & Technology
          </span>
          <hr>
          <h1 class="welcome-title" style="align: center;">Admin Dashboard</h1>
        </div>
        <div class="header-right">
          
          
          
          <div class="user-profile">
            <img src="https://i.pravatar.cc/100?img=8" alt="Admin" class="profile-pic" />
          </div>
        </div>
      </div>



      <!-- Dashboard Container -->
      <div class="dashboard-container">
        <!-- Main Dashboard Content -->
        <div class="dashboard-main">
          <!-- Transfer Cards -->
          <div class="transfer-cards">
            <div class="transfer-card">
              <div class="card-icon">
              <i class="fas fa-chalkboard-teacher"></i>
              </div>
              <p class="card-title">Total Faculty</p>
              <h2 class="card-amount">20</h2>
            </div>

            <div class="transfer-card">
              <div class="card-icon">
              <i class="fas fa-user-check"></i>
              </div>
              <p class="card-title">Total Students</p>
              <h2 class="card-amount">100</h2>
            </div>

            <div class="transfer-card">
              <div class="card-icon">
                <i class="fa-solid fa-bullhorn"></i>
              </div>
              <p class="card-title">Announcement</p>
              <h2 class="card-amount">5 Examination on Monday, 08:00</h2>
            </div>
          </div>


            

   

    <div class="faculty-updates">
      <h2>Recent Faculty Updates</h2> <br> <br>
      <table class="faculty-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Department</th>
            <th>Last Activity</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Dr. Jane Doe</td>
            <td>Aeronautical Engineering</td>
            <td>April 25, 2025</td>
            <td><span class="status-active">Active</span></td>
          </tr>
          <tr>
            <td>Mr. John Smith</td>
            <td>Flight Operations</td>
            <td>April 22, 2025</td>
            <td><span class="status-inactive">Inactive</span></td>
          </tr>
          <tr>
            <td>Ms. Emily Davis</td>
            <td>Aircraft Maintenance</td>
            <td>April 18, 2025</td>
            <td><span class="status-active">Active</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </body>
</html>