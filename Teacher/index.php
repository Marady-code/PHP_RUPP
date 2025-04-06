<?php require_once 'config/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-chalkboard-teacher me-2"></i>Teacher Management
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="teachers/index.php">
                            <i class="fas fa-users me-1"></i>Teachers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="hours/index.php">
                            <i class="fas fa-clock me-1"></i>Teaching Hours
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="payments/index.php">
                            <i class="fas fa-money-bill-wave me-1"></i>Payments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="deleted/teachers.php">
                            <i class="fas fa-trash me-1"></i>Deleted Records
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-users fa-2x mb-3"></i><br>
                            Teachers
                        </h5>
                        <p class="card-text">Manage teacher information and records</p>
                        <a href="teachers/index.php" class="btn btn-light">Go to Teachers</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-clock fa-2x mb-3"></i><br>
                            Teaching Hours
                        </h5>
                        <p class="card-text">Record and track teaching hours</p>
                        <a href="hours/index.php" class="btn btn-light">Go to Hours</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-money-bill-wave fa-2x mb-3"></i><br>
                            Payments
                        </h5>
                        <p class="card-text">Manage teacher payments</p>
                        <a href="payments/index.php" class="btn btn-light">Go to Payments</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>