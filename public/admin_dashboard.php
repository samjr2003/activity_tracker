<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

require_once '../config/database.php';

/* TOTAL USERS */
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();

/* TOTAL ACTIVITIES */
$totalActivities = $conn->query("SELECT COUNT(*) FROM user_activity")->fetchColumn();

/* PAGE VISITS */
$pageVisits = $conn->query(
    "SELECT COUNT(*) FROM user_activity WHERE activity_type = 'Page Visit'"
)->fetchColumn();

/* BUTTON CLICKS */
$buttonClicks = $conn->query(
    "SELECT COUNT(*) FROM user_activity WHERE activity_type = 'BUTTON_CLICK'"
)->fetchColumn();

/* TOTAL LOGINS */
$logins = $conn->query("SELECT COUNT(*) FROM login_logs")->fetchColumn();

/* RECENT ACTIVITIES */
$recentStmt = $conn->query(
    "SELECT u.name, ua.activity_type, ua.page_name, ua.created_at
     FROM user_activity ua
     JOIN users u ON ua.user_id = u.id
     ORDER BY ua.created_at DESC
     LIMIT 10"
);
$recentActivities = $recentStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">Admin Dashboard</span>
    <div class="text-white">
        <a href="dashboard.php" class="text-info text-decoration-none">User View</a> |
        <a href="logout.php" class="text-warning text-decoration-none">Logout</a>
    </div>
</nav>

<div class="container mt-4">

    <!-- SUMMARY CARDS -->
    <div class="row g-4">

        <div class="col-md-3">
            <div class="card text-bg-primary shadow">
                <div class="card-body">
                    <h6>Total Users</h6>
                    <h3><?= $totalUsers ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-success shadow">
                <div class="card-body">
                    <h6>Total Activities</h6>
                    <h3><?= $totalActivities ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-warning shadow">
                <div class="card-body">
                    <h6>Page Visits</h6>
                    <h3><?= $pageVisits ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-danger shadow">
                <div class="card-body">
                    <h6>Button Clicks</h6>
                    <h3><?= $buttonClicks ?></h3>
                </div>
            </div>
        </div>

    </div>

    <!-- CHART -->
    <div class="card mt-4 shadow">
        <div class="card-header fw-bold">
            Overall User Activity Analytics
        </div>
        <div class="card-body">
            <canvas id="adminChart" height="100"></canvas>
        </div>
    </div>

    <!-- RECENT ACTIVITY -->
    <div class="card mt-4 shadow">
        <div class="card-header fw-bold">
            Recent User Activities
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>User</th>
                        <th>Activity</th>
                        <th>Page / Action</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentActivities)): ?>
                        <?php foreach ($recentActivities as $a): ?>
                            <tr>
                                <td><?= htmlspecialchars($a['name']) ?></td>
                                <td><?= htmlspecialchars($a['activity_type']) ?></td>
                                <td><?= htmlspecialchars($a['page_name']) ?></td>
                                <td><?= $a['created_at'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                No activity data available
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- PASS DATA TO JS -->
<script>
    const adminChartData = {
        totalUsers: <?= (int)$totalUsers ?>,
        totalActivities: <?= (int)$totalActivities ?>,
        pageVisits: <?= (int)$pageVisits ?>,
        buttonClicks: <?= (int)$buttonClicks ?>,
        logins: <?= (int)$logins ?>
    };
</script>

<!-- CHART SCRIPT -->
<script>
    const ctx = document.getElementById('adminChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                'Users',
                'Total Activities',
                'Page Visits',
                'Button Clicks',
                'Logins'
            ],
            datasets: [{
                label: 'System Usage Statistics',
                data: [
                    adminChartData.totalUsers,
                    adminChartData.totalActivities,
                    adminChartData.pageVisits,
                    adminChartData.buttonClicks,
                    adminChartData.logins
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
</script>

</body>
</html>
