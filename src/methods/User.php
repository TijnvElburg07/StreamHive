<?php

include_once '../core/db.php';


// {# -- Create User class to handle user-related operations #}

class User{

    private $pdo;

    // {# -- User constructor to initialize user properties #}

    public function __construct($pdo){
        $this->pdo = $pdo;
    }



    // {# -- Method to register a new user #}
    public function register($username, $email, $password){
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->fetch()) {
            return false; // Username or email already exists
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        return $stmt->execute(['username' => $username, 'email' => $email, 'password' => $hashedPassword]);
    }


    // {# -- Method to log in a user #}
    public function login($usernameOrEmail, $password){
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $usernameOrEmail, 'email' => $usernameOrEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            return true;
        }
        return false;
    }


    // {# -- Method to log out a user #}
    public function logout(){
        session_start();
        session_destroy();
    }

    // {# -- Method to check if a user is logged in #}
    public function isLoggedIn(){
        session_start();
        return isset($_SESSION['user_id']);
    }

    // {# -- Method to get user information from the database #}
    public function getUserInfo($userId){
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // {# -- Method to update user information in the database #}
    public function updateUserInfo($userId, $username, $email){
        $stmt = $this->pdo->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
        return $stmt->execute(['username' => $username, 'email' => $email, 'id' => $userId]);
    }
}