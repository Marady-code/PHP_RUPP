<?php
    // Database connection parameters
    $servername = "localhost";
    $username = "root";
    $password = "Suy123$%";
    $dbname = "dbm4";

    //Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    //Check connection
    if(!$conn){
        die("Connection failed: " . mysqli_connect_error());
    }

    echo "Connected successfully using MYSQL Procedural Style";
    // Don't forget to close the connection when you're done
    mysqli_close($conn);
?>