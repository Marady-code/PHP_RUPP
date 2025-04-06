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
    return $stmt->execute([$teacher_id, $week_number, $day_date, $hours, $is_leave, $substitute_id]);
}

function updateScheduleEntry($id, $teacher_id, $week_number, $day_date, $hours, $is_leave = false, $substitute_id = null) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE schedule SET teacher_id=?, week_number=?, day_date=?, hours=?, is_leave=?, substitute_id=? WHERE id=?");
    return $stmt->execute([$teacher_id, $week_number, $day_date, $hours, $is_leave, $substitute_id, $id]);
}

function deleteScheduleEntry($id) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM schedule WHERE id=?");
    return $stmt->execute([$id]);
}

function getTeacherSchedule($teacher_id, $month = null, $year = null) {
    $db = getDB();
    $params = [$teacher_id];
    $sql = "SELECT s.*, t.name as teacher_name, st.name as substitute_name 
            FROM schedule s 
            LEFT JOIN teachers t ON s.teacher_id = t.id 
            LEFT JOIN teachers st ON s.substitute_id = st.id 
            WHERE s.teacher_id=?";
    
    if ($month && $year) {
        $sql .= " AND MONTH(day_date) = ? AND YEAR(day_date) = ?";
        $params[] = $month;
        $params[] = $year;
    }
    
    $sql .= " ORDER BY day_date";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
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
    $stmt = $db->prepare("SELECT 
                            SUM(hours) as total_hours, 
                            SUM(hours * (IFNULL(substitute_id, teacher_id) = teacher_id) * hourly_rate) as total_payment
                          FROM schedule s
                          JOIN teachers t ON s.teacher_id = t.id
                          WHERE teacher_id=? AND MONTH(day_date)=? AND YEAR(day_date)=?");
    $stmt->execute([$teacher_id, $month, $year]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllTeachersMonthlySummary($month, $year) {
    $db = getDB();
    $stmt = $db->prepare("SELECT 
                            t.id, 
                            t.name, 
                            t.hourly_rate,
                            SUM(s.hours) as total_hours, 
                            SUM(s.hours * t.hourly_rate) as total_payment
                          FROM schedule s
                          JOIN teachers t ON s.teacher_id = t.id
                          WHERE MONTH(day_date)=? AND YEAR(day_date)=?
                          GROUP BY t.id, t.name, t.hourly_rate
                          ORDER BY t.name");
    $stmt->execute([$month, $year]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>