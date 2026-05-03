<?php 
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: Admin_Staff_Login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - QueueLess</title>
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
        <a href="enrollments.php">Enrollments</a>
        <a href="profile.php">Profile</a>

        <form action="/queueless/public/logout.php" method="post">
            <button type="submit">Logout</button>
        </form>
    </div>

    <div class="main-about profile-main">
        <div class="dashboard-main">
            <h1>Staff Dashboard</h1>

            <div class="cards">

                <div class="card">
                    <h3>Assigned</h3>
                    <p><?= $assigned ?? 0 ?></p>
                </div>

                <div class="card">
                    <h3>Pending</h3>
                    <p><?= $pending ?? 0 ?></p>
                </div>

            </div>

            <div class="table-section">
                <h2>Quick Actions</h2>

                <table>
                    <tr>
                        <th>Action</th>
                        <th>Link</th>
                    </tr>

                    <tr>
                        <td>View Enrollments</td>
                        <td><a href="enrollments.php" class="action-link">Open</a></td>
                    </tr>

                    <tr>
                        <td>View Profile</td>
                        <td><a href="profile.php" class="action-link">Open</a></td>
                    </tr>
                </table>
            </div>

        </div>
    </div>

</body>
</html>