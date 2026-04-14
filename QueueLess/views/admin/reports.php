<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - QueueLess</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="sidebar">
        <h2>QUEUELESS</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="users.php">Users</a>
        <a href="reports.php">Reports</a>
        <form action="/queueless/public/logout.php" method="post">
            <button type="submit">Logout</button>
        </form>
    </div>

    <div class="main">
        <h1>System Reports</h1>

        <!-- Filter Section -->
        <div class="card">
            <h3>Filter Reports</h3>

            <form method="get">
                <label>From:</label>
                <input type="date" name="from">

                <label>To:</label>
                <input type="date" name="to">

                <button type="submit">Generate Report</button>
            </form>
        </div>

        <!-- Summary -->
        <div class="cards">

            <div class="card">
                <h3>Total Enrollments</h3>
                <p>120</p>
            </div>

            <div class="card">
                <h3>Approved</h3>
                <p>90</p>
            </div>

            <div class="card">
                <h3>Pending</h3>
                <p>23</p>
            </div>

            <div class="card">
                <h3>Rejected</h3>
                <p>2</p>
            </div>
        </div>

        <!-- Table Report -->
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

                <tr>
                    <td>101</td>
                    <td>John Doe</td>
                    <td>Pending</td>
                    <td>2026-04-10</td>
                    <td>Staff1</td>
                </tr>
            </table>
         </div>
    </div>
</body>
</html>