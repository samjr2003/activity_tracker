<?php
session_start();
require_once '../config/database.php';

if (isset($_SESSION['user_id'])) {
    $sql = "UPDATE login_logs 
            SET logout_time = NOW() 
            WHERE user_id = :user_id 
            ORDER BY id DESC LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':user_id' => $_SESSION['user_id']
    ]);
}

session_unset();
session_destroy();

header("Location: login.php");
exit;
