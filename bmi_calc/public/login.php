<?php
session_start();
require_once '../config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
            max-width: 400px;
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
        .form-control {
            background-color: #333333;
            color: #FFD700;
            border: 1px solid #FFD700;
        }
        .form-control::placeholder {
            color: #FFD700;
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
            <a href="register.php" class="btn btn-primary">Register</a>
        </div>
    </nav>
    <div class="container">
        <h1>Login</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="login.php" method="post" class="mt-3">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <p class="mt-3 text-center">Don't have an account? <a href="register.php" style="color: #FFD700;">Register</a></p>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>