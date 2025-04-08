<?php
require_once 'functions.php';

$teacher_id = $_GET['teacher_id'] ?? null;
$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

if ($teacher_id) {
    $teacher = getTeacher($teacher_id);
    $schedule = getTeacherSchedule($teacher_id, $month, $year);
    $summary = getTeacherMonthlySummary($teacher_id, $month, $year);
    
    echo "<div class='card'>";
    echo "<h3><i class='fas fa-user-tie'></i> Report for {$teacher['name']} - " . date('F Y', mktime(0, 0, 0, $month, 1, $year)) . "</h3>";
    
    if (empty($schedule)) {
        echo "<div class='alert alert-info'><i class='fas fa-info-circle'></i> No schedule entries found for this month.</div>";
    } else {
        echo "<div style='overflow-x: auto;'>";
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
                    <td>" . date('M j, Y', strtotime($entry['day_date'])) . "</td>
                    <td>" . date('l', strtotime($entry['day_date'])) . "</td>
                    <td>{$entry['hours']}h</td>
                    <td>
                        <span class='status-badge " . ($entry['is_leave'] ? 'status-leave' : 'status-working') . "'>
                            " . ($entry['is_leave'] ? 'On Leave' : 'Working') . "
                        </span>
                    </td>
                    <td>" . ($entry['substitute_name'] ?? 'None') . "</td>
                </tr>";
        }
        
        echo "</tbody></table></div>";
        
        echo "<div class='stats-container' style='margin-top: 20px;'>";
        echo "<div class='stat-card'>
                <div class='stat-label'>Regular Hours</div>
                <div class='stat-value'>" . number_format($summary['working_hours'], 2) . "</div>
                <div class='stat-label'>hours</div>
              </div>";
        
        echo "<div class='stat-card'>
                <div class='stat-label'>Regular Payment</div>
                <div class='stat-value'>$" . number_format($summary['teacher_payment'], 2) . "</div>
                <div class='stat-label'>@ $" . $teacher['hourly_rate'] . "/hour</div>
              </div>";
        
        echo "<div class='stat-card'>
                <div class='stat-label'>Substitute Hours</div>
                <div class='stat-value'>" . number_format($summary['substitute_hours'], 2) . "</div>
                <div class='stat-label'>hours</div>
              </div>";
        
        echo "<div class='stat-card'>
                <div class='stat-label'>Substitute Payment</div>
                <div class='stat-value'>$" . number_format($summary['substitute_payment'], 2) . "</div>
                <div class='stat-label'>@ $" . $teacher['hourly_rate'] . "/hour</div>
              </div>";
        
        echo "<div class='stat-card'>
                <div class='stat-label'>Total Payment</div>
                <div class='stat-value'>$" . number_format(($summary['teacher_payment'] + $summary['substitute_payment']), 2) . "</div>
                <div class='stat-label'>this month</div>
              </div>";
        echo "</div>";
    }
    echo "</div>";
}
?>