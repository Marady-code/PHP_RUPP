<?php

    include ('connectionDB.php');

    if(isset($_GET['id'])){
        $id = intval($_GET['id']);
        $sql = "UPDATE products SET isActive = 0 WHERE id = $id";
        //$sql = "DELETE FROM products WHERE id = $id";
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