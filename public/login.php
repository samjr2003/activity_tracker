<?php
session_start();

require_once '../config/database.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/models/User.php';

$error = "";
$success = "";

/* If already logged in */
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

/* LOGIN */
if (isset($_POST['login'])) {
    $email    = trim($_POST['login_email']);
    $password = $_POST['login_password'];

    if (empty($email) || empty($password)) {
        $error = "All login fields are required";
    } else {
        $auth = new AuthController($conn);
        if ($auth->login($email, $password)) {
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password";
        }
    }
}

/* REGISTER */
if (isset($_POST['register'])) {
    $name     = trim($_POST['reg_name']);
    $email    = trim($_POST['reg_email']);
    $password = $_POST['reg_password'];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All registration fields are required";
    } else {
        $user = new User($conn);

        if ($user->emailExists($email)) {
            $error = "Email already registered";
        } else {
            if ($user->register($name, $email, $password)) {
                $success = "Registration successful. Please login.";
            } else {
                $error = "Registration failed";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login / Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card shadow">
                <div class="card-header text-center fw-bold">
                    User Access
                </div>

                <div class="card-body">

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <!-- Tabs -->
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#login">
                                Login
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#register">
                                Register
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">

                        <!-- LOGIN FORM -->
                        <div class="tab-pane fade show active" id="login">
                            <form method="POST">
                                <div class="mb-3">
                                    <label>Email</label>
                                    <input type="email" name="login_email" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label>Password</label>
                                    <input type="password" name="login_password" class="form-control" required>
                                </div>

                                <button type="submit" name="login" class="btn btn-primary w-100">
                                    Login
                                </button>
                            </form>
                        </div>

                        <!-- REGISTER FORM -->
                        <div class="tab-pane fade" id="register">
                            <form method="POST">
                                <div class="mb-3">
                                    <label>Name</label>
                                    <input type="text" name="reg_name" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label>Email</label>
                                    <input type="email" name="reg_email" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label>Password</label>
                                    <input type="password" name="reg_password" class="form-control" required>
                                </div>

                                <button type="submit" name="register" class="btn btn-success w-100">
                                    Register
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
