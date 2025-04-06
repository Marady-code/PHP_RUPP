<?php
require_once '../config/db_connect.php';

$teacher_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch teacher details
$stmt = $conn->prepare("SELECT * FROM teachers WHERE id = :id AND deleted_at IS NULL");
$stmt->execute(['id' => $teacher_id]);
$teacher = $stmt->fetch();

if (!$teacher) {
    $_SESSION['error'] = "Teacher not found.";
    header('Location: index.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Start transaction
        $conn->beginTransaction();

        // Update teacher status
        $stmt = $conn->prepare("
            UPDATE teachers 
            SET status = 'inactive', 
                end_date = :end_date 
            WHERE id = :teacher_id
        ");
        $stmt->execute([
            'end_date' => $_POST['end_date'],
            'teacher_id' => $teacher_id
        ]);

        // Add replacement record
        $stmt = $conn->prepare("
            INSERT INTO teacher_replacements 
            (original_teacher_id, replacement_teacher_id, start_date, end_date, reason)
            VALUES (:original_id, :replacement_id, :start_date, :end_date, :reason)
        ");
        $stmt->execute([
            'original_id' => $teacher_id,
            'replacement_id' => $_POST['replacement_teacher_id'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'reason' => $_POST['reason']
        ]);

        $conn->commit();
        $_SESSION['message'] = "Teacher replacement set successfully!";
        header('Location: index.php');
        exit();
    } catch(PDOException $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

// Fetch available teachers for replacement
$stmt = $conn->prepare("
    SELECT id, name 
    FROM teachers 
    WHERE id != :teacher_id 
    AND status = 'active' 
    AND deleted_at IS NULL 
    ORDER BY name
");
$stmt->execute(['teacher_id' => $teacher_id]);
$available_teachers = $stmt->fetchAll();

// Fetch current and past replacements
$stmt = $conn->prepare("
    SELECT r.*, 
           t.name as replacement_name,
           r.start_date,
           r.end_date,
           r.reason
    FROM teacher_replacements r
    JOIN teachers t ON r.replacement_teacher_id = t.id
    WHERE r.original_teacher_id = :teacher_id
    ORDER BY r.start_date DESC
");
$stmt->execute(['teacher_id' => $teacher_id]);
$replacements = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Replacement - <?= htmlspecialchars($teacher['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container my-4">
        <h2 class="mb-4">
            <i class="fas fa-exchange-alt me-2"></i>Teacher Replacement - <?= htmlspecialchars($teacher['name']) ?>
        </h2>

        <?php flashMessage(); ?>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">Set Teacher Replacement</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="replacement_teacher_id" class="form-label">Replacement Teacher</label>
                                <select class="form-select" id="replacement_teacher_id" 
                                        name="replacement_teacher_id" required>
                                    <option value="">Select replacement teacher...</option>
                                    <?php foreach ($available_teachers as $t): ?>
                                        <option value="<?= $t['id'] ?>">
                                            <?= htmlspecialchars($t['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a replacement teacher.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" 
                                       name="start_date" required>
                                <div class="invalid-feedback">
                                    Please select a start date.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" 
                                       name="end_date" required>
                                <div class="invalid-feedback">
                                    Please select an end date.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason for Replacement</label>
                                <textarea class="form-control" id="reason" name="reason" 
                                          rows="3" required></textarea>
                                <div class="invalid-feedback">
                                    Please provide a reason for the replacement.
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Set Replacement
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title mb-0">Replacement History</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($replacements): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Replacement Teacher</th>
                                            <th>Period</th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($replacements as $r): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($r['replacement_name']) ?></td>
                                            <td>
                                                <?= date('Y-m-d', strtotime($r['start_date'])) ?> to 
                                                <?= date('Y-m-d', strtotime($r['end_date'])) ?>
                                            </td>
                                            <td><?= htmlspecialchars($r['reason']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No replacement history found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Teachers
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Form validation
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()

    // Date validation
    document.getElementById('end_date').addEventListener('change', function() {
        var startDate = document.getElementById('start_date').value;
        var endDate = this.value;
        
        if (startDate && endDate && startDate > endDate) {
            this.setCustomValidity('End date must be after start date');
        } else {
            this.setCustomValidity('');
        }
    });
    </script>
</body>
</html> 