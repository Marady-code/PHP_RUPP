<?php
    include('connectDB.php');

    // Search functionality for deleted patients
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Build the query - Only show inactive patients (isActive = 0)
    $sql = "SELECT *, 
            TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age 
            FROM patients WHERE isActive = 0";
    
    if(!empty($search)){
        $search = $conn->real_escape_string($search);
        $sql .= " AND (full_name LIKE '%$search%' OR patient_id LIKE '%$search%' OR contact_number LIKE '%$search%')";
    }

    $sql .= " ORDER BY patient_id DESC";
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleted Patients | Hospital Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .btn-restore {
            background-color: #28a745;
            color: white;
        }
        .btn-restore:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <header class="page-header">
            <h1><i class="fas fa-trash"></i> Deleted Patients</h1>
            <div class="header-actions">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Active Patients
                </a>
            </div>
        </header>

        <div class="content-card">
            <!-- Search form -->
            <div class="search-container">
                <form method="get" action="" class="search-form">
                    <div class="search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="search" placeholder="Search deleted patients..." 
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-search">Search</button>
                        <?php if(!empty($search)): ?>
                            <a href="deleted_patients.php" class="btn btn-clear">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <?php if ($result && $result->num_rows > 0) : ?>
                <div class="table-responsive">
                    <table class="patient-table">
                        <thead>
                            <tr>
                                <th>Patient ID</th>
                                <th>Full Name</th>
                                <th>Age</th>
                                <th>Contact</th>
                                <th>Admission Date</th>
                                <th>Discharge Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                                <tr>
                                    <td class="patient-id">#<?= htmlspecialchars($row['patient_id']); ?></td>
                                    <td class="patient-name">
                                        <strong><?= htmlspecialchars($row['full_name']); ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($row['age']); ?> yrs</td>
                                    <td><?= htmlspecialchars($row['contact_number']); ?></td>
                                    <td><?= date('M d, Y', strtotime($row['admission_date'])); ?></td>
                                    <td><?= $row['discharge_date'] ? date('M d, Y', strtotime($row['discharge_date'])) : 'N/A'; ?></td>
                                    <td class="actions">
                                        <a href="restore.php?id=<?= $row['patient_id']; ?>" 
                                           class="btn-action btn-restore"
                                           title="Restore"
                                           onclick="return confirm('Restore this patient record?');">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle empty-icon"></i>
                    <h3>No Deleted Patient Records</h3>
                    <p>All patients are currently active</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>