<?php
    $servername = "localhost";
    $username = "root";
    $password = "Rupp155";
    $dbname = "dbHospital";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed". $conn->connect_error);
    }

    //echo "Connected Successfully!"
?>