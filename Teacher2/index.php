<?php
require_once 'config.php';
require_once 'functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_teacher'])) {
        addTeacher($pdo, $_POST);
    } elseif (isset($_POST['update_rate'])) {
        updateHourlyRate($pdo, $_POST['teacher_id'], $_POST['hourly_rate']);
    } elseif (isset($_POST['add_hours'])) {
        addTeachingHours($pdo, $_POST);
    } elseif (isset($_POST['delete_teacher'])) {
        deleteTeacher($pdo, $_POST['teacher_id']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #f72585;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        h1, h2, h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        h1 {
            font-size: 2.5rem;
            text-align: center;
            margin: 2rem 0;
            color: var(--secondary-color);
            position: relative;
        }

        h1::after {
            content: '';
            display: block;
            width: 100px;
            height: 4px;
            background: var(--accent-color);
            margin: 0.5rem auto;
            border-radius: 2px;
        }

        h2 {
            font-size: 1.8rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #eee;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        h2 i {
            margin-right: 10px;
            color: var(--accent-color);
        }

        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-color);
        }

        input[type="text"],
        input[type="date"],
        input[type="number"],
        input[type="email"],
        input[type="password"],
        select,
        textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-family: inherit;
            font-size: 1rem;
            transition: var(--transition);
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        button,
        .btn {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-family: inherit;
            font-weight: 500;
            transition: var(--transition);
            text-align: center;
            text-decoration: none;
        }

        button:hover,
        .btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        button:active,
        .btn:active {
            transform: translateY(0);
        }

        .btn-danger {
            background: var(--danger-color);
        }

        .btn-danger:hover {
            background: #d1145a;
        }

        .btn-success {
            background: var(--success-color);
        }

        .btn-success:hover {
            background: #3aa8d8;
        }

        .btn-warning {
            background: var(--warning-color);
        }

        .btn-warning:hover {
            background: #e07e0c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
            box-shadow: var(--box-shadow);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: var(--primary-color);
            color: white;
            font-weight: 500;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #f1f3f5;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 0.5rem;
        }

        .alert {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        .alert-danger {
            background: #fee2e2;
            color: #b91c1c;
        }

        .alert i {
            margin-right: 0.5rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .hidden {
            display: none;
        }

        .flex {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }
            
            .flex {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Teacher Management System</h1>
        
        <div class="grid">
            <div class="card">
                <h2><i class="fas fa-user-plus"></i> Add New Teacher</h2>
                <form method="post">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date:</label>
                        <input type="date" id="start_date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="hourly_rate">Hourly Rate ($):</label>
                        <input type="number" step="0.01" id="hourly_rate" name="hourly_rate" value="10.00" required>
                    </div>
                    <button type="submit" name="add_teacher" class="btn-success">
                        <i class="fas fa-save"></i> Add Teacher
                    </button>
                </form>
            </div>
            
            <div class="card">
                <h2><i class="fas fa-money-bill-wave"></i> Update Hourly Rate</h2>
                <form method="post">
                    <div class="form-group">
                        <label for="teacher_id_rate">Teacher:</label>
                        <select id="teacher_id_rate" name="teacher_id" required>
                            <?php echo getTeacherOptions($pdo); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="hourly_rate_update">New Hourly Rate ($):</label>
                        <input type="number" step="0.01" id="hourly_rate_update" name="hourly_rate" required>
                    </div>
                    <button type="submit" name="update_rate" class="btn-warning">
                        <i class="fas fa-sync-alt"></i> Update Rate
                    </button>
                </form>
            </div>
        </div>
        
        <div class="grid">
            <div class="card">
                <h2><i class="fas fa-clock"></i> Add Teaching Hours</h2>
                <form method="post">
                    <div class="form-group">
                        <label for="teacher_id_hours">Teacher:</label>
                        <select id="teacher_id_hours" name="teacher_id" required>
                            <?php echo getTeacherOptions($pdo); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date_taught">Date:</label>
                        <input type="date" id="date_taught" name="date_taught" required>
                    </div>
                    <div class="form-group">
                        <label for="hours">Hours:</label>
                        <input type="number" step="0.01" id="hours" name="hours" required>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_substitute" name="is_substitute" value="1">
                        <label for="is_substitute" style="display: inline; width: auto;">Is Substitute?</label>
                    </div>
                    <div class="form-group" id="substitute_for_group" style="display:none;">
                        <label for="substitute_for_id">Substitute For:</label>
                        <select id="substitute_for_id" name="substitute_for_id">
                            <?php echo getTeacherOptions($pdo); ?>
                        </select>
                    </div>
                    <button type="submit" name="add_hours" class="btn">
                        <i class="fas fa-plus-circle"></i> Add Hours
                    </button>
                </form>
            </div>
            
            <div class="card">
                <h2><i class="fas fa-user-minus"></i> Delete Teacher</h2>
                <form method="post" onsubmit="return confirm('Are you sure you want to delete this teacher?');">
                    <div class="form-group">
                        <label for="teacher_id_delete">Teacher:</label>
                        <select id="teacher_id_delete" name="teacher_id" required>
                            <?php echo getTeacherOptions($pdo); ?>
                        </select>
                    </div>
                    <button type="submit" name="delete_teacher" class="btn-danger">
                        <i class="fas fa-trash-alt"></i> Delete Teacher
                    </button>
                </form>
            </div>
        </div>
        
        <div class="card">
            <h2><i class="fas fa-file-invoice-dollar"></i> Monthly Report</h2>
            <?php echo generateMonthlyReport($pdo); ?>
        </div>
        
        <div class="card">
            <h2><i class="fas fa-trash-restore"></i> Deleted Records</h2>
            <?php echo showDeletedRecords($pdo); ?>
        </div>
    </div>

    <script>
        document.getElementById('is_substitute').addEventListener('change', function() {
            document.getElementById('substitute_for_group').style.display = this.checked ? 'block' : 'none';
        });

        // Add smooth scrolling to all links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add confirmation for delete actions
        document.querySelectorAll('form[onsubmit]').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to perform this action?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>