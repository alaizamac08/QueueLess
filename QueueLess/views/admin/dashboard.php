<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: Admin_Staff_LogIn.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - QueueLess</title>
    <link rel="stylesheet" href="../../public/assets/css/cherry.css">
</head>
<body class="no-navbar">
    <div class="sidebar">
        <div class="logo">
            <a href="../main/main_page.html">
                <img src="../../public/assets/images/queueless.png" alt="Queueless Logo">
            </a>
        </div>
        <a href="dashboard.php">Dashboard</a>
        <a href="users.php">Users</a>
        <a href="reports.php">Reports</a>
        <form action="/queueless/public/logout.php" method="post">
            <button type="submit">Logout</button>
        </form>
    </div>

    <div class="main-about profile-main">
        <div class="dashboard-main">
            <h1>Admin Dashboard</h1>

            <div class="cards">

                <div class="card">
                    <h3>Total Users</h3>
                    <p>120</p>
                </div>

                <div class="card">
                    <h3>Pending Enrollments</h3>
                    <p>35</p>
                </div>

                <div class="card">
                    <h3>Approved Enrollments</h3>
                    <p>8</p>
                </div>

                    <div class="card">
                        <h3>Rejected Enrollments</h3>
                        <p>12</p>
                    </div>

            </div>

            <div class="table-section">
                <h2>Recent Activity</h2>
                <table>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Date</th>
                    </tr>

                    <tr>
                        <td>Staff1</td>
                        <td>Approved Enrollment</td>
                        <td>2026-04-13 10:00 AM</td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</body>
</html>