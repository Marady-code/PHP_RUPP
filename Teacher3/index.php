<?php
require_once 'functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_teacher'])) {
        if (addTeacher($_POST['name'], $_POST['start_date'], $_POST['phone'], $_POST['hourly_rate'])) {
            $success_message = "Teacher added successfully!";
        }
    } elseif (isset($_POST['update_teacher'])) {
        if (updateTeacher($_POST['id'], $_POST['name'], $_POST['start_date'], $_POST['phone'], $_POST['hourly_rate'])) {
            $success_message = "Teacher updated successfully!";
        }
    } elseif (isset($_POST['delete_teacher'])) {
        if (deleteTeacher($_POST['id'])) {
            $success_message = "Teacher deleted successfully!";
        }
    } elseif (isset($_POST['add_schedule'])) {
        $is_leave = isset($_POST['is_leave']) ? 1 : 0;
        if (addScheduleEntry(
            $_POST['teacher_id'],
            $_POST['week_number'],
            $_POST['day_date'],
            $_POST['hours'],
            $is_leave,
            $_POST['substitute_id'] ?? null
        )) {
            $success_message = "Schedule added successfully!";
        }
    } elseif (isset($_POST['update_schedule'])) {
        $is_leave = isset($_POST['is_leave']) ? 1 : 0;
        if (updateScheduleEntry(
            $_POST['id'],
            $_POST['teacher_id'],
            $_POST['week_number'],
            $_POST['day_date'],
            $_POST['hours'],
            $is_leave,
            $_POST['substitute_id'] ?? null
        )) {
            $success_message = "Schedule updated successfully!";
        }
    } elseif (isset($_POST['delete_schedule'])) {
        if (deleteScheduleEntry($_POST['id'])) {
            $success_message = "Schedule entry deleted successfully!";
        }
    }
    
    // Refresh the page to show changes
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Get current month and year for display
$current_month = date('m');
$current_year = date('Y');
$teachers = getAllTeachers();
$schedules = getAllSchedules($current_month, $current_year);
$summary = getAllTeachersMonthlySummary($current_month, $current_year);
$total_hours = array_sum(array_column($summary, 'total_hours'));
$total_payment = array_sum(array_column($summary, 'total_payment'));

// Check if we're editing a teacher
$editing_teacher = isset($_GET['edit_teacher']) ? getTeacher($_GET['edit_teacher']) : null;

// Check if we're editing a schedule
$editing_schedule = isset($_GET['edit_schedule']) ? getScheduleEntry($_GET['edit_schedule']) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Schedule Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container header-content">
            <h1><i class="fas fa-chalkboard-teacher"></i> Teacher Schedule Manager</h1>
        </div>
    </header>
    
    <main class="container">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $success_message ?>
            </div>
        <?php endif; ?>
        
        <div class="tabs">
            <div class="tab active" onclick="openTab('teachers')">
                <i class="fas fa-users"></i> Teachers
            </div>
            <div class="tab" onclick="openTab('schedule')">
                <i class="fas fa-calendar-alt"></i> Schedule
            </div>
            <div class="tab" onclick="openTab('summary')">
                <i class="fas fa-chart-pie"></i> Reports
            </div>
        </div>
        
        <!-- Teachers Tab -->
        <div id="teachers" class="tab-content active">
            <h2><i class="fas fa-users"></i> Teacher Management</h2>
            
            <div class="card">
                <h3><i class="fas fa-user-plus"></i> <?= $editing_teacher ? 'Edit Teacher' : 'Add New Teacher' ?></h3>
                <form method="post">
                    <?php if ($editing_teacher): ?>
                        <input type="hidden" name="id" value="<?= $editing_teacher['id'] ?>">
                    <?php endif; ?>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" placeholder="Enter teacher's full name" 
                                       value="<?= $editing_teacher ? htmlspecialchars($editing_teacher['name']) : '' ?>" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" id="start_date" name="start_date" 
                                       value="<?= $editing_teacher ? $editing_teacher['start_date'] : date('Y-m-d') ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" id="phone" name="phone" placeholder="Enter phone number" 
                                       value="<?= $editing_teacher ? htmlspecialchars($editing_teacher['phone']) : '' ?>" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="hourly_rate">Hourly Rate ($)</label>
                                <input type="number" id="hourly_rate" name="hourly_rate" step="0.01" min="0" 
                                       value="<?= $editing_teacher ? $editing_teacher['hourly_rate'] : '10.00' ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" name="<?= $editing_teacher ? 'update_teacher' : 'add_teacher' ?>" class="btn-success">
                        <i class="fas fa-save"></i> <?= $editing_teacher ? 'Update Teacher' : 'Add Teacher' ?>
                    </button>
                    
                    <?php if ($editing_teacher): ?>
                        <a href="?" class="btn">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="card">
                <h3><i class="fas fa-list"></i> All Teachers</h3>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Start Date</th>
                                <th>Phone</th>
                                <th>Hourly Rate</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teachers as $teacher): ?>
                            <tr>
                                <td><?= $teacher['id'] ?></td>
                                <td><?= htmlspecialchars($teacher['name']) ?></td>
                                <td><?= date('M j, Y', strtotime($teacher['start_date'])) ?></td>
                                <td><?= htmlspecialchars($teacher['phone']) ?></td>
                                <td>$<?= number_format($teacher['hourly_rate'], 2) ?></td>
                                <td>
                                    <a href="?edit_teacher=<?= $teacher['id'] ?>" class="btn btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $teacher['id'] ?>">
                                        <button type="submit" name="delete_teacher" class="btn-danger btn-sm"
                                        onclick="return confirmDeleteTeacher();">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Schedule Tab -->
        <div id="schedule" class="tab-content">
            <h2><i class="fas fa-calendar-alt"></i> Schedule Management</h2>
            
            <div class="card">
                <h3><i class="fas fa-plus-circle"></i> <?= $editing_schedule ? 'Edit Schedule Entry' : 'Add Schedule Entry' ?></h3>
                <form method="post">
                    <?php if ($editing_schedule): ?>
                        <input type="hidden" name="id" value="<?= $editing_schedule['id'] ?>">
                    <?php endif; ?>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="teacher_id">Teacher</label>
                                <select id="teacher_id" name="teacher_id" required>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= $teacher['id'] ?>" 
                                            <?= ($editing_schedule && $editing_schedule['teacher_id'] == $teacher['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($teacher['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="week_number">Week Number</label>
                                <input type="number" id="week_number" name="week_number" min="1" max="6" 
                                       value="<?= $editing_schedule ? $editing_schedule['week_number'] : '1' ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="day_date">Date</label>
                                <input type="date" id="day_date" name="day_date" 
                                       value="<?= $editing_schedule ? $editing_schedule['day_date'] : date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="hours">Hours</label>
                                <input type="number" id="hours" name="hours" step="0.5" min="0" 
                                       value="<?= $editing_schedule ? $editing_schedule['hours'] : '' ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="is_leave" name="is_leave" 
                                   <?= ($editing_schedule && $editing_schedule['is_leave']) ? 'checked' : '' ?>>
                            On Leave
                        </label>
                    </div>
                    
                    <div class="form-group" id="substitute_group" style="<?= ($editing_schedule && $editing_schedule['is_leave']) ? 'display:block;' : 'display:none;' ?>">
                        <label for="substitute_id">Substitute Teacher</label>
                        <select id="substitute_id" name="substitute_id">
                            <option value="">-- None --</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>" 
                                    <?= ($editing_schedule && $editing_schedule['substitute_id'] == $teacher['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($teacher['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" name="<?= $editing_schedule ? 'update_schedule' : 'add_schedule' ?>" class="btn-success">
                        <i class="fas fa-calendar-plus"></i> <?= $editing_schedule ? 'Update Schedule' : 'Add Schedule' ?>
                    </button>
                    
                    <?php if ($editing_schedule): ?>
                        <a href="?" class="btn">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="card">
                <h3><i class="fas fa-calendar-week"></i> Current Month Schedule (<?= date('F Y') ?>)</h3>
                
                <!-- Calendar View -->
                <div class="calendar-container">
                    <?php
                    $first_day_of_month = date('Y-m-01', strtotime("$current_year-$current_month-01"));
                    $last_day_of_month = date('Y-m-t', strtotime("$current_year-$current_month-01"));
                    
                    // Create a calendar grid
                    $start_date = new DateTime($first_day_of_month);
                    $end_date = new DateTime($last_day_of_month);
                    
                    // Find the first day of the week (Sunday)
                    while ($start_date->format('w') != 0) {
                        $start_date->modify('-1 day');
                    }
                    
                    // Find the last day of the week (Saturday)
                    while ($end_date->format('w') != 6) {
                        $end_date->modify('+1 day');
                    }
                    
                    $interval = new DateInterval('P1D');
                    $date_range = new DatePeriod($start_date, $interval, $end_date);
                    
                    $days = [];
                    foreach ($date_range as $date) {
                        $days[] = $date->format('Y-m-d');
                    }
                    
                    $weeks = array_chunk($days, 7);
                    ?>
                    
                    <div class="calendar">
                        <div class="calendar-header">Sunday</div>
                        <div class="calendar-header">Monday</div>
                        <div class="calendar-header">Tuesday</div>
                        <div class="calendar-header">Wednesday</div>
                        <div class="calendar-header">Thursday</div>
                        <div class="calendar-header">Friday</div>
                        <div class="calendar-header">Saturday</div>
                        
                        <?php foreach ($weeks as $week): ?>
                            <?php foreach ($week as $day): 
                                $day_entries = array_filter($schedules, function($entry) use ($day) {
                                    return $entry['day_date'] == $day;
                                });
                                $is_current_month = (date('m', strtotime($day)) == $current_month);
                                ?>
                                <div class="calendar-day <?= !$is_current_month ? 'other-month' : '' ?>">
                                    <div class="calendar-date">
                                        <?= date('j', strtotime($day)) ?>
                                        <?php if (!$is_current_month): ?>
                                            <small><?= date('M', strtotime($day)) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <?php foreach ($day_entries as $entry): ?>
                                        <div class="calendar-entry <?= $entry['is_leave'] ? 'leave' : '' ?>" 
                                             onclick="openScheduleModal(<?= $entry['id'] ?>)">
                                            <strong><?= htmlspecialchars($entry['teacher_name']) ?></strong><br>
                                            <?= $entry['hours'] ?>h
                                            <?php if ($entry['is_leave']): ?>
                                                <i class="fas fa-umbrella-beach"></i>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Table View -->
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Week</th>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Teacher</th>
                                <th>Hours</th>
                                <th>Status</th>
                                <th>Substitute</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $entry): ?>
                            <tr>
                                <td><?= $entry['week_number'] ?></td>
                                <td><?= date('M j, Y', strtotime($entry['day_date'])) ?></td>
                                <td><?= date('l', strtotime($entry['day_date'])) ?></td>
                                <td><?= htmlspecialchars($entry['teacher_name']) ?></td>
                                <td><?= $entry['hours'] ?>h</td>
                                <td>
                                    <span class="status-badge <?= $entry['is_leave'] ? 'status-leave' : 'status-working' ?>">
                                        <?= $entry['is_leave'] ? 'On Leave' : 'Working' ?>
                                    </span>
                                </td>
                                <td><?= $entry['substitute_name'] ?? 'None' ?></td>
                                <td>
                                    <a href="?edit_schedule=<?= $entry['id'] ?>" class="btn btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $entry['id'] ?>">
                                        <button type="submit" name="delete_schedule" class="btn-danger btn-sm" onclick="return confirmDeleteSchedule();">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Summary Tab (remains the same) -->
        <!-- Summary Tab -->
        <div id="summary" class="tab-content">
            <h2><i class="fas fa-chart-pie"></i> Monthly Reports</h2>
            
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-label">Total Teachers</div>
                    <div class="stat-value"><?= count($teachers) ?></div>
                    <div class="stat-label">Active</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-label">Current Month</div>
                    <div class="stat-value"><?= date('F Y') ?></div>
                    <div class="stat-label">Schedule Entries: <?= count($schedules) ?></div>
                </div>
                
                <?php 
                $total_hours = array_sum(array_column($summary, 'total_hours'));
                $total_payment = array_sum(array_column($summary, 'total_payment'));
                ?>
                
                <div class="stat-card">
                    <div class="stat-label">Total Hours</div>
                    <div class="stat-value"><?= number_format($total_hours ?? 0, 2) ?></div>
                    <div class="stat-label">hours</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Total Payment</div>
                    <div class="stat-value">$<?= number_format($total_payment ?? 0, 2) ?></div>
                    <div class="stat-label">this month</div>
                </div>
            </div>
            
            <div class="card">
                <h3><i class="fas fa-file-invoice-dollar"></i> Monthly Summary for <?= date('F Y') ?></h3>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Hourly Rate</th>
                                <th>Regular Hours</th>
                                <th>Substitute Hours</th>
                                <th>Total Hours</th>
                                <th>Total Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($summary as $teacher): ?>
                            <tr>
                                <td><?= htmlspecialchars($teacher['name']) ?></td>
                                <td>$<?= number_format($teacher['hourly_rate'], 2) ?></td>
                                <td><?= number_format($teacher['working_hours'], 2) ?>h</td>
                                <td><?= number_format($teacher['substitute_hours'], 2) ?>h</td>
                                <td><?= number_format($teacher['total_hours'], 2) ?>h</td>
                                <td>$<?= number_format($teacher['total_payment'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card">
                <h3><i class="fas fa-user-graduate"></i> Individual Teacher Reports</h3>
                <form method="get" id="teacher_report_form">
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="report_teacher_id">Select Teacher</label>
                                <select id="report_teacher_id" name="teacher_id" required>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= $teacher['id'] ?>"><?= htmlspecialchars($teacher['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="report_month">Month</label>
                                <select id="report_month" name="month">
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?= $i ?>" <?= $i == $current_month ? 'selected' : '' ?>>
                                            <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="report_year">Year</label>
                                <input type="number" id="report_year" name="year" min="2000" max="2100" value="<?= $current_year ?>">
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" onclick="generateTeacherReport()" class="btn">
                        <i class="fas fa-file-pdf"></i> Generate Report
                    </button>
                </form>
                
                <div id="teacher_report_result" style="margin-top: 20px;"></div>
            </div>
        </div>
    </main>
    
    <!-- Schedule Entry Modal -->
    <div id="scheduleModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>
    
    <script>
        // Tab functionality
        function openTab(tabName) {
            const tabs = document.getElementsByClassName('tab');
            for (let i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            
            document.querySelector(`.tab[onclick="openTab('${tabName}')"]`).classList.add('active');
            document.getElementById(tabName).classList.add('active');
            
            // Store the active tab in localStorage
            localStorage.setItem('activeTab', tabName);
        }
        
        // Show/hide substitute field based on leave checkbox
        document.getElementById('is_leave').addEventListener('change', function() {
            document.getElementById('substitute_group').style.display = this.checked ? 'block' : 'none';
        });
        
        // Generate teacher report via AJAX
        function generateTeacherReport() {
            const form = document.getElementById('teacher_report_form');
            const formData = new FormData(form);
            const resultDiv = document.getElementById('teacher_report_result');
            
            resultDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Generating report...</div>';
            
            fetch('teacher_report.php?' + new URLSearchParams(formData))
                .then(response => response.text())
                .then(data => {
                    resultDiv.innerHTML = data;
                })
                .catch(error => {
                    resultDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error generating report: ${error}</div>`;
                });
        }
        
        // Modal functions
        function openScheduleModal(scheduleId) {
            fetch(`get_schedule_entry.php?id=${scheduleId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('modalContent').innerHTML = data;
                    document.getElementById('scheduleModal').style.display = 'block';
                });
        }
        
        function closeModal() {
            document.getElementById('scheduleModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('scheduleModal');
            if (event.target == modal) {
                closeModal();
            }
        }
        
        // Check for active tab in localStorage on page load
        document.addEventListener('DOMContentLoaded', function() {
            const activeTab = localStorage.getItem('activeTab') || 'teachers';
            openTab(activeTab);
            
            // Set default date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('day_date').value = today;
            document.getElementById('start_date').value = today;
            
            // Set week number to 1 by default
            document.getElementById('week_number').value = 1;
        });

        function confirmDeleteTeacher(form) {
            if (confirm("Are you sure you want to delete this teacher? This will remove them from active lists but keep their records.")) {
                form.submit();
            }
            return false;
        }

        function confirmDeleteSchedule(form) {
            if (confirm("Are you sure you want to delete this schedule entry?")) {
                form.submit();
            }
            return false;
        }

    </script>
</body>
</html>
