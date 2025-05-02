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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BMI Calculator</title>
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
        .btn-primary {
            background-color: #FFD700;
            border-color: #FFD700;
            color: #000000;
        }
        .btn-primary:hover {
            background-color: #e6c200;
            border-color: #e6c200;
        }
        .btn-danger, .btn-warning {
            color: #000000;
        }
        .form-control {
            background-color: #333333;
            color: #FFD700;
            border: 1px solid #FFD700;
        }
        .form-control::placeholder {
            color: #FFD700;
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
        <h1>BMI Calculator</h1>
        <form action="../public/index.php" method="post" class="mt-3">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="weight">Weight (kg):</label>
                <input type="number" id="weight" name="weight" class="form-control" step="0.1" required>
            </div>
            <div class="form-group">
                <label for="height">Height (m):</label>
                <input type="number" id="height" name="height" class="form-control" step="0.01" required>
            </div>
            <button type="submit" class="btn btn-primary">Calculate</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
