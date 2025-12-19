<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/database.php';

$userId = $_SESSION['user_id'];
$name   = $_SESSION['name'];
$role   = $_SESSION['role'];

/* Total activities */
$stmt = $conn->prepare("SELECT COUNT(*) FROM user_activity WHERE user_id = :uid");
$stmt->execute([':uid' => $userId]);
$totalActivities = (int)$stmt->fetchColumn();

/* Page visits */
$stmt = $conn->prepare(
    "SELECT COUNT(*) FROM user_activity
     WHERE user_id = :uid AND activity_type = 'Page Visit'"
);
$stmt->execute([':uid' => $userId]);
$pageVisits = (int)$stmt->fetchColumn();

/* Button clicks (FIXED) */
$stmt = $conn->prepare(
    "SELECT COUNT(*) FROM user_activity
     WHERE user_id = :uid AND activity_type = 'BUTTON_CLICK'"
);
$stmt->execute([':uid' => $userId]);
$buttonClicks = (int)$stmt->fetchColumn();

/* Login count */
$stmt = $conn->prepare(
    "SELECT COUNT(*) FROM login_logs WHERE user_id = :uid"
);
$stmt->execute([':uid' => $userId]);
$loginCount = (int)$stmt->fetchColumn();

/* Recent activities */
$stmt = $conn->prepare(
    "SELECT activity_type, page_name, created_at
     FROM user_activity
     WHERE user_id = :uid
     ORDER BY created_at DESC
     LIMIT 10"
);
$stmt->execute([':uid' => $userId]);
$recentActivities = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">User Activity Tracker</span>
    <div class="text-white">
        Welcome, <?= htmlspecialchars($name) ?>
        <?php if ($role === 'admin'): ?>
            | <a href="admin_dashboard.php" class="text-info text-decoration-none">Admin Panel</a>
        <?php endif; ?>
        | <a href="logout.php" class="text-warning text-decoration-none">Logout</a>
    </div>
</nav>

<div class="container mt-4">

    <!-- SUMMARY CARDS -->
    <div class="row g-4">

        <div class="col-md-3">
            <div class="card text-bg-primary shadow">
                <div class="card-body">
                    <h6>Total Activities</h6>
                    <h3><?= $totalActivities ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-success shadow">
                <div class="card-body">
                    <h6>Page Visits</h6>
                    <h3><?= $pageVisits ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-warning shadow">
                <div class="card-body">
                    <h6>Button Clicks</h6>
                    <h3><?= $buttonClicks ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-danger shadow">
                <div class="card-body">
                    <h6>Logins</h6>
                    <h3><?= $loginCount ?></h3>
                </div>
            </div>
        </div>

    </div>

    <!-- CHART -->
    <div class="card mt-4 shadow">
        <div class="card-header fw-bold">
            Activity Overview
        </div>
        <div class="card-body">
            <canvas id="activityChart" height="100"></canvas>
        </div>
    </div>

    <!-- RECENT ACTIVITY -->
    <div class="card mt-4 shadow">
        <div class="card-header fw-bold">
            Recent Activity
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Activity Type</th>
                        <th>Page / Action</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentActivities)): ?>
                        <?php foreach ($recentActivities as $activity): ?>
                            <tr>
                                <td><?= htmlspecialchars($activity['activity_type']) ?></td>
                                <td><?= htmlspecialchars($activity['page_name']) ?></td>
                                <td><?= $activity['created_at'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">
                                No activity recorded yet
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
    const chartData = {
        totalActivities: <?= $totalActivities ?>,
        pageVisits: <?= $pageVisits ?>,
        buttonClicks: <?= $buttonClicks ?>,
        logins: <?= $loginCount ?>
    };
</script>

<!-- CHART SCRIPT -->
<script>
    const ctx = document.getElementById('activityChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                'Total Activities',
                'Page Visits',
                'Button Clicks',
                'Logins'
            ],
            datasets: [{
                label: 'User Activity Count',
                data: [
                    chartData.totalActivities,
                    chartData.pageVisits,
                    chartData.buttonClicks,
                    chartData.logins
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

<!-- ACTIVITY TRACKER -->
<script src="../assets/js/tracker.js"></script>

</body>
</html>
