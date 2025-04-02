<?php
    include ('connectDB.php');

    if(isset($_GET['id'])){
        $id = intval($_GET['id']);
        // Change to soft delete
        $sql = "UPDATE patients SET isActive = 0 WHERE patient_id = $id";
        if($conn->query($sql) === TRUE) {
            header("Location: index.php");
            exit();
        }else{
            echo "Error: ". $conn->error;
        }
    }else{
        header("Location: index.php");
        exit();
    }
?>