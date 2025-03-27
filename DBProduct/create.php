<?php
    include('connectionDB.php');

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        $name = $conn-> real_escape_string($_POST["name"]);
        $price = $conn->real_escape_string($_POST["price"]);

        $sql = "INSERT INTO prouducts (name , price) VALUES ('$name' ,'$price')";
        if($conn->query($sql) === TRUE){
            header("Location : index.php");
            exit();
        }else{
            echo "Error : " . $conn->error;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
  </head>
  <body>
    <h2>Add New Product</h2>
    <form method="post" action="">
      <label>Name : </label>
      <input type="text" name-"name" required /> <br />
      <label>Price : </label>
      <input type="text" name="price" required /> <br />
      <input type="submit" value="submit" />
    </form>
    <br />
    <a href=""></a>
  </body>
</html>
