<?php   // name of file is connectByPDO.php
    include 'connectByPDO.php';

     $createTableSQL = "CREATE TABLE IF NOT EXISTS users(
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE
    )";
    if($pdo->exec($createTableSQL) === false){
        echo "Error creating table!";
    }else{
        echo "Table 'users' created successfully!";
    }
?>