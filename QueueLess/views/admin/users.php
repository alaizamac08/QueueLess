<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - QueueLess</title>
</head>
<body>

    <div class="sidebar">
        <h1>User Management</h1>
        <a href="dashboard.php">Dashboard</a>
        <a href="users.php">Users</a>
        <a href="reports.php">Reports</a>
        <form action="/queueless/public/logout.php" method="post">
            <button type="submit">Logout</button>
        </form>
        <br>
    </div>
    


    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <tr>
            <td>1</td>
            <td>admin1</td>
            <td>Admin</td>
            <td>Active</td>
            <td>
                <button>Edit</button> 
                <button>Disable</button>
            </td>
        </tr>
    </table>
</body>
</html>