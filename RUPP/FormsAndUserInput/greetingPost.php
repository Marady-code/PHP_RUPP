<?php
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        //Get the name from the form
        $name = $_GET["fullName"];

        //Display a personalized greeting
        echo "Hello, " . ($name) . "! Welcome to our website.";
    }

    //localhost:8085/rupp/FormsAndUserInput/greeting.php?fullName=Suybouern
?>