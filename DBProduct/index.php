<?php
    include('connectionDB.php');

    // Search functionality
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
    $order = isset($_GET['order']) ? strtoupper($_GET['order']) : 'ASC';
    
    // Allowed sort options
    $allowedSorts = ['id', 'name', 'price'];
    $allowedOrders = ['ASC', 'DESC'];
    if(!in_array($sort, $allowedSorts)) {
        $sort = 'id';
    }
    if(!in_array($order, $allowedOrders)) {
        $order = 'ASC';
    }

    // Pagination
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $page = ($page < 1) ? 1 : $page;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    // Build the query
    $sql = "SELECT * FROM products WHERE isActive = 1";
    
    if(!empty($search)){
        $search = $conn->real_escape_string($search);
        $sql .= " AND (name LIKE '%$search%' OR id LIKE '%$search%')";
    }

    $sql .= " ORDER BY $sort $order LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);

    // Function to build sort links
    function sortLink($column, $label, $currentSort, $currentOrder){
        $newOrder = 'ASC';
        if($column === $currentSort){
            $newOrder = ($currentOrder === 'ASC') ? 'DESC' : 'ASC';
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
    <title>Product List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
            color: #333;
        }

        h2 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .add-product {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
        }

        .add-product:hover {
            background-color: #2980b9;
            scale: 1.05;
            transition: 0.5s;
        }
        
        .add-product:hover:active {
            opacity: 0.7;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .search-form input[type="text"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 300px;
        }

        .search-form button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        th, td {
            text-align: left;
            padding: 12px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .action-links a {
            text-decoration: none;
            padding: 4px 8px;
            border-radius: 3px;
            margin-right: 5px;
        }

        .edit-link {
            background-color: rgb(32, 164, 87);
            color: white;
        }

        .delete-link {
            background-color: rgb(227, 12, 12);
            color: white;
        }

        .delete-link:hover {
            background-color: rgb(194, 7, 7);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }

        .pagination a, .pagination strong {
            text-decoration: none;
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #333;
        }

        .pagination strong {
            background-color: #3498db;
            color: white;
        }

        .pagination a:hover {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Product List</h2>
    
    <div class="action-buttons">
        <a href="create.php" class="add-product">Add New Product</a>
    </div>
    
    <!-- Search form -->
    <form method="get" action="" class="search-form">
        <input type="text" name="search" placeholder="Search products by name or ID..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
        
        <?php if(!empty($search)): ?>
            <a href="index.php">Clear</a>
        <?php endif; ?>
    </form>

    <?php if ($result && $result->num_rows > 0) : ?>
        <table>
            <tr>
                <th><?= sortLink('id', 'ID', $sort, $order) ?></th>
                <th><?= sortLink('name', 'Name', $sort, $order) ?></th>
                <th><?= sortLink('price', 'Price', $sort, $order) ?></th>
                <th>Action</th>
            </tr>
            <?php while ( $row = $result->fetch_assoc() ) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']); ?></td>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['price']); ?></td>
                    <td class="action-links">
                        <a href="edit.php?id=<?= $row['id']; ?>" class="edit-link">Edit</a>
                        <a href="delete.php?id=<?= $row['id']; ?>"
                            class="delete-link"
                            onclick="return confirm('Are you sure you want to delete this product?');">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else : ?>
        <p>No products found.</p>
    <?php endif; ?>

    <?php
        // Count total rows for pagination
        $countSql = "SELECT COUNT(*) AS total FROM products WHERE isActive = 1";
        
        if(!empty($search)){
            $countSql .= " AND (name LIKE '%$search%' OR id LIKE '%$search%')";
        }
        
        $countResult = $conn->query($countSql);
        $totalRows = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalRows / $limit);

        if($totalPages > 1){
            echo "<div class='pagination'>";
            
            // Keep search parameters in pagination links
            $params = $_GET;
            unset($params['page']);
            $queryString = http_build_query($params);
            $queryString = !empty($queryString) ? "&$queryString" : "";
            
            for($i=1; $i<= $totalPages; $i++){
                if($i == $page){
                    echo "<strong>$i</strong>";
                }else{
                    echo "<a href='index.php?page=$i$queryString'>$i</a>";
                }
            }
            echo "</div>";
        }
    ?>
</body>
</html>