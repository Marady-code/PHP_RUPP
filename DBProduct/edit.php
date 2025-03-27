<?php
    include ('connectionDB.php');

    // Fix a typo in the isset check (was $GET, should be $_GET)
    if(!isset($_GET['id'])){
        header("Location: index.php"); 
        exit();
    }
    $id = intval($_GET["id"]);

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $name = $conn->real_escape_string($_POST["name"]);
        $price = $conn->real_escape_string($_POST["price"]);

        $sql = "UPDATE products SET name='$name', price='$price' WHERE id=$id";
        if($conn->query($sql) === TRUE){
            header("Location: index.php"); 
            exit();
        }else{
            $error = "Error: " . $conn->error;
        }
    }

    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);
    $product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        .edit-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }

        .edit-title {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }

        .edit-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background-color: #3498db;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        .form-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #2980b9;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #777;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #3498db;
        }

        .error-message {
            background-color: #e74c3c;
            color: white;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <?php if(isset($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <h2 class="edit-title">Edit Product</h2>
        
        <form method="post" action="">
            <div class="form-group">
                <label class="form-label" for="name">Name</label>
                <input 
                    type="text" 
                    id="name"
                    name="name" 
                    class="form-input" 
                    value="<?= htmlspecialchars($product['name']); ?>" 
                    required
                />
            </div>
            
            <div class="form-group">
                <label class="form-label" for="price">Price</label>
                <input 
                    type="text" 
                    id="price"
                    name="price" 
                    class="form-input" 
                    value="<?= htmlspecialchars($product['price']); ?>" 
                    required
                />
            </div>
            
            <button type="submit" class="submit-btn">Update Product</button>
        </form>
        
        <a href="index.php" class="back-link">Back to Product List</a>
    </div>
</body>
</html>