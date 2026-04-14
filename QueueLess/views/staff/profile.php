<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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
        <h1>My Profile</h1>

        <div class="card">
            <form method="post">
                <label>Username</label><br>
                <input type="text" name="username" value="staff1"><br><br>

                <label>Password</label><br>
                <input type="password" name="password" placeholder="Enter new password"><br><br>

                <label>Status</label>
                <input type="text" value="Active" disabled><br><br>

                <button type="submit">Update Profile</button>
            </form>
        </div>

        <!-- Activity Log -->
         <div class="table-section">
            <h2>My Activity</h2>

            <table>
                <tr>
                    <th>Action</th>
                    <th>Date</th>
                </tr>

                <tr>
                    <td>Approved Enrollment</td>
                    <td>2026-04-13</td>
                </tr>
                
                <tr>
                    <td>Rejected Enrollment</td>
                    <td>2026-04-11</td>
                </tr>
            </table>
         </div>
    </div>
</body>
</html>