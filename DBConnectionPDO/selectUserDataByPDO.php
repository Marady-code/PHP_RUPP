<?php   // name of file is connectByPDO.php
    include 'connectByPDO.php';
    $selectSQL = "SELECT * FROM users";
    $sql = $pdo -> query($selectSQL);
    //Fetch all rows as an associative array
    $users = $sql -> fetchAll(PDO::FETCH_ASSOC);
    //Display all users information
    if($users){
        echo "<h3>Users Information:</h3>";
        foreach($users as $user){
            echo "ID: " . $user['id']
                . " - Name: " . $user['username']
                . " - Email " . $user['email'] . "<br>";
        }
    }else{
        echo "No data found.";
    }
?>