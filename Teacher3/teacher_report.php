<?php
// teacher_report.php
require_once 'functions.php';

$teacher_id = $_GET['teacher_id'] ?? null;
$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

if ($teacher_id) {
    $teacher = getTeacher($teacher_id);
    $schedule = getTeacherSchedule($teacher_id, $month, $year);
    $summary = getTeacherMonthlySummary($teacher_id, $month, $year);
    
    echo "<h4>Report for {$teacher['name']} - " . date('F Y', mktime(0, 0, 0, $month, 1, $year)) . "</h4>";
    
    if (empty($schedule)) {
        echo "<p>No schedule entries found for this month.</p>";
    } else {
        echo "<table>
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
                <tbody>";
        
        foreach ($schedule as $entry) {
            echo "<tr>
                    <td>{$entry['week_number']}</td>
                    <td>{$entry['day_date']}</td>
                    <td>" . date('l', strtotime($entry['day_date'])) . "</td>
                    <td>{$entry['hours']}h</td>
                    <td>" . ($entry['is_leave'] ? 'On Leave' : 'Working') . "</td>
                    <td>" . ($entry['substitute_name'] ?? 'None') . "</td>
                </tr>";
        }
        
        echo "</tbody></table>";
        
        echo "<h4>Summary</h4>";
        echo "<p>Total Hours: " . number_format($summary['total_hours'], 2) . "h</p>";
        echo "<p>Total Payment: $" . number_format($summary['total_payment'], 2) . "</p>";
    }
}
?>