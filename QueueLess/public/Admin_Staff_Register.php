<?php
include(__DIR__ . "/../app/core/database.php");
include(__DIR__ . "/../app/core/Logger.php");
include(__DIR__ . "/../app/models/User.php");

$db = (new Database())->connect();

if (!$db){
    die("Database connection failed: " . mysqli_connect_error());
}

Logger::init($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = $_POST['password'];

    // CHECK DUPLICATE USERNAME
    $check = $db->prepare("SELECT user_id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        header("Location: Admin_Staff_Register.php?error=taken");
        exit();
    }

    // GET STAFF ROLE
    $roleQuery = $db->prepare("SELECT role_id FROM roles WHERE role_name = 'staff' LIMIT 1");
    $roleQuery->execute();
    $roleResult = $roleQuery->get_result();
    $roleRow = $roleResult->fetch_assoc();

    if (!$roleRow) {
        die("Role 'staff' not found in database");
    }

    $role_id = $roleRow['role_id'];

    // INSERT USER
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $is_active = 1;

    $stmt = $db->prepare("
        INSERT INTO users (username, password_hash, is_active, role_id)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("ssii", $username, $hash, $is_active, $role_id);

    if ($stmt->execute()){
        Logger::log("REGISTER", "New staff registered: " . $username);
        header("Location: Admin_Staff_Register.php?success=1");
        exit();
    } else {
        header("Location: Admin_Staff_Register.php?error=insert");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin/Staff Register Page</title>
    <link rel="stylesheet" href="../public/assets/css/cherry.css">
</head>
<body class="no-navbar">

    <div class="main">
        <div class="register-wrapper">
            <div class="register-card">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
                    <h1>Register</h1>

                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>

                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                        <button type="submit">Register</button>
                </form>


                <div class="user_login">
                Already have an account?
                <a href="Admin_Staff_LogIn.php">Login</a>
                </div>
            </div>
        </div>
    </div>

<!-- FOOTER -->
                <footer class="footer minimal">
                    <p>© 2026 Queueless Enrollment System | All Rights Reserved</p>
                </footer>
</body>
</html>
