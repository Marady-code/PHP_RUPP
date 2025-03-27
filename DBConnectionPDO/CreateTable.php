<?php
    include 'UsingOOS.php';
    //CREATE TABLE
    $createTableSQL = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE)";
    if($conn->query($createTableSQL) === TRUE){
        echo "Table 'users' created successfully.<br>";
    }else{
        echo "Error creating table: " . $conn->error . "<br>";
    }

    //INSERT DATA
    $name = "Sok Panha";
    $email = "sok.panha@gmail.com";
    $insertSQL = "INSERT INTO users (name, email) VALUES ('$name', '$email') ON DUPLICATE KEY UPDATE name = VALUES(name)";
    if($conn->query($insertSQL) === TRUE){
        echo "Data inserted successfully.<br>";
    }else{
        echo "Error inserting data: " . $conn->error . "<br>";
    }        
?>