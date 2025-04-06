<?php
require_once('../config/db_connect.php');

try {
    $sql = "SELECT th.*, t.full_name, t.hourly_rate 
            FROM teaching_hours th
            JOIN teachers t ON th.teacher_id = t.teacher_id
            ORDER BY th.date_taught DESC
            LIMIT 50";

    $stmt = $conn->query($sql);
    $hours = $stmt->fetchAll();
} catch(PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teaching Hours</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4"><i class="fas fa-clock"></i> Teaching Hours</h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <?php flashMessage(); ?>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="report.php" class="btn btn-info me-2">
                            <i class="fas fa-file-alt"></i> Monthly Report
                        </a>
                    </div>
                    <a href="add.php" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Hours
                    </a>
                </div>
            </div>
        </div>

        <?php if(!empty($hours)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Teacher</th>
                            <th>Hours</th>
                            <th>Earnings</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($hours as $hour): ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($hour['date_taught'])) ?></td>
                                <td><?= htmlspecialchars($hour['full_name']) ?></td>
                                <td><?= number_format($hour['hours_taught'], 2) ?></td>
                                <td>$<?= number_format($hour['hours_taught'] * $hour['hourly_rate'], 2) ?></td>
                                <td><?= htmlspecialchars($hour['notes'] ?: 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No teaching hours recorded yet.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>