<?php
require_once '../../config/db_connect.php';

if(!isset($_GET['id'])) {
    header("Location: ../deleted/teachers.php");
    exit();
}

$teacher_id = $_GET['id'];

try {
    // Restore (set isActive to 1)
    $stmt = $conn->prepare("UPDATE teachers SET isActive = 1 WHERE teacher_id = ?");
    $stmt->execute([$teacher_id]);
    
    $_SESSION['message'] = "Teacher restored successfully!";
} catch(PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header("Location: ../deleted/teachers.php");
exit();
?>