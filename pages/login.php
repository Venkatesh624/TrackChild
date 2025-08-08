<?php
include('../includes/db.php');
session_start();

// Function to redirect with a message
function redirect($url, $message = null) {
    if ($message) {
        $_SESSION['message'] = $message;
    }
    header("Location: $url");
    exit;
}

// Handle request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role']; // 'user' or 'agency'
    $action = $_POST['action']; // 'login' or 'register'

    // Basic validation
    if (empty($username) || empty($password) || empty($role) || empty($action)) {
        redirect('login.php', 'All fields are required.');
    }

    $table = ($role === 'user') ? 'users' : 'agencies';

    try {
        if ($action === 'login') {
            // Login logic
            $query = "SELECT * FROM $table WHERE username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;

                $redirectPage = ($role === 'user') 
                    ? 'new_report.php' 
                    : 'admin.php';

                redirect($redirectPage);
            } else {
                redirect('login.php', 'Invalid username or password.');
            }
        } elseif ($action === 'register') {
            // Registration logic
            $query = "SELECT * FROM $table WHERE username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->fetch()) {
                redirect('login.php', 'Username already exists. Choose a different one.');
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $insertQuery = "INSERT INTO $table (username, password) VALUES (:username, :password)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);

            if ($stmt->execute()) {
                redirect('login.php', 'Registration successful. Please log in.');
            } else {
                redirect('login.php', 'Error during registration. Try again.');
            }
        }
    } catch (PDOException $e) {
        redirect('login.php', 'An error occurred: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register - TrackChild</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">TrackChild</a>
        </div>
    </nav>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-80">
            <div class="col-lg-6">
                <div class="card p-4 shadow-sm">
                    <h1 class="mb-4 text-center">TrackChild Login/Register</h1>
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <form action="login.php" method="POST" class="mb-4">
                        <h2 class="mb-3 text-center">Login</h2>
                        <div class="mb-3">
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="mb-3">
                            <select name="role" class="form-select" required>
                                <option value="user">User</option>
                                <option value="agency">Agency</option>
                            </select>
                        </div>
                        <input type="hidden" name="action" value="login">
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <hr>
                    <form action="login.php" method="POST">
                        <h2 class="mb-3 text-center">Register</h2>
                        <div class="mb-3">
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="mb-3">
                            <select name="role" class="form-select" required>
                                <option value="user">User</option>
                                <option value="agency">Agency</option>
                            </select>
                        </div>
                        <input type="hidden" name="action" value="register">
                        <button type="submit" class="btn btn-success w-100">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
