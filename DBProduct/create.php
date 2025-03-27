<?php
    include('connectionDB.php');

    if($_SERVER["REQUEST_METHOD"] == "POST"){
      // Sanitize and validate name
      $name = trim($conn->real_escape_string($_POST["name"]));
      
      // Remove any non-numeric characters except decimal point
      $price = preg_replace("/[^0-9.]/", "", $_POST["price"]);
      
      // Validate price is a valid number
      if (!is_numeric($price)) {
          $error = "Invalid price. Please enter a valid number.";
      } else {
          // Format price to two decimal places
          $price = number_format((float)$price, 2, '.', '');

          $sql = "INSERT INTO products (name, price) VALUES ('$name', '$price')";
          if($conn->query($sql) === TRUE){
              header("Location: index.php");
              exit();
          }else{
              $error = "Error: " . $conn->error;
          }
      }
  }
?>

<!-- <!DOCTYPE html>
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
      <input type="text" name="name" required /> <br />
      <label>Price : </label>
      <input type="text" name="price" required /> <br />
      <input type="submit" value="submit" />
    </form>
    <br />
    <a href=""></a>
  </body>
</html> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add New Product</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2 class="form-title">Add New Product</h2>
        
        <?php if(isset($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label class="form-label" for="name">Product Name</label>
                <input 
                    type="text" 
                    id="name"
                    name="name" 
                    class="form-input" 
                    required 
                    placeholder="Enter product name"
                    value="<?= isset($name) ? htmlspecialchars($name) : '' ?>"
                />
            </div>
            
            <div class="form-group">
                <label class="form-label" for="price">Price</label>
                <input 
                    type="text" 
                    id="price"
                    name="price" 
                    class="form-input" 
                    required 
                    placeholder="Enter product price"
                    value="<?= isset($price) ? htmlspecialchars($price) : '' ?>"
                />
            </div>
            
            <button type="submit" class="submit-btn">Add Product</button>
        </form>
        
        <a href="index.php" class="back-link">Back to Product List</a>
    </div>
</body>
</html>
