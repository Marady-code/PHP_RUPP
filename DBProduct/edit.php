<?php
    include ('connectionDB.php');

    if(isset($GET['id'])){
        header("Location : index.php"); exit();
    }
    $id = intval($_GET["id"]);

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $name = $conn->real_escape_string($_POST["name"]);
        $price = $conn->real_escape_string($_POST["price"]);

        $sql = "UPDATE products SET name='$name', price='$price' WHERE id=$id";
        if($conn->query($sql) === TRUE){
            header("Location : index.php"); 
            exit();
        }else{
            echo "Error: " . $conn->error;
        }
    }

    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);
    $product = $result->fetch_assoc();
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Edit Products</h2>
    < method="post" action="">
        <label>Name : </label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" required/><br>
        <label>Price : </label>
        <input type="text" name="price" value="<?= htmlspecialchars($product['price']); ?>" required/><br>
        <input type="submit" value="Update"/>
    </form>
    <br/>
    <a href="index.php">Back to Product List</a>
</body>
</html>