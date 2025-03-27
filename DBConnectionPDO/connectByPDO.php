<?php   // name of file is connectByPDO.php
    $servername = "localhost";
    $username = "root";
    $password = "Rupp155";
    $dbname = "dbPDO";

    try{
        // Set up DSN (Data Source Name) and 
        //create a PDO instance
        $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);

        //Set error reporting mode to exception
        $pdo -> setAttribute(PDO::ATTR_ERRMODE, 
                            PDO::ERRMODE_EXCEPTION);
       // echo "Connected successfully by using PDO!";
    }catch(Exception $e){
        echo "Error: " . $e -> getMessage();
    }
?>