<?php   // name of file is connectByPDO.php
    include 'connectByPDO.php';
    $updateSQL = "UPDATE users
                SET username = :new_username,
                    email = :new_email
                WHERE id = :user_id";
    $sql = $pdo -> prepare($updateSQL);

    $id = 1;
    $newName = "Maraduekk Sarik";
    $newEmail = "sarik.maraduekk@gmail.com";

    $sql -> execute(['new_username' => $newName,
                    'new_email' => $newEmail,
                    'user_id' => $id]);
    echo "Data updated successfully!";
?>