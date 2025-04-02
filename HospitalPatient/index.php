<?php
    include('connectDB.php');

    // Search functionality
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'patient_id';
    $order = isset($_GET['order']) ? strtoupper($_GET['order']) : 'ASC';
    
    // Allowed sort options
    $allowedSorts = ['patient_id', 'full_name', 'admission_date', 'blood_type'];
    $allowedOrders = ['ASC', 'DESC'];
    if(!in_array($sort, $allowedSorts)) {
        $sort = 'patient_id';
    }
    if(!in_array($order, $allowedOrders)) {
        $order = 'ASC';
    }

    // Pagination
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $page = ($page < 1) ? 1 : $page;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    // Build the query - Only show active patients (isActive = 1)
    $sql = "SELECT *, 
            TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age 
            FROM patients WHERE isActive = 1";
    
    if(!empty($search)){
        $search = $conn->real_escape_string($search);
        $sql .= " AND (full_name LIKE '%$search%' OR patient_id LIKE '%$search%' OR contact_number LIKE '%$search%')";
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
        $arrow = $column === $currentSort ? ($currentOrder === 'ASC' ? '↑' : '↓') : '';
        return "<a href=\"?{$query}\" class=\"sort-link\">{$label} {$arrow}</a>";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Records | Hospital Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style.css?v=1.1">
</head>
<body>
    <div class="main-container">
        <header class="page-header">
            <h1><i class="fas fa-hospital-user"></i> Patient Records</h1>
            <div class="header-actions">
                <a href="create.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Patient
                </a>
                <a href="deleted_patients.php" class="btn btn-warning">
                    <i class="fas fa-trash"></i> View Deleted Patients
                </a>
            </div>
        </header>

        <div class="content-card">
            <!-- Search form -->
            <div class="search-container">
                <form method="get" action="" class="search-form">
                    <div class="search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="search" placeholder="Search patients by name, ID or phone..." 
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-search">Search</button>
                        <?php if(!empty($search)): ?>
                            <a href="index.php" class="btn btn-clear">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <?php if ($result && $result->num_rows > 0) : ?>
                <div class="table-responsive">
                    <table class="patient-table">
                        <thead>
                            <tr>
                                <th><?= sortLink('patient_id', 'Patient ID', $sort, $order) ?></th>
                                <th><?= sortLink('full_name', 'Full Name', $sort, $order) ?></th>
                                <th>Age</th>
                                <th>Contact</th>
                                <th>Blood Type</th>
                                <th><?= sortLink('admission_date', 'Admission', $sort, $order) ?></th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                                <tr>
                                    <td class="patient-id">#<?= htmlspecialchars($row['patient_id']); ?></td>
                                    <td class="patient-name">
                                        <strong><?= htmlspecialchars($row['full_name']); ?></strong>
                                        <small><?= htmlspecialchars($row['email']); ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($row['age']); ?> yrs</td>
                                    <td><?= htmlspecialchars($row['contact_number']); ?></td>
                                    <td>
                                        <span class="blood-type <?= strtolower(str_replace('+', 'p', $row['blood_type'])) ?>">
                                            <?= htmlspecialchars($row['blood_type'] ? $row['blood_type'] : '--'); ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($row['admission_date'])); ?></td>
                                    <td>
                                        <span class="status-badge <?= $row['discharge_date'] ? 'discharged' : 'admitted' ?>">
                                            <?= $row['discharge_date'] ? 'Discharged' : 'Admitted' ?>
                                            <?php if($row['discharge_date']): ?>
                                                <small><?= date('M d, Y', strtotime($row['discharge_date'])) ?></small>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="edit.php?id=<?= $row['patient_id']; ?>" class="btn-action btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete.php?id=<?= $row['patient_id']; ?>"
                                            class="btn-action btn-delete"
                                            title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this patient record?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                        <a href="#" class="btn-action btn-view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <div class="empty-state">
                    <i class="fas fa-user-injured empty-icon"></i>
                    <h3>No Patient Records Found</h3>
                    <p>Try adjusting your search or add a new patient</p>
                    <a href="create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Patient
                    </a>
                </div>
            <?php endif; ?>

            <?php
                // Count total rows for pagination - Only active patients
                $countSql = "SELECT COUNT(*) AS total FROM patients WHERE isActive = 1";
                
                if(!empty($search)){
                    $countSql .= " AND (full_name LIKE '%$search%' OR patient_id LIKE '%$search%' OR contact_number LIKE '%$search%')";
                }
                
                $countResult = $conn->query($countSql);
                $totalRows = $countResult->fetch_assoc()['total'];
                $totalPages = ceil($totalRows / $limit);

                if($totalPages > 1){
                    echo "<div class='pagination-container'>";
                    echo "<div class='pagination'>";
                    
                    // Previous button
                    if($page > 1){
                        $prevPage = $page - 1;
                        $params = $_GET;
                        $params['page'] = $prevPage;
                        $queryString = http_build_query($params);
                        echo "<a href='?$queryString' class='page-nav'><i class='fas fa-chevron-left'></i></a>";
                    }
                    
                    // Keep search parameters in pagination links
                    $params = $_GET;
                    unset($params['page']);
                    $queryString = http_build_query($params);
                    $queryString = !empty($queryString) ? "&$queryString" : "";
                    
                    // Page numbers
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    
                    if($startPage > 1){
                        echo "<a href='?page=1$queryString'>1</a>";
                        if($startPage > 2) echo "<span class='page-dots'>...</span>";
                    }
                    
                    for($i = $startPage; $i <= $endPage; $i++){
                        if($i == $page){
                            echo "<span class='page-current'>$i</span>";
                        }else{
                            echo "<a href='?page=$i$queryString'>$i</a>";
                        }
                    }
                    
                    if($endPage < $totalPages){
                        if($endPage < $totalPages - 1) echo "<span class='page-dots'>...</span>";
                        echo "<a href='?page=$totalPages$queryString'>$totalPages</a>";
                    }
                    
                    // Next button
                    if($page < $totalPages){
                        $nextPage = $page + 1;
                        $params = $_GET;
                        $params['page'] = $nextPage;
                        $queryString = http_build_query($params);
                        echo "<a href='?$queryString' class='page-nav'><i class='fas fa-chevron-right'></i></a>";
                    }
                    
                    echo "</div>";
                    echo "</div>";
                }
            ?>
        </div>
    </div>
</body>
</html>