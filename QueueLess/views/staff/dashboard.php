<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - QueueLess</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="sidebar">
        <h2>QUEUELESS</h2>

        <a href="dashboard.php">Dashboard</a>
        <a href="enrollments.php">Enrollments</a>
        <a href="profile.php">Profile</a>
        <form action="/queueless/public/logout.php" method="post">
            <button type="submit">Logout</button>
        </form>
    </div>

    <div class="main">
        <h1>Staff Dashboard</h1>

        <div class="cards">

            <div class="card">
                <h3>Assigned Enrollments</h3>
                <p>5</p>
            </div>

            <div class="card">
                <h3>Pending Reviews</h3>
                <p>2</p>
            </div>

            <div class="card">
                <h3>Completed Today</h3>
                <p>8</p>
            </div>
        </div>

        <div class="table-section">
            <h2>My Assigned Enrollments</h2>

            <table>
                <tr>
                    <th>Student Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>

                <tr>
                    <td>John Doe</td>
                    <td>Pending</td>
                    <td><button>Review</button></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>