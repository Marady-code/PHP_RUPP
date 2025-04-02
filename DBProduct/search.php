<?php
    $dsn = 'mysql:host=localhost;dbname=dbsearchproduct;charset=utf8';
    $dbUser = 'root';
    $dbPass = 'Rupp155';

    try{
        $pdo = new PDO($dsn, $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }catch(PDOException $e){
        die("Database connection failed : ". $e->getMessage());
    }

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

    $sql = "SELECT * FROM products WHERE 1";
    $params = [];
    if(!empty($search)){
        $sql .= " AND (pname LIKE :search OR description LIKE :search)";
        $params[':search'] = '%' . $search .'%';
    }

    if(!empty($category)){
        $sql .= " AND category = :category";
        $params[':category'] = $category;
    }

    $sql .= " ORDER BY $sort $order";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <hr>

    <?php if ($products): ?>
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
            <?php foreach($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['pname']) ?></td>
                    <td><?= htmlspecialchars($product['price']) ?></td>
                    <td><?= htmlspecialchars($product['category']) ?></td>
                    <td><?= htmlspecialchars($product['description']) ?></td>
                    <td><?= htmlspecialchars($product['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
        <?php else : ?>
            <p>No Products found!</p>
        <?php endif; ?>
</body>
</html>