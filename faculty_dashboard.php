<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PAEES Faculty Dashboard</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    />
    <link rel="stylesheet" href="css/faculty.css" />
  </head>
  <body>
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="logo" style="display: flex; align-items: center;">
        <img src="images/LOGOhd_NoLetters.png" alt="PAEES LOGO" width="65" height="65" style="margin-right: 15px;" />
        <span style="font-size: 45px; font-weight: bold; color: #262A39;">PAAES</span>
      </div>
      <div class="nav-menu">
        <a href="faculty_dashboard.php" class="nav-item active">
          <i class="fas fa-home"></i>
          <span>Home</span>
        </a>
        <a href="/Project/Faculty/Manage_Student.php" class="nav-item">
          <i class="fa-solid fa-users"></i>
          <span>Manage Student</span>
        </a>
        <a href="/Project/Faculty/Faculty_Announcement.php" class="nav-item">
          <i class="fa-solid fa-bullhorn"></i>
          <span>Announcement</span>
        </a>
        <a href="/Project/Faculty/Examination_List.php" class="nav-item">
          <i class="fas fa-sliders-h"></i>
          <span>Examination</span>
        </a>
        <a href="/Project/Faculty/Results.php" class="nav-item">
          <i class="fas fa-chart-line"></i>
          <span>Results</span>
        </a>
        <a href="/Project/Faculty/Feedbacks.php" class="nav-item">
          <i class="fas fa-comment-dots"></i>
          <span>Comments & Feedbacks</span>
        </a>
        <a href="/Project/Faculty/Manage_Account.php" class="nav-item">
          <i class="fa-solid fa-user"></i>
          <span>Manage Account</span>
        </a>
        <a href="/Project/Faculty/Calendar.php" class="nav-item">
          <i class="fa-solid fa-calendar-days"></i>
          <span>Calendar</span>
        </a>
      </div>
      <button class="premium-btn"><a href="logout.php">Logout</a></button>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Header -->
      <div class="header">
        <div class="welcome-section">
          <img src="images/PHILSCA Logo.png" alt="PAEES LOGO" width="65" height="100" style="margin-right: 10px;" />
          <div>
            <span style="font-size: 14px; color: #262A39;">
              Philippine State College of Aeronautics <br />
              Piccio Garden, Pasay City <br />
              Republic of the Philippines <br />
              Institute of Engineering & Technology
            </span>
            <h1 class="welcome-title" style="text-align: center;">Faculty Dashboard</h1>
          </div>
        </div>
        <div class="header-right">
          <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" class="profile-pic" />
        </div>
      </div>

      <!-- Dashboard Container -->
      <div class="dashboard-container">
        <!-- Main Dashboard Content -->
        <div class="dashboard-main">
          <!-- Cards -->
          <div class="transfer-cards">
            <div class="transfer-card">
              <div class="card-icon">
                <i class="fa-solid fa-globe"></i>
              </div>
              <p class="card-title">Exam Status</p>
              <h2 class="card-amount"><?php echo $exam_status; ?></h2>
            </div>
            <div class="transfer-card">
              <div class="card-icon">
                <i class="fa-solid fa-chart-line"></i>
              </div>
              <p class="card-title">Previous Exam Results</p>
              <h2 class="card-amount"><?php echo $previous_exam_results; ?></h2>
            </div>
            <div class="transfer-card">
              <div class="card-icon">
                <i class="fa-solid fa-bullhorn"></i>
              </div>
              <p class="card-title">Announcement</p>
              <h2 class="card-amount"><?php echo $announcement; ?></h2>
            </div>
          </div>

          <!-- Promo Section -->
          <div class="promo-card">
            <h2 class="promo-title">Announcement</h2>
            <p class="promo-desc"><?php echo $promo_desc; ?></p>
            <button class="promo-btn">Check Calendar</button>
          </div>

          <!-- Sections -->
          <div class="transaction-section">
            <div class="transaction-card">
              <h3>Examination Deadline</h3>
              <?php echo $examination_deadlines; ?>
            </div>
            <div class="transaction-card">
              <h3>Recent Activities</h3>
              <?php echo $recent_activities; ?>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="dashboard-sidebar">
          <div class="savings-card">
            <h3>Examination Results Received</h3>
            <div class="savings-amount"><?php echo $exam_results_received; ?></div>
          </div>
          <div class="plan-card">
            <div class="plan-info">
              <h3>Goal Q1 2025</h3>
              <p>Status: <?php echo $goal_status; ?></p>
              <p>Progress: <?php echo $goal_progress; ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
