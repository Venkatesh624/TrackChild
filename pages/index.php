<?php
// Include your database connection (adjust the path accordingly)
include('../includes/db.php');

// Start session for login tracking
session_start();

// Function to redirect with a message
function redirect($url, $message = null) {
    if ($message) {
        $_SESSION['message'] = $message;
    }
    header("Location: $url");
    exit;
}

// Handle login or registration request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];  // Can be 'user' or 'agency'
    $action = $_POST['action'];  // Can be 'login' or 'register'

    // Basic validation
    if (empty($username) || empty($password) || empty($role) || empty($action)) {
        redirect('login.php', 'Please fill in all fields.');
    }

    // If action is 'login', handle login process
    if ($action == 'login') {
        $table = $role === 'user' ? 'users' : 'agencies';
        $query = "SELECT * FROM $table WHERE username = :username";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        if ($stmt->execute()) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;
                $_SESSION['user_id'] = $user['user_id'];

                if ($role === 'user') {
                    $reportQuery = "SELECT * FROM children WHERE reported_by = :user_id AND status = 'missing'";
                    $reportStmt = $conn->prepare($reportQuery);
                    $reportStmt->bindParam(':user_id', $user['user_id'], PDO::PARAM_INT);
                    $reportStmt->execute();
                    $reported_child = $reportStmt->fetch(PDO::FETCH_ASSOC);

                    if ($reported_child) {
                        redirect('existing_report.php');
                    } else {
                        redirect('new_report.php');
                    }
                } else {
                    redirect('admin.php');
                }
            } else {
                redirect('login.php', 'Invalid credentials.');
            }
        } else {
            redirect('login.php', 'Error with query execution.');
        }
    }

    // If action is 'register', handle user registration process
    if ($action == 'register') {
        $table = $role === 'user' ? 'users' : 'agencies';
        $query = "SELECT * FROM $table WHERE username = :username";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            redirect('login.php', 'Username already taken. Please choose another.');
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $insertQuery = "INSERT INTO $table (username, password) VALUES (:username, :password)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bindParam(':username', $username);
            $insertStmt->bindParam(':password', $hashedPassword);
            if ($insertStmt->execute()) {
                redirect('login.php', 'Registration successful. You can now log in.');
            } else {
                redirect('login.php', 'Error during registration.');
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - TrackChild</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f4f9 url('https://childdevelopmentinfo.com/wp-content/uploads/2011/09/Untitled-design-2-mini.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .brand-gradient {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            color: #fff;
        }
        .track-child-image {
            width: 70%;
            border-radius: 8px;
            margin: 1.5rem auto 0 auto;
            display: block;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg brand-gradient mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">TrackChild</a>
        </div>
    </nav>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-80">
            <div class="col-lg-5 mb-4">
                <div class="card brand-gradient text-center h-100 p-4">
                    <h1 class="display-5 mb-3">Welcome to TrackChild</h1>
                    <p class="lead">A National Tracking System for Missing and Vulnerable Children.</p>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRgTuNB6FBDcJbByydSmdbbpLM85RI8PvhENg&s" alt="Track Child" class="track-child-image">
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card p-4">
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <h2 class="mb-3 text-center">Login</h2>
                    <form action="" method="POST" class="mb-4">
                        <input type="hidden" name="action" value="login">
                        <div class="mb-3">
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="mb-3">
                            <select name="role" class="form-select" required>
                                <option value="user">User (Report Child)</option>
                                <option value="agency">Agency</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <hr>
                    <h2 class="mb-3 text-center">Register</h2>
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="register">
                        <div class="mb-3">
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="mb-3">
                            <select name="role" class="form-select" required>
                                <option value="user">User (Report Child)</option>
                                <option value="agency">Agency</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

