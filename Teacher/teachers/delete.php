<?php
require_once('../config/db_connect.php');

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$teacher_id = $_GET['id'];

try {
    $stmt = $conn->prepare("UPDATE teachers SET isActive = 0 WHERE teacher_id = ?");
    $stmt->execute([$teacher_id]);
    
    $_SESSION['message'] = "Teacher deleted successfully!";
} catch(PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header("Location: index.php");
exit();
?>