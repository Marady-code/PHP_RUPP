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
    <title>Document</title>
</head>
<body>
    <h2>Product List</h2>
    <a href="create.php">Add New Product</a>
    <Br></Br>
    <?php if ($result->num_rows > 0) : ?>
        <table border="1" cellpadding="10">
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
                    <td><a href="edit.php?id=<?= $row['id']; ?>">Edit</a> |
                        <a href="delete.php?id=<?= $row['id']; ?>"
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
            echo "<br/><div>Pages : ";
            for($i=1 ; $i<= $totalPages; $i++){
                if($i == $page){
                    echo "<strong>$i</strong>";
                }else{
                    echo "<a href = 'index.php?page=$i'>$i</a>";
                }
            }
            echo "</div>";
        }
    ?>
</body>
</html>