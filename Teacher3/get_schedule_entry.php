<?php
require_once 'functions.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $entry = getScheduleEntry($id);
    if ($entry) {
        echo "<h3>Schedule Details</h3>";
        echo "<p><strong>Teacher:</strong> {$entry['teacher_name']}</p>";
        echo "<p><strong>Date:</strong> " . date('l, F j, Y', strtotime($entry['day_date'])) . "</p>";
        echo "<p><strong>Week:</strong> {$entry['week_number']}</p>";
        echo "<p><strong>Hours:</strong> {$entry['hours']}</p>";
        echo "<p><strong>Status:</strong> " . ($entry['is_leave'] ? 'On Leave' : 'Working') . "</p>";
        
        if ($entry['is_leave'] && $entry['substitute_name']) {
            echo "<p><strong>Substitute:</strong> {$entry['substitute_name']}</p>";
        }
        
        echo "<div style='margin-top: 20px;'>";
        echo "<a href='?edit_schedule={$entry['id']}' class='btn'>";
        echo "<i class='fas fa-edit'></i> Edit";
        echo "</a>";
        
        echo "<form method='post' style='display:inline; margin-left:10px;'>";
        echo "<input type='hidden' name='id' value='{$entry['id']}'>";
        echo "<button type='submit' name='delete_schedule' class='btn-danger'>";
        echo "<i class='fas fa-trash-alt'></i> Delete";
        echo "</button>";
        echo "</form>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-danger'>Schedule entry not found</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Invalid request</div>";
}
?>