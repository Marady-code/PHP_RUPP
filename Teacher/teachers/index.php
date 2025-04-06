<?php
require_once '../config/db_connect.php';

// Fetch all active teachers
$stmt = $conn->prepare("
    SELECT 
        t.*,
        COALESCE(SUM(th.hours_taught), 0) as total_hours,
        COALESCE(SUM(th.hours_taught * t.hourly_rate), 0) as total_earnings
    FROM teachers t
    LEFT JOIN teaching_hours th ON t.id = th.teacher_id 
        AND MONTH(th.date) = MONTH(CURRENT_DATE)
        AND YEAR(th.date) = YEAR(CURRENT_DATE)
    WHERE t.deleted_at IS NULL
    GROUP BY t.id
    ORDER BY t.name
");
$stmt->execute();
$teachers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachers Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-users me-2"></i>Teachers</h2>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Teacher
            </a>
        </div>

        <?php flashMessage(); ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone Number</th>
                                <th>Start Date</th>
                                <th>Hourly Rate</th>
                                <th>This Month Hours</th>
                                <th>This Month Earnings</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teachers as $teacher): ?>
                            <tr>
                                <td><?= htmlspecialchars($teacher['name']) ?></td>
                                <td><?= htmlspecialchars($teacher['phone_number']) ?></td>
                                <td><?= date('Y-m-d', strtotime($teacher['start_date'])) ?></td>
                                <td>$<?= number_format($teacher['hourly_rate'], 2) ?></td>
                                <td><?= number_format($teacher['total_hours'], 2) ?> hrs</td>
                                <td>$<?= number_format($teacher['total_earnings'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= $teacher['status'] === 'active' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($teacher['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="view.php?id=<?= $teacher['id'] ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit.php?id=<?= $teacher['id'] ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="schedule.php?id=<?= $teacher['id'] ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-calendar"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="deleteTeacher(<?= $teacher['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
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
    <script>
    function deleteTeacher(id) {
        if (confirm('Are you sure you want to delete this teacher?')) {
            window.location.href = `delete.php?id=${id}`;
        }
    }
    </script>
</body>
</html>