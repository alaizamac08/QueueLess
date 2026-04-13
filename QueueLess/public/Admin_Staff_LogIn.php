<?php 
session_start();

include(__DIR__ . "/../config/database.php");
include(__DIR__ . "/../core/Logger.php");
include(__DIR__ . "/../models/ActivityLog.php");

$db = (new Database())->connect();

if (!$db){
    die("Database connection Failed: ". mysqli_connect_error());
}

Logger::init($db);

if ($_SERVER["REQUEST_METHOD"] == "POST"){

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $db->prepare("SELECT user_id, username, password_hash, is_active FROM users WHERE username = ?");

$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1){
    $user = $result->fetch_assoc();

    if ($user['is_active'] == 1 && password_verify($password, $user['password_hash'])){

        $_SESSION['user_id'] = $user['user_id'];

        Logger::log(
            "LOGIN",
            "User logged in: " . $username
        );

        header("Location: Admin_Dashboard.php");
        exit();
    } else {
        header("Location: Admin_Staff_Login.php?error=invalid");
        exit();
    }
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
</head>
<body>
    <center>
        <form action="<?php  echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
        
        <h2>Login</h2>
        <label for="username">Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="Login">
        </form>


        <br>Don't have an account?
        <a href="Admin_Staff_Register.php">Register</a>

    </center>
</body>
</html>




<?php 


?>