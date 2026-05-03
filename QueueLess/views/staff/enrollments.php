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
    <title>Enrollments - QueueLess</title>
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
            <h1>Enrollment Requests</h1>

            <div class="table-section">
                <h2>Pending Requests</h2>

                <table>
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Grade Level</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                    <tr>
                        <td>101</td>
                        <td>John Doe</td>
                        <td>10</td>
                        <td><span class="status-pending">Pending</span></td>
                        <td>
                            <button class="btn-approve">Approve</button>
                            <button class="btn-reject">Reject</button>
                            <button class="btn-view">View</button>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
    </div>

</body>
</html>