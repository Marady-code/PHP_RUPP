<?php
require_once '../../config/db_connect.php';

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$teacher_id = $_GET['id'];

// Fetch teacher data
$stmt = $conn->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
$stmt->execute([$teacher_id]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$teacher) {
    $_SESSION['error'] = "Teacher not found";
    header("Location: index.php");
    exit();
}

// Fetch teaching hours for this teacher (last 30 days)
$hoursStmt = $conn->prepare("
    SELECT date_taught, hours_taught, notes 
    FROM teaching_hours 
    WHERE teacher_id = ? 
    AND date_taught >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ORDER BY date_taught DESC
");
$hoursStmt->execute([$teacher_id]);
$teachingHours = $hoursStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total hours for last 30 days
$totalHoursStmt = $conn->prepare("
    SELECT SUM(hours_taught) AS total_hours 
    FROM teaching_hours 
    WHERE teacher_id = ? 
    AND date_taught >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
");
$totalHoursStmt->execute([$teacher_id]);
$totalHours = $totalHoursStmt->fetch(PDO::FETCH_ASSOC)['total_hours'] ?? 0;

// Calculate total earnings for last 30 days
$totalEarnings = $totalHours * $teacher['hourly_rate'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Teacher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4"><i class="fas fa-user"></i> Teacher Details</h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h3><?= htmlspecialchars($teacher['full_name']) ?></h3>
                        <p class="text-muted mb-1"><i class="fas fa-envelope"></i> <?= htmlspecialchars($teacher['email'] ?: 'N/A') ?></p>
                        <p class="text-muted mb-1"><i class="fas fa-phone"></i> <?= htmlspecialchars($teacher['phone'] ?: 'N/A') ?></p>
                        <p class="text-muted"><i class="fas fa-money-bill-wave"></i> $<?= number_format($teacher['hourly_rate'], 2) ?> per hour</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="edit.php?id=<?= $teacher['teacher_id'] ?>" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
                        <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Last 30 Days Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Total Hours</h6>
                                        <h3 class="card-text"><?= number_format($totalHours, 2) ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Total Earnings</h6>
                                        <h3 class="card-text">$<?= number_format($totalEarnings, 2) ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="../hours/add.php?teacher_id=<?= $teacher['teacher_id'] ?>" class="btn btn-success w-100">
                            <i class="fas fa-plus"></i> Add Teaching Hours
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Teaching Hours</h5>
                    </div>
                    <div class="card-body">
                        <?php if(count($teachingHours) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Hours</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($teachingHours as $hour): ?>
                                            <tr>
                                                <td><?= date('M d, Y', strtotime($hour['date_taught'])) ?></td>
                                                <td><?= number_format($hour['hours_taught'], 2) ?></td>
                                                <td><?= htmlspecialchars($hour['notes'] ?: '') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <a href="../hours/teacher_details.php?teacher_id=<?= $teacher['teacher_id'] ?>" class="btn btn-primary w-100">
                                <i class="fas fa-list"></i> View All Hours
                            </a>
                        <?php else: ?>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle"></i> No teaching hours recorded in the last 30 days.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>