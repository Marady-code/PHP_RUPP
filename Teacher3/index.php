<?php
// index.php
require_once 'functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_teacher'])) {
        addTeacher($_POST['name'], $_POST['start_date'], $_POST['phone'], $_POST['hourly_rate']);
    } elseif (isset($_POST['update_teacher'])) {
        updateTeacher($_POST['id'], $_POST['name'], $_POST['start_date'], $_POST['phone'], $_POST['hourly_rate']);
    } elseif (isset($_POST['delete_teacher'])) {
        deleteTeacher($_POST['id']);
    } elseif (isset($_POST['add_schedule'])) {
        addScheduleEntry(
            $_POST['teacher_id'],
            $_POST['week_number'],
            $_POST['day_date'],
            $_POST['hours'],
            isset($_POST['is_leave']),
            $_POST['substitute_id'] ?: null
        );
    } elseif (isset($_POST['update_schedule'])) {
        updateScheduleEntry(
            $_POST['id'],
            $_POST['teacher_id'],
            $_POST['week_number'],
            $_POST['day_date'],
            $_POST['hours'],
            isset($_POST['is_leave']),
            $_POST['substitute_id'] ?: null
        );
    } elseif (isset($_POST['delete_schedule'])) {
        deleteScheduleEntry($_POST['id']);
    }
}

// Get current month and year for display
$current_month = date('m');
$current_year = date('Y');
$teachers = getAllTeachers();
$schedules = getAllSchedules($current_month, $current_year);
$summary = getAllTeachersMonthlySummary($current_month, $current_year);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Schedule Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .form-group { margin-bottom: 10px; }
        label { display: inline-block; width: 150px; }
        .section { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .tabs { display: flex; margin-bottom: 20px; }
        .tab { padding: 10px 20px; cursor: pointer; background: #f0f0f0; margin-right: 5px; }
        .tab.active { background: #ddd; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>
    <h1>Teacher Schedule Management System</h1>
    
    <div class="tabs">
        <div class="tab active" onclick="openTab('teachers')">Teachers</div>
        <div class="tab" onclick="openTab('schedule')">Schedule</div>
        <div class="tab" onclick="openTab('summary')">Monthly Summary</div>
    </div>
    
    <!-- Teachers Tab -->
    <div id="teachers" class="tab-content active">
        <h2>Teacher Management</h2>
        
        <!-- Add Teacher Form -->
        <div class="section">
            <h3>Add New Teacher</h3>
            <form method="post">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="hourly_rate">Hourly Rate ($):</label>
                    <input type="number" id="hourly_rate" name="hourly_rate" step="0.01" min="0" value="10.00" required>
                </div>
                <button type="submit" name="add_teacher">Add Teacher</button>
            </form>
        </div>
        
        <!-- Teachers List -->
        <div class="section">
            <h3>All Teachers</h3>
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
                        <td><?= $teacher['start_date'] ?></td>
                        <td><?= htmlspecialchars($teacher['phone']) ?></td>
                        <td>$<?= number_format($teacher['hourly_rate'], 2) ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $teacher['id'] ?>">
                                <button type="submit" name="delete_teacher">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Schedule Tab -->
    <div id="schedule" class="tab-content">
        <h2>Schedule Management</h2>
        
        <!-- Add Schedule Entry Form -->
        <div class="section">
            <h3>Add Schedule Entry</h3>
            <form method="post">
                <div class="form-group">
                    <label for="teacher_id">Teacher:</label>
                    <select id="teacher_id" name="teacher_id" required>
                        <?php foreach ($teachers as $teacher): ?>
                            <option value="<?= $teacher['id'] ?>"><?= htmlspecialchars($teacher['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="week_number">Week Number:</label>
                    <input type="number" id="week_number" name="week_number" min="1" max="6" required>
                </div>
                <div class="form-group">
                    <label for="day_date">Date:</label>
                    <input type="date" id="day_date" name="day_date" required>
                </div>
                <div class="form-group">
                    <label for="hours">Hours:</label>
                    <input type="number" id="hours" name="hours" step="0.5" min="0" required>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="is_leave" name="is_leave"> On Leave
                    </label>
                </div>
                <div class="form-group" id="substitute_group" style="display:none;">
                    <label for="substitute_id">Substitute Teacher:</label>
                    <select id="substitute_id" name="substitute_id">
                        <option value="">-- None --</option>
                        <?php foreach ($teachers as $teacher): ?>
                            <option value="<?= $teacher['id'] ?>"><?= htmlspecialchars($teacher['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="add_schedule">Add Schedule</button>
            </form>
        </div>
        
        <!-- Schedule List -->
        <div class="section">
            <h3>Current Month Schedule (<?= date('F Y') ?>)</h3>
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
                        <td><?= $entry['day_date'] ?></td>
                        <td><?= date('l', strtotime($entry['day_date'])) ?></td>
                        <td><?= htmlspecialchars($entry['teacher_name']) ?></td>
                        <td><?= $entry['hours'] ?>h</td>
                        <td><?= $entry['is_leave'] ? 'On Leave' : 'Working' ?></td>
                        <td><?= $entry['substitute_name'] ?? 'None' ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $entry['id'] ?>">
                                <button type="submit" name="delete_schedule">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Summary Tab -->
    <div id="summary" class="tab-content">
        <h2>Monthly Summary</h2>
        
        <div class="section">
            <h3>All Teachers Summary for <?= date('F Y') ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>Teacher</th>
                        <th>Hourly Rate</th>
                        <th>Total Hours</th>
                        <th>Total Payment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($summary as $teacher): ?>
                    <tr>
                        <td><?= htmlspecialchars($teacher['name']) ?></td>
                        <td>$<?= number_format($teacher['hourly_rate'], 2) ?></td>
                        <td><?= number_format($teacher['total_hours'], 2) ?>h</td>
                        <td>$<?= number_format($teacher['total_payment'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h3>Individual Teacher Reports</h3>
            <form method="get" id="teacher_report_form">
                <div class="form-group">
                    <label for="report_teacher_id">Select Teacher:</label>
                    <select id="report_teacher_id" name="teacher_id" required>
                        <?php foreach ($teachers as $teacher): ?>
                            <option value="<?= $teacher['id'] ?>"><?= htmlspecialchars($teacher['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="report_month">Month:</label>
                    <select id="report_month" name="month">
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i ?>" <?= $i == $current_month ? 'selected' : '' ?>>
                                <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="report_year">Year:</label>
                    <input type="number" id="report_year" name="year" min="2000" max="2100" value="<?= $current_year ?>">
                </div>
                <button type="button" onclick="generateTeacherReport()">Generate Report</button>
            </form>
            
            <div id="teacher_report_result"></div>
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
        }
        
        // Show/hide substitute field based on leave checkbox
        document.getElementById('is_leave').addEventListener('change', function() {
            document.getElementById('substitute_group').style.display = this.checked ? 'block' : 'none';
        });
        
        // Generate teacher report via AJAX
        function generateTeacherReport() {
            const form = document.getElementById('teacher_report_form');
            const formData = new FormData(form);
            
            fetch('teacher_report.php?' + new URLSearchParams(formData))
                .then(response => response.text())
                .then(data => {
                    document.getElementById('teacher_report_result').innerHTML = data;
                });
        }
    </script>
</body>
</html>