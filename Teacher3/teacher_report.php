<?php
require_once 'functions.php';

// Get parameters
$teacher_id = $_POST['teacher_id'] ?? null;
$month = $_POST['month'] ?? date('m');
$year = $_POST['year'] ?? date('Y');

// Validate parameters
if (!$teacher_id) {
    die('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Teacher ID is required</div>');
}

// Get teacher data
$teacher = getTeacher($teacher_id);
if (!$teacher) {
    die('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Teacher not found</div>');
}

// Get schedule and summary data
$schedule = getTeacherSchedule($teacher_id, $month, $year);
$summary = getTeacherMonthlySummary($teacher_id, $month, $year);

// Generate the report HTML
?>
<div class="teacher-report">
    <div class="card">
        <h3><i class="fas fa-user-tie"></i> Report for <?= htmlspecialchars($teacher['name']) ?> - <?= date('F Y', strtotime("$year-$month-01")) ?></h3>
        
        <?php if (empty($schedule)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No schedule entries found for this month.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Week</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Hours</th>
                            <th>Status</th>
                            <th>Substitute</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedule as $entry): ?>
                        <tr>
                            <td><?= $entry['week_number'] ?></td>
                            <td><?= date('M j, Y', strtotime($entry['day_date'])) ?></td>
                            <td><?= date('l', strtotime($entry['day_date'])) ?></td>
                            <td><?= $entry['hours'] ?>h</td>
                            <td>
                                <span class="status-badge <?= $entry['is_leave'] ? 'status-leave' : 'status-working' ?>">
                                    <?= $entry['is_leave'] ? 'On Leave' : 'Working' ?>
                                </span>
                            </td>
                            <td><?= $entry['substitute_name'] ?? 'None' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="stats-container" style="margin-top: 20px;">
                <div class="stat-card">
                    <div class="stat-label">Working Hours</div>
                    <div class="stat-value"><?= number_format($summary['working_hours'] ?? 0, 2) ?></div>
                    <div class="stat-label">hours</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-label">Substitute Hours</div>
                    <div class="stat-value"><?= number_format($summary['substitute_hours'] ?? 0, 2) ?></div>
                    <div class="stat-label">hours</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-label">Working Payment</div>
                    <div class="stat-value">$<?= number_format($summary['working_payment'] ?? 0, 2) ?></div>
                    <div class="stat-label">this month</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-label">Substitute Payment</div>
                    <div class="stat-value">$<?= number_format($summary['substitute_payment'] ?? 0, 2) ?></div>
                    <div class="stat-label">this month</div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>