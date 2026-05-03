<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: Admin_Staff_Login.php");
    exit();
}

require_once __DIR__ . '/../../app/core/database.php';
require_once __DIR__ . '/../../app/controllers/ReportController.php';

$db = (new Database())->connect();

$controller = new ReportController($db);

$from = $_GET['from'] ?? date('Y-01-01');
$to = $_GET['to'] ?? date('Y-m-d');

$summary = $controller->getSummary($from, $to);
$detail = $controller->getDetailed($from, $to);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - QueueLess</title>
    <link rel="stylesheet" href="../../public/assets/css/cherry.css">
</head>

<body class="no-navbar">

    <div class="sidebar">
        <div class="logo">
            <a href="../main/main_page.html">
                <img src="../../public/assets/images/queueless.png" alt="QueueLess Logo">
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

            <h1>System Reports</h1>

            <!-- Filter -->
            <div class="card">
                <h3>Filter Reports</h3>

                <form method="get" class="report-filter-form">
                    <div class="filter-group">
                        <label>From:</label>
                        <input type="date" name="from" value="<?= $from ?>">
                    </div>

                    <div class="filter-group">
                        <label>To:</label>
                        <input type="date" name="to" value="<?= $to ?>">
                    </div>

                    <button type="submit">Generate</button>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="cards">

                <div class="card">
                    <h3>Total Enrollments</h3>
                    <p><?= $summary['total'] ?? 0 ?></p>
                </div>

                <div class="card">
                    <h3>Approved</h3>
                    <p><?= $summary['approved'] ?? 0 ?></p>
                </div>

                <div class="card">
                    <h3>Pending</h3>
                    <p><?= $summary['pending'] ?? 0 ?></p>
                </div>

                <div class="card">
                    <h3>Rejected</h3>
                    <p><?= $summary['rejected'] ?? 0 ?></p>
                </div>

            </div>

            <!-- Table -->
            <div class="table-section">

                <h2>Detailed Report</h2>

                <table>
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Status</th>
                        <th>Date Submitted</th>
                        <th>Processed By</th>
                    </tr>

                    <?php if ($detail && $detail->num_rows > 0): ?>
                        <?php while ($row = $detail->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['enrollment_id'] ?></td>
                                <td><?= $row['student_name'] ?></td>
                                <td><?= $row['status'] ?></td>
                                <td><?= $row['created_at'] ?></td>
                                <td><?= $row['username'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No records found</td>
                        </tr>
                    <?php endif; ?>

                </table>

            </div>

        </div>
    </div>

</body>
</html>