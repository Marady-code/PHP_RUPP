<?php
    include 'Procedural.php';

    //Database connection parameters
    $servername = "localhost";
    $username = "root";
    $password = "Suy123$%";
    $dbname = "dbm4";

    //Create connection using MYSQLi Object-Oriented style
    $conn = new mysqli($servername, $username, $password, $dbname);

    //Check connection
    if($conn ->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }
   // echo "Connected successfully using MYSQLi Object-Oriented style";
?>