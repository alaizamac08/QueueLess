<?php
include(__DIR__ . "/../config/database.php");
include(__DIR__ . "/../core/Logger.php");
include(__DIR__ . "/../models/ActivityLog.php");
include(__DIR__ . "/../models/User.php");

$db = (new Database())->connect();

if (!$db){
    die("Database connection failed: " . mysqli_connect_error());
}

Logger::init($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = $_POST['password'];

    $check = $db->prepare("SELECT user_id FROM users WHERE username = ?");
    
    $check->bind_param("s", $username);
    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows > 0){
        header("Location: Admin_Staff_Register.php?error=taken");
        exit();
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $is_active = 1;

        $stmt = $db->prepare("INSERT INTO users (username, password_hash, is_active) 
                VALUES (?, ?, ?)");

        
        $stmt->bind_param("ssi", $username, $hash, $is_active);

        if ($stmt->execute()){
            Logger::log(
                "REGISTER",
                "New user registered: " . $username);
            header("Location: Admin_Staff_Register.php?success=1");
            exit();
        } else{
            header("Location: Admin_Staff_Register.php?error=insert");
            exit();
        }
    }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin/Staff Register Page</title>
</head>
<body><center>


    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
        <h2>Register</h2>

        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Register">
    </form>

    <br>Already have an account? 
    <a href="Admin_Staff_LogIn.php">Login</a>
    </center>
</body>
</html>
