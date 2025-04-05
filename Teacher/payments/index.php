<?php
require_once '../../config/db_connect.php';

// Get recent payments (last 30 days)
$sql = "SELECT p.*, t.full_name 
        FROM payments p
        JOIN teachers t ON p.teacher_id = t.teacher_id
        WHERE p.payment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        ORDER BY p.payment_date DESC
        LIMIT 50";

$stmt = $conn->query($sql);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4"><i class="fas fa-money-bill-wave"></i> Payments</h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <a href="add.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Payment
                </a>
            </div>
        </div>

        <?php if(count($payments) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Teacher</th>
                            <th>Amount</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($payments as $payment): ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($payment['payment_date'])) ?></td>
                                <td><?= htmlspecialchars($payment['full_name']) ?></td>
                                <td>$<?= number_format($payment['amount'], 2) ?></td>
                                <td><?= htmlspecialchars($payment['notes'] ?: 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No payments recorded in the last 30 days.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>