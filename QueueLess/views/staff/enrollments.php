<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollments - QueueLess</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

    <div class="sidebar">
        <h1>Enrollment Requests</h1>

        <a href="dashboard.php">Dashboard</a>
        <a href="enrollments.php">Enrollments</a>
        <a href="profile.php">Profile</a>
        <form action="/queueless/public/logout.php" method="post">
            <button type="submit">Logout</button>
        </form>
    </div>


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
        <td>Pending</td>
        <td>
            <button>Approve</button>
            <button>Reject</button>
            <button>View Details</button>
        </td>
    </tr>
</table>
</body>
</html>