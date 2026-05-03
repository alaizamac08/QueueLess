<?php
session_start();

include(__DIR__ . "/../app/core/database.php");
include(__DIR__ . "/../app/core/Logger.php");

$db = (new Database())->connect();

if (!$db){
    die("Database connection Failed");
}

Logger::init($db);

$showSuccess = false;

if (isset($_SESSION['success']) && $_SESSION['success'] === 'registered') {
    $showSuccess = true;
    unset($_SESSION['success']); // remove immediately (important)
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $db->prepare("
        SELECT u.user_id, u.username, u.password_hash, u.is_active, u.role_id, r.role_name
        FROM users u
        JOIN roles r ON u.role_id = r.role_id
        WHERE u.username = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1){

        $user = $result->fetch_assoc();

        if ($user['is_active'] != 1){
            header("Location: Admin_Staff_Login.php?error=inactive");
            exit();
        }

        if (!password_verify($password, $user['password_hash'])){
            header("Location: Admin_Staff_Login.php?error=invalid");
            exit();
        }

        // SESSION SETUP (correct place)
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['role'] = $user['role_name'];
        $_SESSION['roles'] = [$user['role_name']];

        Logger::log("LOGIN", "User logged in: " . $username);

        // ROUTING
        if ($user['role_name'] === 'admin') {
            header("Location: /QueueLess/views/admin/dashboard.php");
            exit();
        }

        if ($user['role_name'] === 'staff') {
            header("Location: /QueueLess/views/staff/dashboard.php");
            exit();
        }

        header("Location: Admin_Staff_Login.php?error=norole");
        exit();

    } else {
        header("Location: Admin_Staff_Login.php?error=notfound");
        exit();
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin/Staff Login Page</title>
    <link rel="stylesheet" href="../public/assets/css/cherry.css">
</head>
<body class="no-navbar">
        <div class="main">
            <div class="login-wrapper">
                <div class="login-card">
                    <form action="<?php  echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">

                        <h1>Login</h1>

                        <label for="username">Username:</label>
                        <input type="text" name="username" required>

                        <label for="password">Password:</label>
                        <input type="password" name="password" required>
                        <button type="submit">Login</button>
                    </form>

                    <div class="user_register">
                        Don't have an account?
                        <a href="Admin_Staff_Register.php">Register</a>
                    </div>
                </div>

            </div>
        </div>


        <!-- LOGIN SUCCESS MODAL -->
        <?php if ($showSuccess): ?>
        <div id="loginSuccessModal" class="login-success-modal" style="display:flex;">
            <div class="login-success-content">
                <h2>Success</h2>
                <p>Your account was created. You can now log in.</p>
                <button onclick="closeLoginSuccess()">OK</button>
            </div>
        </div>
        <?php endif; ?>


        <!-- FOOTER -->
        <footer class="footer minimal">

            <p>© 2026 Queueless Enrollment System | All Rights Reserved</p>

        </footer>

        <script>
        function closeLoginSuccess() {
            document.getElementById("loginSuccessModal").style.display = "none";
        }
        </script>

    </body>
</html>
