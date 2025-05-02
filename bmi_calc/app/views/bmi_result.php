<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

require_once '../config/database.php';
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$role = $user['role'];
$stmt->close();

require_once '../models/BmiModel.php';
require_once '../controllers/BmiController.php';

$model = new BmiModel($conn);
$controller = new BmiController($model);

$result = ['success' => false, 'message' => 'No data submitted.', 'history' => $model->getBmiHistory($_SESSION['user_id'])];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['weight'], $_POST['height'])) {
    $name = htmlspecialchars($_POST['name']);
    $weight = floatval($_POST['weight']);
    $height = floatval($_POST['height']);
    $result = $controller->calculateBmi($_SESSION['user_id'], $name, $weight, $height);
}

$labels = [];
$values = [];
foreach ($result['history'] as $entry) {
    $labels[] = $entry['timestamp'];
    $values[] = $entry['bmi'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BMI Result</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        h1, h3 {
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
        .alert {
            color: #000000;
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
            <?php if ($role === 'admin'): ?>
                <a href="../public/admin.php" class="btn btn-warning mr-2">Admin Panel</a>
            <?php endif; ?>
            <a href="../public/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </nav>
    <div class="container">
        <h1>BMI Result</h1>
        <?php if ($result['success']): ?>
            <div class="alert alert-<?php echo $result['bmi'] < 18.5 ? 'info' : ($result['bmi'] < 25 ? 'success' : ($result['bmi'] < 30 ? 'warning' : 'danger')); ?>">
                <?php echo htmlspecialchars($result['message']); ?>
            </div>
            <p><strong>Health Tip:</strong> <?php echo htmlspecialchars($result['tip']); ?></p>
        <?php else: ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($result['message']); ?></div>
        <?php endif; ?>
        
        <div class="mt-3">
            <h3>Previous Calculations</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>BMI</th>
                        <th>Interpretation</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['history'] as $entry): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($entry['name']); ?></td>
                            <td><?php echo number_format($entry['bmi'], 2); ?></td>
                            <td><?php echo htmlspecialchars($entry['status']); ?></td>
                            <td><?php echo $entry['timestamp']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <h3>BMI History Chart</h3>
            <canvas id="bmiChart" width="400" height="200"></canvas>
        </div>
        
        <a href="bmi_form.php" class="btn btn-secondary mt-3">Back to Form</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        const ctx = document.getElementById('bmiChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'BMI Over Time',
                    data: <?php echo json_encode($values); ?>,
                    borderColor: '#FFD700',
                    backgroundColor: 'rgba(255, 215, 0, 0.2)',
                    borderWidth: 2
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
