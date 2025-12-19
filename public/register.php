<?php
session_start();

require_once '../config/database.php';
require_once '../app/models/User.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $message = "All fields are required";
    } else {
        $user = new User($conn);

        if ($user->emailExists($email)) {
            $message = "Email already registered";
        } else {
            if ($user->register($name, $email, $password)) {
                header("Location: login.php");
                exit;
            } else {
                $message = "Registration failed";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header text-center fw-bold">
                    Register
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-danger"><?= $message ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button class="btn btn-primary w-100">Register</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a href="login.php">Already have an account?</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
