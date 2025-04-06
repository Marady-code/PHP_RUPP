<?php
require_once('../config/db_connect.php');

$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

try {
    $sql = "SELECT t.teacher_id, t.full_name, t.hourly_rate, 
                   SUM(th.hours_taught) AS total_hours,
                   SUM(th.hours_taught * t.hourly_rate) AS total_earnings
            FROM teachers t
            LEFT JOIN teaching_hours th ON t.teacher_id = th.teacher_id 
                AND MONTH(th.date_taught) = :month 
                AND YEAR(th.date_taught) = :year
            WHERE t.isActive = 1
            GROUP BY t.teacher_id
            ORDER BY t.full_name";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':month', $month, PDO::PARAM_INT);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->execute();
    $report = $stmt->fetchAll();

    $total_hours = array_sum(array_column($report, 'total_hours'));
    $total_earnings = array_sum(array_column($report, 'total_earnings'));

} catch(PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
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
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4"><i class="fas fa-file-alt"></i> Monthly Teaching Report</h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <?php flashMessage(); ?>
                <form method="get" class="row g-3">
                    <div class="col-md-4">
                        <label for="month" class="form-label">Month</label>
                        <select class="form-select" id="month" name="month">
                            <?php for($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= $m == $month ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="year" class="form-label">Year</label>
                        <select class="form-select" id="year" name="year">
                            <?php for($y = date('Y') - 5; $y <= date('Y') + 1; $y++): ?>
                                <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Summary for <?= date('F Y', mktime(0, 0, 0, $month, 1, $year)) ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-title">Total Hours</h6>
                                <h3 class="card-text"><?= number_format($total_hours, 2) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-title">Total Earnings</h6>
                                <h3 class="card-text">$<?= number_format($total_earnings, 2) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Hourly Rate</th>
                                <th>Total Hours</th>
                                <th>Total Earnings</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($report as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                                    <td>$<?= number_format($row['hourly_rate'], 2) ?></td>
                                    <td><?= number_format($row['total_hours'] ?: 0, 2) ?></td>
                                    <td>$<?= number_format($row['total_earnings'] ?: 0, 2) ?></td>
                                    <td>
                                        <a href="teacher_details.php?teacher_id=<?= $row['teacher_id'] ?>&month=<?= $month ?>&year=<?= $year ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-list"></i> View Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>