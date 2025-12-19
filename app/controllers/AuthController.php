<?php
// app/controllers/AuthController.php

class AuthController
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function login($email, $password)
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);

        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch();

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name']    = $user['name'];
                $_SESSION['role']    = $user['role'];

                $this->logLogin($user['id']);
                return true;
            }
        }
        return false;
    }

    private function logLogin($userId)
    {
        $sql = "INSERT INTO login_logs (user_id, login_time, ip_address)
                VALUES (:user_id, NOW(), :ip)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':ip'      => $_SERVER['REMOTE_ADDR']
        ]);
    }
}
