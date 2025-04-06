<?php
require_once('../config/db_connect.php');

if(!isset($_GET['teacher_id']) || !isset($_GET['month']) || !isset($_GET['year'])) {
    header("Location: report.php");
    exit();
}

$teacher_id = $_GET['teacher_id'];
$month = intval($_GET['month']);
$year = intval($_GET['year']);

try {
    // Get teacher info
    $teacherStmt = $conn->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
    $teacherStmt->execute([$teacher_id]);
    $teacher = $teacherStmt->fetch();
    
    if(!$teacher) {
        $_SESSION['error'] = "Teacher not found";
        header("Location: report.php");
        exit();
    }

    // Get teaching hours
    $hoursStmt = $conn->prepare("
        SELECT date_taught, hours_taught, notes 
        FROM teaching_hours 
        WHERE teacher_id = ? 
        AND MONTH(date_taught) = ? 
        AND YEAR(date_taught) = ?
        ORDER BY date_taught DESC
    ");
    $hoursStmt->execute([$teacher_id, $month, $year]);
    $teachingHours = $hoursStmt->fetchAll();

    // Calculate totals
    $totalHoursStmt = $conn->prepare("
        SELECT SUM(hours_taught) AS total_hours 
        FROM teaching_hours 
        WHERE teacher_id = ? 
        AND MONTH(date_taught) = ? 
        AND YEAR(date_taught) = ?
    ");
    $totalHoursStmt->execute([$teacher_id, $month, $year]);
    $totalHours = $totalHoursStmt->fetch()['total_hours'] ?? 0;
    $totalEarnings = $totalHours * $teacher['hourly_rate'];

} catch(PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: report.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Hours Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4"><i class="fas fa-list"></i> Teaching Hours Details</h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <?php flashMessage(); ?>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3><?= htmlspecialchars($teacher['full_name']) ?></h3>
                        <p class="mb-0"><?= date('F Y', mktime(0, 0, 0, $month, 1, $year)) ?></p>
                    </div>
                    <div class="text-end">
                        <a href="report.php?month=<?= $month ?>&year=<?= $year ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="card-title">Total Hours</h6>
                        <h3 class="card-text"><?= number_format($totalHours, 2) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="card-title">Total Earnings</h6>
                        <h3 class="card-text">$<?= number_format($totalEarnings, 2) ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Teaching Sessions</h5>
            </div>
            <div class="card-body">
                <?php if(!empty($teachingHours)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Hours</th>
                                    <th>Earnings</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($teachingHours as $hour): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($hour['date_taught'])) ?></td>
                                        <td><?= number_format($hour['hours_taught'], 2) ?></td>
                                        <td>$<?= number_format($hour['hours_taught'] * $teacher['hourly_rate'], 2) ?></td>
                                        <td><?= htmlspecialchars($hour['notes'] ?: 'N/A') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> No teaching hours recorded for this period.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>