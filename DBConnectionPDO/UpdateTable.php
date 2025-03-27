<?php
    include "Procedural.php";
    include "CreateTable.php";
        //update data
        $id = 1;
        $newName = "Sok Sovantha";
        $updateSQL = "UPDATE users SET name = '$newName' WHERE id = '$id'";
        if($conn -> query($updateSQL) === TRUE){
            echo "Data updated successfully.<br>";
        }else{
            echo "Error updating data." . $conn->error . "<br>";
        }
    
        //Query data
        $sql = "SELECT id, name FROM users";
        $result = $conn->query($sql);
    
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                echo "ID: " . $row["id"] . " - Name: " . $row["name"] . "<br>";
            }
        }else{
            echo "0 results";
        }
        //Closing the connection when you're done
        $conn->close();
?>