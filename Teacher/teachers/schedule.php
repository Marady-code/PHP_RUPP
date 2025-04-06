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

// Handle form submission for schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_schedule'])) {
            $stmt = $conn->prepare("
                INSERT INTO schedules (teacher_id, day_of_week, start_time, end_time)
                VALUES (:teacher_id, :day_of_week, :start_time, :end_time)
            ");
            
            $stmt->execute([
                'teacher_id' => $teacher_id,
                'day_of_week' => $_POST['day_of_week'],
                'start_time' => $_POST['start_time'],
                'end_time' => $_POST['end_time']
            ]);

            $_SESSION['message'] = "Schedule added successfully!";
        } elseif (isset($_POST['add_hours'])) {
            $stmt = $conn->prepare("
                INSERT INTO teaching_hours (teacher_id, date, hours_taught, notes)
                VALUES (:teacher_id, :date, :hours_taught, :notes)
            ");
            
            $stmt->execute([
                'teacher_id' => $teacher_id,
                'date' => $_POST['date'],
                'hours_taught' => $_POST['hours_taught'],
                'notes' => $_POST['notes']
            ]);

            $_SESSION['message'] = "Teaching hours recorded successfully!";
        }
        
        header("Location: schedule.php?id=" . $teacher_id);
        exit();
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

// Fetch current schedule
$stmt = $conn->prepare("
    SELECT * FROM schedules 
    WHERE teacher_id = :teacher_id 
    ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
");
$stmt->execute(['teacher_id' => $teacher_id]);
$schedules = $stmt->fetchAll();

// Fetch recent teaching hours
$stmt = $conn->prepare("
    SELECT * FROM teaching_hours 
    WHERE teacher_id = :teacher_id 
    ORDER BY date DESC 
    LIMIT 10
");
$stmt->execute(['teacher_id' => $teacher_id]);
$teaching_hours = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Schedule - <?= htmlspecialchars($teacher['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container my-4">
        <h2 class="mb-4">
            <i class="fas fa-calendar me-2"></i>Schedule Management - <?= htmlspecialchars($teacher['name']) ?>
        </h2>

        <?php flashMessage(); ?>

        <div class="row">
            <!-- Weekly Schedule Section -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">Weekly Schedule</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" class="mb-4">
                            <input type="hidden" name="add_schedule" value="1">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="day_of_week" class="form-label">Day of Week</label>
                                    <select class="form-select" id="day_of_week" name="day_of_week" required>
                                        <option value="">Select day...</option>
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                        <option value="Sunday">Sunday</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="start_time" class="form-label">Start Time</label>
                                    <input type="time" class="form-control" id="start_time" name="start_time" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="end_time" class="form-label">End Time</label>
                                    <input type="time" class="form-control" id="end_time" name="end_time" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i>Add Schedule
                            </button>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Time</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($schedules as $schedule): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($schedule['day_of_week']) ?></td>
                                        <td>
                                            <?= date('g:i A', strtotime($schedule['start_time'])) ?> - 
                                            <?= date('g:i A', strtotime($schedule['end_time'])) ?>
                                        </td>
                                        <td>
                                            <a href="delete_schedule.php?id=<?= $schedule['id'] ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
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

            <!-- Teaching Hours Section -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title mb-0">Record Teaching Hours</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" class="mb-4">
                            <input type="hidden" name="add_hours" value="1">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                            <div class="mb-3">
                                <label for="hours_taught" class="form-label">Hours Taught</label>
                                <input type="number" step="0.5" class="form-control" id="hours_taught" 
                                       name="hours_taught" required min="0" max="24">
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes (Optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Record Hours
                            </button>
                        </form>

                        <h4>Recent Teaching Hours</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Hours</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($teaching_hours as $hours): ?>
                                    <tr>
                                        <td><?= date('Y-m-d', strtotime($hours['date'])) ?></td>
                                        <td><?= number_format($hours['hours_taught'], 1) ?></td>
                                        <td><?= htmlspecialchars($hours['notes']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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
</body>
</html> 