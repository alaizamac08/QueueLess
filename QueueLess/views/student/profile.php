<?php
session_start();

require_once __DIR__ . '/../../app/core/database.php';
require_once __DIR__ . '/../../app/controllers/UserController.php';

$db = (new Database())->connect();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$userController = new UserController($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['update_name'])) {
        $userController->updateDisplayName($userId, $_POST['display_name']);
    }

    if (isset($_POST['change_password'])) {
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            $userController->changePassword($userId, $_POST['new_password']);
        }
    }

    if (isset($_POST['upload_profile'])) {
        $userController->uploadProfilePicture($userId, $_FILES['profile_picture']);
    }

    header("Location: profile.php");
    exit();
}

$stmt = $db->prepare("
    SELECT u.username, p.display_name, p.profile_picture
    FROM users u
    LEFT JOIN user_profiles p ON u.user_id = p.user_id
    WHERE u.user_id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/assets/css/cherry.css">
</head>

<body class="admin-dashboard">

<div class="sidebar">
    <h2>Hello, <?php echo $user['display_name'] ?? $user['username']; ?></h2>

    <a href="dashboard.php">Dashboard</a>
    <a href="profile.php">Profile</a>

    <form method="POST" action="/queueless/public/logout.php">
        <button>Logout</button>
    </form>
</div>

<div class="dashboard-main">

    <div class="topbar">
        <h1>Profile Settings</h1>
    </div>

    <div class="cards">

        <div class="card profile-card">

            <?php if (!empty($user['profile_picture'])): ?>
                <img src="../../public/<?php echo $user['profile_picture']; ?>">
            <?php else: ?>
                <img src="../../public/assets/images/default.png">
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_picture">
                <button name="upload_profile">Upload</button>
            </form>

        </div>

        <div class="card">
            <h3>Display Name</h3>

            <form method="POST">
                <input type="text" name="display_name"
                       value="<?php echo $user['display_name'] ?? ''; ?>" required>

                <button name="update_name">Save</button>
            </form>
        </div>

        <div class="card">
            <h3>Password</h3>

            <form method="POST">
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>

                <button name="change_password">Update</button>
            </form>
        </div>

    </div>

</div>

</body>
</html>