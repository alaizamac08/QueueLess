<?php
class AuthController{
    private $user;
    private $db;

    public function __construct($db){
        $this->user = new User($db);
        $this->db = $db;
    }

    public function login($username, $password){
        $user = $this->user->findByUsername($username);

        if (!$user) return false;

        if($user && password_verify($password, $user['password'])){

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            Logger::log("LOGIN", "User logged in: " . $username);

            // load user roles
            $authHelper = new AuthHelper($this->db);

            $user = $this->user->findByUsername($username);

            $_SESSION['roles'] = $authHelper->gerUserRole($user['user_id']);

            return true;
        }
    }

    public function register($username, $password){
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $is_active = 1;
        $created_at = date("Y-m-d H:i:s");

        return $this->user->createUser($username, $hashedPassword, $is_active, $created_at);
    }
}
?>