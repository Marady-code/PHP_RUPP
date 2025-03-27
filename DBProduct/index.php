<?php
    include('connectionDB.php');

    $page = isset($_GET['page']) ? intval($_GET['page']) :1;
    $page = ($page == 1) ?1: $page;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $sql = "SELECT * FROM products LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);
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
            max-width: 800px;
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

        .add-product {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .add-product:hover {
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
            background-color: #2ecc71;
            color: white;
        }

        .delete-link {
            background-color: #e74c3c;
            color: white;
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
    <a href="create.php" class="add-product">Add New Product</a>

    <?php if ($result->num_rows > 0) : ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
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
                        onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
        </table>
    <?php else : ?>
        <p>No products found.</p>
    <?php endif; ?>

    <?php
        $sqlCount = "SELECT COUNT(*) AS total FROM products";
        $countResult = $conn->query($sqlCount);
        $totalRows = $countResult->fetch_assoc() ['total'];
        $totalPages = ceil($totalRows / $limit);

        if($totalPages > 1){
            echo "<div class='pagination'>";
            for($i=1 ; $i<= $totalPages; $i++){
                if($i == $page){
                    echo "<strong>$i</strong>";
                }else{
                    echo "<a href='index.php?page=$i'>$i</a>";
                }
            }
            echo "</div>";
        }
    ?>
</body>
</html>