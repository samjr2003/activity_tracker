<?php
// app/models/User.php

require_once __DIR__ . '/../../config/database.php';

class User
{
    private $conn;
    private $table = "users";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Register new user
    public function register($name, $email, $password)
    {
        $sql = "INSERT INTO " . $this->table . " 
                (name, email, password) 
                VALUES (:name, :email, :password)";

        $stmt = $this->conn->prepare($sql);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        return $stmt->execute([
            ':name'     => htmlspecialchars(strip_tags($name)),
            ':email'    => htmlspecialchars(strip_tags($email)),
            ':password' => $hashedPassword
        ]);
    }

    // Check if email already exists
    public function emailExists($email)
    {
        $sql = "SELECT id FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);

        return $stmt->rowCount() > 0;
    }
}
