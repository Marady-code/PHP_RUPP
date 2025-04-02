<?php
    include ('connectDB.php');

    if(isset($_GET['id'])){
        $id = intval($_GET['id']);
        // Restore patient - set isActive to 1
        $sql = "UPDATE patients SET isActive = 1 WHERE patient_id = $id";
        if($conn->query($sql) === TRUE) {
            header("Location: deleted_patients.php");
            exit();
        }else{
            echo "Error: ". $conn->error;
        }
    }else{
        header("Location: index.php");
        exit();
    }
?>