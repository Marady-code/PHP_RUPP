<?php
require_once '../config/db_connect.php';

// Get current month and year, or from query parameters
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Fetch monthly summary for all teachers
$stmt = $conn->prepare("
    SELECT 
        t.id,
        t.name,
        t.hourly_rate,
        COALESCE(SUM(th.hours_taught), 0) as total_hours,
        COALESCE(SUM(th.hours_taught * t.hourly_rate), 0) as total_earnings
    FROM teachers t
    LEFT JOIN teaching_hours th ON t.id = th.teacher_id 
        AND MONTH(th.date) = :month 
        AND YEAR(th.date) = :year
    WHERE t.deleted_at IS NULL
    GROUP BY t.id, t.name, t.hourly_rate
    ORDER BY t.name
");

$stmt->execute([
    'month' => $month,
    'year' => $year
]);

$teachers = $stmt->fetchAll();

// Calculate totals
$total_hours = 0;
$total_earnings = 0;
foreach ($teachers as $teacher) {
    $total_hours += $teacher['total_hours'];
    $total_earnings += $teacher['total_earnings'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Teaching Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-chart-bar me-2"></i>Monthly Teaching Report
            </h2>
            <div class="d-flex gap-2">
                <form action="" method="GET" class="d-flex gap-2">
                    <select name="month" class="form-select" onchange="this.form.submit()">
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i ?>" <?= $i === $month ? 'selected' : '' ?>>
                                <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <select name="year" class="form-select" onchange="this.form.submit()">
                        <?php for ($i = date('Y'); $i >= date('Y')-5; $i--): ?>
                            <option value="<?= $i ?>" <?= $i === $year ? 'selected' : '' ?>>
                                <?= $i ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </form>
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Print Report
                </button>
            </div>
        </div>

        <?php flashMessage(); ?>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0">
                    Teaching Hours and Earnings - <?= date('F Y', mktime(0, 0, 0, $month, 1, $year)) ?>
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Teacher Name</th>
                                <th>Hourly Rate</th>
                                <th>Total Hours</th>
                                <th>Total Earnings</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teachers as $teacher): ?>
                            <tr>
                                <td><?= htmlspecialchars($teacher['name']) ?></td>
                                <td>$<?= number_format($teacher['hourly_rate'], 2) ?></td>
                                <td><?= number_format($teacher['total_hours'], 1) ?> hrs</td>
                                <td>$<?= number_format($teacher['total_earnings'], 2) ?></td>
                                <td>
                                    <a href="../teachers/view.php?id=<?= $teacher['id'] ?>" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Details
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-primary">
                            <tr>
                                <th colspan="2">Monthly Totals:</th>
                                <th><?= number_format($total_hours, 1) ?> hrs</th>
                                <th>$<?= number_format($total_earnings, 2) ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h4 class="card-title mb-0">Monthly Statistics</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Total Teachers
                                <span class="badge bg-primary rounded-pill">
                                    <?= count($teachers) ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Average Hours per Teacher
                                <span class="badge bg-info rounded-pill">
                                    <?= count($teachers) ? number_format($total_hours / count($teachers), 1) : 0 ?> hrs
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Average Earnings per Teacher
                                <span class="badge bg-success rounded-pill">
                                    $<?= count($teachers) ? number_format($total_earnings / count($teachers), 2) : 0 ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 