<?php
session_start();

include(__DIR__ . "/../../app/core/database.php");
include(__DIR__ . "/../../app/core/Logger.php");

$db = (new Database())->connect();

if (!$db){
    die("Database connection Failed");
}

Logger::init($db);

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
            header("Location: login.php?error=inactive");
            exit();
        }

        if (!password_verify($password, $user['password_hash'])){
            header("Location: login.php?error=invalid");
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
        if ($user['role_name'] === 'student') {
            header("Location: /QueueLess/views/student/dashboard.php");
            exit();
        }

        header("Location: login.php?error=norole");
        exit();

    } else {
        header("Location: login.php?error=notfound");
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
    <link rel="stylesheet" href="../../public/assets/css/cherry.css">
</head>
<body class="no-navbar">
        <div class="main">
            <div class="login-wrapper">
                <button class="back-btn" onclick="history.back()">← Back</button>
                <div class="login-card">
                    <form action="<?php  echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">

                        <h1>Login</h1>

                        <label for="username">Username:</label>
                        <input type="text" name="username" required>

                        <label for="password">Password:</label>
                        <input type="password" name="password" required>
                        <button type="submit">Login</button>
                    </form>
                </div>

            </div>
        </div>

        <!-- FOOTER -->
        <footer class="footer minimal">

            <p>© 2026 Queueless Enrollment System | All Rights Reserved</p>

        </footer>



        <div id="errorModal" class="modal">
            <div class="modal-content error-modal">
                <h2 id="errorTitle">Login Error</h2>
                <p id="errorMessage"></p>

                <div class="modal-actions">
                    <button onclick="closeModal()" class="login-btn-ok">OK</button>
                </div>
            </div>
        </div>

        <script>
        function getErrorFromURL() {
            const params = new URLSearchParams(window.location.search);
            return params.get("error");
        }

        function showModal(message, title = "Login Error") {
            document.getElementById("errorTitle").innerText = title;
            document.getElementById("errorMessage").innerText = message;
            document.getElementById("errorModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("errorModal").style.display = "none";

            // clean URL (removes ?error=...)
            window.history.replaceState({}, document.title, "login.php");
        }

        window.onload = function () {
            const error = getErrorFromURL();

            if (error === "notfound") {
                showModal("User not found. Please check your username.");
            }

            if (error === "norole") {
                showModal("Your account has no assigned role. Contact the administrator.");
            }

            if (error === "inactive") {
                showModal("Your account is inactive.");
            }

            if (error === "invalid") {
                showModal("Incorrect password.");
            }
        };
        </script>
</body>
</html>
