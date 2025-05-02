<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once '../config/database.php';
$stmt = $conn->query("SELECT bmi_records.*, users.username FROM bmi_records JOIN users ON bmi_records.user_id = users.id ORDER BY timestamp DESC");
$all_records = $stmt->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #000000;
            color: #FFD700;
            font-family: Arial, sans-serif;
        }
        .container {
            background-color: #1a1a1a;
            padding: 20px;
            border-radius: 10px;
            margin-top: 50px;
        }
        h1 {
            color: #FFD700;
            text-align: center;
        }
        .btn-primary, .btn-secondary {
            background-color: #FFD700;
            border-color: #FFD700;
            color: #000000;
        }
        .btn-primary:hover, .btn-secondary:hover {
            background-color: #e6c200;
            border-color: #e6c200;
        }
        .btn-danger, .btn-warning {
            color: #000000;
        }
        .table {
            color: #FFD700;
            background-color: #333333;
        }
        .table th, .table td {
            border: 1px solid #FFD700;
        }
        .navbar {
            background-color: #FFD700;
        }
        .navbar a {
            color: #000000;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="#">BMI Calculator</a>
        <div class="ml-auto">
            <a href="index.php" class="btn btn-secondary mr-2">Back to Calculator</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </nav>
    <div class="container">
        <h1>Admin Panel - All BMI Records</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Name</th>
                    <th>BMI</th>
                    <th>Status</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_records as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['username']); ?></td>
                        <td><?php echo htmlspecialchars($record['name']); ?></td>
                        <td><?php echo number_format($record['bmi'], 2); ?></td>
                        <td><?php echo htmlspecialchars($record['status']); ?></td>
                        <td><?php echo $record['timestamp']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>