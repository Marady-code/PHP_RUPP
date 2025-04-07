<?php
// functions.php
require_once 'config.php';

// Teacher functions
function addTeacher($name, $start_date, $phone, $hourly_rate) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO teachers (name, start_date, phone, hourly_rate) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$name, $start_date, $phone, $hourly_rate]);
}

function updateTeacher($id, $name, $start_date, $phone, $hourly_rate) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE teachers SET name=?, start_date=?, phone=?, hourly_rate=? WHERE id=?");
    return $stmt->execute([$name, $start_date, $phone, $hourly_rate, $id]);
}

function deleteTeacher($id) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM teachers WHERE id=?");
    return $stmt->execute([$id]);
}

function getTeacher($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM teachers WHERE id=?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllTeachers() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM teachers ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Schedule functions
function addScheduleEntry($teacher_id, $week_number, $day_date, $hours, $is_leave = false, $substitute_id = null) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO schedule (teacher_id, week_number, day_date, hours, is_leave, substitute_id) VALUES (?, ?, ?, ?, ?, ?)");
    
    // Convert empty substitute_id to NULL
    $substitute_id = ($substitute_id === '' || $substitute_id === null) ? null : $substitute_id;
    
    return $stmt->execute([$teacher_id, $week_number, $day_date, $hours, $is_leave, $substitute_id]);
}

function updateScheduleEntry($id, $teacher_id, $week_number, $day_date, $hours, $is_leave = false, $substitute_id = null) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE schedule SET teacher_id=?, week_number=?, day_date=?, hours=?, is_leave=?, substitute_id=? WHERE id=?");
    
    // Convert empty substitute_id to NULL
    $substitute_id = ($substitute_id === '' || $substitute_id === null) ? null : $substitute_id;
    
    return $stmt->execute([$teacher_id, $week_number, $day_date, $hours, $is_leave, $substitute_id, $id]);
}

function deleteScheduleEntry($id) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM schedule WHERE id=?");
    return $stmt->execute([$id]);
}

function getTeacherSchedule($teacher_id, $month, $year) {
    $db = getDB();
    
    $startDate = "$year-$month-01";
    $endDate = date('Y-m-t', strtotime($startDate));
    
    $query = "SELECT s.*, 
                     t2.name as substitute_name 
              FROM schedule s
              LEFT JOIN teachers t2 ON s.substitute_id = t2.id
              WHERE s.teacher_id = :teacher_id 
              AND s.day_date BETWEEN :start_date AND :end_date
              ORDER BY s.day_date ASC";
              
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':teacher_id' => $teacher_id,
        ':start_date' => $startDate,
        ':end_date' => $endDate
    ]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getAllSchedules($month = null, $year = null) {
    $db = getDB();
    $sql = "SELECT s.*, t.name as teacher_name, t.hourly_rate, st.name as substitute_name 
            FROM schedule s 
            JOIN teachers t ON s.teacher_id = t.id 
            LEFT JOIN teachers st ON s.substitute_id = st.id";
    
    $params = [];
    if ($month && $year) {
        $sql .= " WHERE MONTH(day_date) = ? AND YEAR(day_date) = ?";
        $params[] = $month;
        $params[] = $year;
    }
    
    $sql .= " ORDER BY week_number, day_date";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTeacherMonthlySummary($teacher_id, $month, $year) {
    $db = getDB();
    
    // Get the teacher details first
    $teacherQuery = "SELECT * FROM teachers WHERE id = :id LIMIT 1";
    $teacherStmt = $db->prepare($teacherQuery);
    $teacherStmt->execute([':id' => $teacher_id]);
    $teacher = $teacherStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$teacher) {
        return null;
    }
    
    // Calculate working hours (when not on leave)
    $workingHoursQuery = "SELECT COALESCE(SUM(hours), 0) as working_hours 
                          FROM schedule 
                          WHERE teacher_id = :teacher_id 
                          AND day_date BETWEEN :start_date AND :end_date
                          AND is_leave = 0";
                          
    $workingStmt = $db->prepare($workingHoursQuery);
    $workingStmt->execute([
        ':teacher_id' => $teacher_id,
        ':start_date' => "$year-$month-01",
        ':end_date' => date('Y-m-t', strtotime("$year-$month-01"))
    ]);
    
    $workingHours = $workingStmt->fetch(PDO::FETCH_ASSOC)['working_hours'] ?: 0;
    
    // Calculate substitute hours
    $substituteHoursQuery = "SELECT COALESCE(SUM(hours), 0) as substitute_hours 
                             FROM schedule 
                             WHERE substitute_id = :teacher_id 
                             AND day_date BETWEEN :start_date AND :end_date";
                          
    $substituteStmt = $db->prepare($substituteHoursQuery);
    $substituteStmt->execute([
        ':teacher_id' => $teacher_id,
        ':start_date' => "$year-$month-01",
        ':end_date' => date('Y-m-t', strtotime("$year-$month-01"))
    ]);
    
    $substituteHours = $substituteStmt->fetch(PDO::FETCH_ASSOC)['substitute_hours'] ?: 0;
    
    // Calculate payments
    $workingPayment = $workingHours * $teacher['hourly_rate'];
    $substitutePayment = $substituteHours * $teacher['hourly_rate'];
    
    return [
        'working_hours' => $workingHours,
        'substitute_hours' => $substituteHours,
        'working_payment' => $workingPayment,
        'substitute_payment' => $substitutePayment,
        'total_hours' => $workingHours + $substituteHours,
        'total_payment' => $workingPayment + $substitutePayment
    ];
}


function getAllTeachersMonthlySummary($month, $year) {
    $db = getDB();
    
    $teachers = getAllTeachers();
    $summary = [];
    
    foreach ($teachers as $teacher) {
        $teacherSummary = getTeacherMonthlySummary($teacher['id'], $month, $year);
        if ($teacherSummary) {
            $summary[] = array_merge($teacher, $teacherSummary);
        }
    }
    
    return $summary;
}

function getScheduleEntry($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT s.*, t.name as teacher_name, st.name as substitute_name 
                         FROM schedule s 
                         LEFT JOIN teachers t ON s.teacher_id = t.id 
                         LEFT JOIN teachers st ON s.substitute_id = st.id 
                         WHERE s.id=?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


?>