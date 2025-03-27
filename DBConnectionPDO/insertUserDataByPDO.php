<?php   // name of file is connectByPDO.php
     include 'connectByPDO.php';

    $insertSQL = "INSERT INTO users (username, email)
                VALUES (:user_name, :user_email)";
    $sql = $pdo->prepare($insertSQL);

    $name = "Long Maraduek";
    $email = "long.maraduek@gmail.com";

   
    $sql->execute([
        'user_name' => $name,
        'user_email' => $email
    ]);
    echo "Data inserted successfully!";
?>