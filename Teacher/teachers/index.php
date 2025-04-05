<?php
require_once '../../config/db_connect.php';

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT * FROM teachers WHERE isActive = 1";
if(!empty($search)) {
    $sql .= " AND (full_name LIKE :search OR email LIKE :search OR phone LIKE :search)";
}
$sql .= " ORDER BY full_name ASC";

$stmt = $conn->prepare($sql);
if(!empty($search)) {
    $searchParam = "%$search%";
    $stmt->bindParam(':search', $searchParam);
}
$stmt->execute();
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../config/db_connect.php'; ?>
    <div class="container mt-4">
        <h1 class="mb-4"><i class="fas fa-chalkboard-teacher"></i> Teacher Management</h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <?php flashMessage(); ?>
                <div class="d-flex justify-content-between align-items-center">
                    <form method="get" class="w-50">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search teachers..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            <?php if(!empty($search)): ?>
                                <a href="index.php" class="btn btn-outline-secondary">Clear</a>
                            <?php endif; ?>
                        </div>
                    </form>
                    <a href="create.php" class="btn btn-success"><i class="fas fa-plus"></i> Add Teacher</a>
                </div>
            </div>
        </div>

        <?php if(count($teachers) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Hourly Rate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($teachers as $teacher): ?>
                            <tr>
                                <td><?= htmlspecialchars($teacher['teacher_id']) ?></td>
                                <td><?= htmlspecialchars($teacher['full_name']) ?></td>
                                <td>
                                    <?= htmlspecialchars($teacher['email']) ?><br>
                                    <?= htmlspecialchars($teacher['phone']) ?>
                                </td>
                                <td>$<?= number_format($teacher['hourly_rate'], 2) ?></td>
                                <td>
                                    <a href="view.php?id=<?= $teacher['teacher_id'] ?>" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="edit.php?id=<?= $teacher['teacher_id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="delete.php?id=<?= $teacher['teacher_id'] ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this teacher?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No teachers found. <a href="create.php" class="alert-link">Add a new teacher</a>.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>