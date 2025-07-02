<?php
session_start();
include "../db_conn.php";
include "announcement_functions.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$success_msg = "";
$error_msg = "";

// Handle new announcement submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_announcement'])) {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $status = $_POST['status'] === 'Inactive' ? 'Inactive' : 'Active';
    $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;

    if ($title === "" || $message === "") {
        $error_msg = "Title and message cannot be empty.";
    } else {
        if (addAnnouncement($conn, $title, $message, $status, $deadline)) {
            $success_msg = "Announcement added successfully.";
        } else {
            $error_msg = "Failed to add announcement.";
        }
    }
}

// Handle delete announcement
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    if (deleteAnnouncement($conn, $delete_id)) {
        $success_msg = "Announcement deleted successfully.";
    } else {
        $error_msg = "Failed to delete announcement.";
    }
}

// Fetch announcements
$result = $conn->query("SELECT * FROM announcements ORDER BY last_updated DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PAEES Admin Dashboard - Manage Announcements</title>
    <link rel="stylesheet" href="../css/admin.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    />
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
    <a href="/Project/Admin/Manage_Faculty.php" class="nav-item">
      <i class="fa-solid fa-users"></i>
      <span>Manage Faculty</span>
    </a>
    <a href="/Project/Admin/Manage_Announcement.php" class="nav-item active">
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
        <div class="welcome-section" style="display: flex; align-items: center; justify-content: center">
          <img src="../images/PHILSCA Logo.png" alt="PAEES LOGO" width="65" height="100" style="margin-right: 10px" />
          <span style="font-size: 14px; color: #262a39; line-height: 20px; text-align: center">
            Philippine State College of Aeronautics <br />
            Piccio Garden, Pasay City <br />
            Republic of the Philippines <br />
            Institute of Engineering & Technology
          </span>
          <hr />
          <h1 class="welcome-title" style="text-align: center">Admin Dashboard - Manage Announcements</h1>
        </div>
        <div class="header-right">
          <div class="user-profile">
            <img src="https://i.pravatar.cc/100?img=8" alt="Admin" class="profile-pic" />
          </div>
        </div>
      </div>

      <div class="dashboard-container" style="padding: 20px;">
        <?php if ($success_msg): ?>
          <div class="message success" style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <?= htmlspecialchars($success_msg) ?>
          </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
          <div class="message error" style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <?= htmlspecialchars($error_msg) ?>
          </div>
        <?php endif; ?>

        <table class="faculty-table" style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr>
              <th>Title</th>
              <th>Message</th>
              <th>Date Posted</th>
              <th>Last Updated</th>
              <th>Deadline</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($row['title']) ?></td>
                  <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                  <td><?= date('M d, Y H:i', strtotime($row['date_posted'])) ?></td>
                  <td><?= date('M d, Y H:i', strtotime($row['last_updated'])) ?></td>
                  <td><?= $row['deadline'] ? date('M d, Y H:i', strtotime($row['deadline'])) : "<i>None</i>" ?></td>
                  <td><?= htmlspecialchars($row['status']) ?></td>
                  <td>
                    <a href="edit_announcement.php?id=<?= $row['announcement_id'] ?>" class="btn edit" style="margin-right: 8px"><i class="fa-solid fa-pen"></i> Edit</a>
                    <a href="Manage_Announcement.php?delete_id=<?= $row['announcement_id'] ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this announcement?');"><i class="fa-solid fa-trash"></i> Delete</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="7" style="text-align: center">No announcements found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>

        <h2 style="margin-top: 40px;">Add New Announcement</h2>
        <form method="post" action="Manage_Announcement.php" style="max-width: 600px;">
          <label for="title">Title:</label>
          <input type="text" id="title" name="title" required style="width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc" />

          <label for="message">Message:</label>
          <textarea id="message" name="message" rows="5" required style="width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc"></textarea>

          <label for="deadline">Deadline (optional):</label>
          <input type="datetime-local" id="deadline" name="deadline" style="width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc" />

          <label for="status">Status:</label>
          <select id="status" name="status" required style="width: 100%; padding: 8px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #ccc">
            <option value="Active" selected>Active</option>
            <option value="Inactive">Inactive</option>
          </select>

          <button type="submit" name="add_announcement" style="background-color: #262a39; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
            Add Announcement
          </button>
        </form>
      </div>
    </div>
  </body>
</html>
