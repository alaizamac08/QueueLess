<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: Admin_Staff_Login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - QueueLess</title>
    <link rel="stylesheet" href="../../public/assets/css/cherry.css">
</head>

<body class="no-navbar">

    <!-- SIDEBAR -->
    <div class="sidebar">

        <div class="logo">
            <a href="main_page.html">
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

    <!-- MAIN CONTENT -->
    <div class="main-about profile-main">
        <div class="dashboard-main">

            <h1>User Management</h1>

            <div class="table-section">

                <h2>All Users</h2>

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
                            <div class="action-buttons">
                                <button class="btn-edit">Edit</button>
                                <button class="btn-disable" onclick="openDisableModal(1)">Disable</button>
                            </div>
                        </td>
                    </tr>

                </table>

            </div>

        </div>
    </div>




    <div class="modal" id="disableModal">
        <div class="modal-content">
            <h2>Confirm Action</h2>
            <p>Are you sure you want to disable this user?</p>
            
            <div class="modal-actions">
                <button class="btn-confirm" onclick="confirmDisable()">Yes, Disable</button>
                <button class="btn-cancel" onclick="closeDisableModal()">Cancel</button>
            </div>
        </div>
    </div>


    <script>
        document.getElementById('disableModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDisableModal();
            }
        });

        let selectedUserId = null;

        function openDisableModal(userId) {
            selectedUserId = userId;
            document.getElementById('disableModal').style.display = 'flex';
        }

        function closeDisableModal() {
            selectedUserId = null;
            document.getElementById('disableModal').style.display = 'none';
        }

        function confirmDisable() {
            // Here you would typically make an AJAX request to disable the user
            console.log('User ID to disable:', selectedUserId);
            closeDisableModal();
        }
    </script>
</body>
</html>