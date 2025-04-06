<?php
function getTeacherOptions($pdo) {
    $stmt = $pdo->query("SELECT id, name FROM teachers ORDER BY name");
    $options = '';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $options .= "<option value='{$row['id']}'>{$row['name']}</option>";
    }
    return $options;
}

function addTeacher($pdo, $data) {
    $stmt = $pdo->prepare("INSERT INTO teachers (name, start_date, phone, hourly_rate) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data['name'], $data['start_date'], $data['phone'], $data['hourly_rate']]);
}

function updateHourlyRate($pdo, $teacherId, $hourlyRate) {
    $stmt = $pdo->prepare("UPDATE teachers SET hourly_rate = ? WHERE id = ?");
    $stmt->execute([$hourlyRate, $teacherId]);
}

function addTeachingHours($pdo, $data) {
    $dayOfWeek = date('N', strtotime($data['date_taught']));
    $isSubstitute = isset($data['is_substitute']) ? 1 : 0;
    $substituteForId = $isSubstitute ? $data['substitute_for_id'] : null;
    
    $stmt = $pdo->prepare("INSERT INTO teaching_schedule 
                          (teacher_id, day_of_week, hours, date_taught, is_substitute, substitute_for_id) 
                          VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['teacher_id'],
        $dayOfWeek,
        $data['hours'],
        $data['date_taught'],
        $isSubstitute,
        $substituteForId
    ]);
}

function deleteTeacher($pdo, $teacherId) {
    // First get the teacher data to store in deleted_records
    $stmt = $pdo->prepare("SELECT * FROM teachers WHERE id = ?");
    $stmt->execute([$teacherId]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($teacher) {
        // Store in deleted_records
        $stmt = $pdo->prepare("INSERT INTO deleted_records (table_name, record_id, data) VALUES (?, ?, ?)");
        $stmt->execute(['teachers', $teacherId, json_encode($teacher)]);
        
        // Delete the teacher
        $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
        $stmt->execute([$teacherId]);
    }
}

function generateMonthlyReport($pdo) {
    // Get current month and year
    $month = date('m');
    $year = date('Y');
    
    // Get all teachers
    $teachers = $pdo->query("SELECT id, name, hourly_rate FROM teachers")->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($teachers)) {
        return "<p>No teachers found.</p>";
    }
    
    $html = '<table>
                <tr>
                    <th>Teacher</th>
                    <th>Hourly Rate ($)</th>
                    <th>Total Hours</th>
                    <th>Monthly Salary ($)</th>
                </tr>';
    
    foreach ($teachers as $teacher) {
        // Get total hours for the month
        $stmt = $pdo->prepare("SELECT SUM(hours) as total_hours 
                              FROM teaching_schedule 
                              WHERE teacher_id = ? 
                              AND MONTH(date_taught) = ? 
                              AND YEAR(date_taught) = ?");
        $stmt->execute([$teacher['id'], $month, $year]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalHours = $result['total_hours'] ?? 0;
        
        // Calculate salary
        $salary = $totalHours * $teacher['hourly_rate'];
        
        $html .= "<tr>
                    <td>{$teacher['name']}</td>
                    <td>{$teacher['hourly_rate']}</td>
                    <td>{$totalHours}</td>
                    <td>{$salary}</td>
                </tr>";
    }
    
    $html .= '</table>';
    return $html;
}

function showDeletedRecords($pdo) {
    $stmt = $pdo->query("SELECT * FROM deleted_records ORDER BY deleted_at DESC");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($records)) {
        return "<p>No deleted records found.</p>";
    }
    
    $html = '<table>
                <tr>
                    <th>Table</th>
                    <th>Record ID</th>
                    <th>Data</th>
                    <th>Deleted At</th>
                </tr>';
    
    foreach ($records as $record) {
        $data = json_decode($record['data'], true);
        $dataStr = '';
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $dataStr .= "$key: $value, ";
            }
            $dataStr = rtrim($dataStr, ', ');
        } else {
            $dataStr = $record['data'];
        }
        
        $html .= "<tr>
                    <td>{$record['table_name']}</td>
                    <td>{$record['record_id']}</td>
                    <td>{$dataStr}</td>
                    <td>{$record['deleted_at']}</td>
                </tr>";
    }
    
    $html .= '</table>';
    return $html;
}
?>