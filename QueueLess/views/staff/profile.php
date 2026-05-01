<?php
session_start();

require_once __DIR__ . '/../../app/controllers/UserController.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: Admin_Staff_LogIn.php");
    exit();
}

$userController = new UserController();

$user_id = $_SESSION['user_id'];
$user = $userController->getUser($user_id);

// assume logs come from controller
$logs = $userController->getUserLogs($user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // fetch fresh user (important for validation)
    $user = $userController->getUser($user_id);

    // verify current password
    if (!password_verify($current_password, $user['password'])) {
        die("Current password is incorrect.");
    }

    // confirm match
    if ($new_password !== $confirm_password) {
        die("New passwords do not match.");
    }

    // update
    $userController->updateUser($user_id, $username, $new_password);

    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - QueueLess</title>
    <link rel="stylesheet" href="../../public/assets/css/cherry.css">
</head>
<body class="no-navbar">

    <div class="main-about profile-main">
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

            <div class="dashboard-main">
                <h1>My Profile</h1>

                <!-- Profile Card -->
                <div class="card">
                    <h3>Account Security</h3>


                    <?php if (isset($error)): ?>
                        <div class="error-box"><?= $error = "Current password is incorrect." ?></div>
                    <?php endif; ?>

                    <form method="POST" class="account-form">

                        <div class="user-account">
                            <label>Username</label>
                            <input type="text" name="username" value="<?= $user['username'] ?>" required>
                        </div>

                        <div class="user-account">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required>
                        </div>

                        <div class="user-account">
                            <label>New Password</label>
                            <input type="password" name="new_password" required>
                        </div>

                        <div class="user-account">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" required>
                        </div>

                        <button type="submit">Update Security</button>
                    </form>

                </div>


                <!-- Activity Logs -->
            <div class="table-section">
                <h2>Activity Log</h2>

                <table>
                    <tr>
                        <th>Action</th>
                        <th>Date</th>
                    </tr>

                <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= $log['action'] ?></td>
                        <td><?= $log['created_at'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="2">No activity yet</td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>

            </div>

        </div>
    </div>

</body>
</html>