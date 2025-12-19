<?php
// app/controllers/ActivityController.php

session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    exit;
}

$user_id       = $_SESSION['user_id'];
$activity_type = $data['activity_type'] ?? '';
$page_name     = $data['page_name'] ?? '';

$sql = "INSERT INTO user_activity 
        (user_id, activity_type, page_name, ip_address, user_agent)
        VALUES 
        (:user_id, :activity_type, :page_name, :ip, :agent)";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':user_id'       => $user_id,
    ':activity_type' => $activity_type,
    ':page_name'     => $page_name,
    ':ip'            => $_SERVER['REMOTE_ADDR'],
    ':agent'         => $_SERVER['HTTP_USER_AGENT']
]);

echo json_encode(['status' => 'success']);
