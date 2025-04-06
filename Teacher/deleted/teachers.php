<?php
require_once('../config/db_connect.php');

try {
    $stmt = $conn->query("SELECT * FROM teachers WHERE isActive = 0 ORDER BY full_name");
    $teachers = $stmt->fetchAll();
} catch(PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleted Teachers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4"><i class="fas fa-trash"></i> Deleted Teachers</h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <?php flashMessage(); ?>
                <a href="../teachers/index.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Back to Active Teachers</a>
            </div>
        </div>

        <?php if(!empty($teachers)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Hourly Rate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($teachers as $teacher): ?>
                            <tr>
                                <td><?= htmlspecialchars($teacher['teacher_id']) ?></td>
                                <td><?= htmlspecialchars($teacher['full_name']) ?></td>
                                <td>
                                    <?= htmlspecialchars($teacher['email']) ?><br>
                                    <?= htmlspecialchars($teacher['phone']) ?>
                                </td>
                                <td>$<?= number_format($teacher['hourly_rate'], 2) ?></td>
                                <td>
                                    <a href="../teachers/restore.php?id=<?= $teacher['teacher_id'] ?>" class="btn btn-sm btn-success" title="Restore" onclick="return confirm('Restore this teacher?')">
                                        <i class="fas fa-undo"></i> Restore
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No deleted teachers found.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>