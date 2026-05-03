<?php
session_start();

require_once __DIR__ . '/../../app/controllers/UserController.php';
require_once __DIR__ . '/../../app/controllers/StaffController.php';
require_once __DIR__ . '/../../app/core/database.php';

$db = (new Database())->connect();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: Admin_Staff_Login.php");
    exit();
}

$userController = new UserController($db);
$staffController = new StaffController($db);

$user_id = $_SESSION['user_id'];

$user = $userController->getUser($user_id);
$logs = $userController->getUserLogs($user_id);

$staff = $staffController->getStaffByUserId($user_id);
$staffExists = $staff !== null;

// SAFE DEFAULT VALUES (prevents null errors)
$staff = $staff ?? [
    'first_name' => '',
    'middle_name' => '',
    'last_name' => '',
    'position' => '',
    'contact_number' => '',
    'email' => ''
];

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ACCOUNT UPDATE
    if (isset($_POST['update_account'])) {

        $username = $_POST['username'];
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $user = $userController->getUser($user_id);

        if (!password_verify($current_password, $user['password'])) {
            $error = "Current password is incorrect.";
        } elseif ($new_password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            $userController->updateUser($user_id, $username, $new_password);
            header("Location: profile.php");
            exit();
        }
    }

    // STAFF PROFILE SAVE (CREATE OR UPDATE)
    if (isset($_POST['update_profile'])) {

        $staffData = [
            'first_name' => $_POST['first_name'],
            'middle_name' => $_POST['middle_name'],
            'last_name' => $_POST['last_name'],
            'position' => $_POST['position'],
            'contact_number' => $_POST['contact_number'],
            'email' => $_POST['email']
        ];

        $staffController->saveStaff($user_id, $staffData);

        header("Location: profile.php");
        exit();
    }
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

                <h2>Staff Information</h2>

                <form method="POST" class="account-form">

                    <div class="user-account">
                        <label>First Name</label>
                        <input type="text" name="first_name" value="<?= $staff['first_name'] ?>">
                    </div>

                    <div class="user-account">
                        <label>Middle Name</label>
                        <input type="text" name="middle_name" value="<?= $staff['middle_name'] ?>">
                    </div>

                    <div class="user-account">
                        <label>Last Name</label>
                        <input type="text" name="last_name" value="<?= $staff['last_name'] ?>">
                    </div>

                    <div class="user-account">
                        <label>Position</label>
                        <input type="text" name="position" value="<?= $staff['position'] ?>">
                    </div>

                    <div class="user-account">
                        <label>Contact Number</label>
                        <input type="text" name="contact_number" value="<?= $staff['contact_number'] ?>">
                    </div>

                    <div class="user-account">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= $staff['email'] ?>">
                    </div>

                    <button type="submit" name="update_profile">
                        <?= $staffExists ? "Update Profile" : "Create Profile" ?>
                    </button>

                </form>


                    <h2>Account Security</h2>


                    <?php if ($error): ?>
                        <div class="error-box"><?= $error ?></div>
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

                        <button type="submit" name="update_account">Update Security</button>
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