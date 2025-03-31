<?php
    include('connectionDB.php');

    $search = isset($_GET['search']) ?
        trim($_GET['search']) : '';
    $category = isset($_GET['category']) ?
        trim($_GET['category']) : '';
    $sort = isset($_GET['sort']) ?
        $_GET['sort']: 'pname';
    $order = isset($_GET['order']) ?
        strtoupper($_GET['order']) : 'ASC';
    
    // Allowed sort
    $allowedSorts = ['pname', 'price', 'created_at'];
    $allowedOrders = ['ASC', 'DESC'];
    if(!in_array($sort, $allowedSorts)) {
        $sort = 'pname';
    }
    if(!in_array($order, $allowedOrders)) {
        $order = 'ASC';
    }

    // Build query
    $sql = "SELECT * FROM products WHERE 1";
    
    if(!empty($search)){
        $search = $conn->real_escape_string($search);
        $sql .= " AND (pname LIKE '%$search%' OR description LIKE '%$search%')";
    }

    if(!empty($category)){
        $category = $conn->real_escape_string($category);
        $sql .= " AND category = '$category'";
    }

    $sql .= " ORDER BY $sort $order";
    $result = $conn->query($sql);
    
    function sortLink($column, $label, $currentSort, $currentOrder){
        $newOrder = 'ASC';
        if($column === $currentSort){
            $newOrder = ($currentOrder === 'ASC') ?
                 'DESC' : 'ASC';
        }

        $params = $_GET;
        $params['sort'] = $column;
        $params['order'] = $newOrder;
        $query = http_build_query($params);
        return "<a href=\"?{$query}\">{$label}</a>";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Search, Filter & Sort</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: block;
            padding: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f2f2f2;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        label {
            font-weight: 500;
            display: inline-block;
            margin-bottom: 5px;
        }
        
        input[type="text"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        input[type="submit"] {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        input[type="submit"]:hover {
            background-color: #2980b9;
        }
        
        a {
            color: #3498db;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Search Products</h1>
    <form method="get" action="search.php">
        <label>Search:</label>
        <input type="text" name="search" id="search" value="<?= htmlspecialchars($search) ?>">
        <br><br>

        <label>Category : </label>
        <select name="category">
            <option value="">-- All categories --</option>
            <option value="new" <?= ($category === 'new') ? 'selected' : '' ?>>New</option>
            <option value="secondhand" <?= ($category === 'secondhand') ? 'selected' : '' ?>>Second Hand</option>
        </select>
        <br><br>

        <input type="submit" value="Search">
    </form>
    
    <a href="index.php" class="back-link">Back to Product List</a>
    
    <hr>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th><?= sortLink('pname', 'Name', $sort, $order) ?></th>
                    <th><?= sortLink('price', 'Price', $sort, $order) ?></th>
                    <th>Category</th>
                    <th>Description</th>
                    <th><?= sortLink('created_at', 'Date Added', $sort, $order) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php while($product = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['pname']) ?></td>
                        <td><?= htmlspecialchars($product['price']) ?></td>
                        <td><?= htmlspecialchars($product['category']) ?></td>
                        <td><?= htmlspecialchars($product['description']) ?></td>
                        <td><?= htmlspecialchars($product['created_at']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No Products found!</p>
    <?php endif; ?>
</body>
</html>