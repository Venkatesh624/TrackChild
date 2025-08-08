<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    die('Error: User is not logged in.');
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare("INSERT INTO children (name, age, gender, last_seen_location, last_seen_date, reported_by, status) 
                                VALUES (:name, :age, :gender, :location, :date, :reported_by, 'missing')");
        $stmt->execute([
            ':name' => $_POST['child_name'],
            ':age' => $_POST['child_age'],
            ':gender' => $_POST['child_gender'],
            ':location' => $_POST['last_seen_location'],
            ':date' => $_POST['last_seen_date'],
            ':reported_by' => $_SESSION['user_id'] // Ensure this is set
        ]);

        $child_id = $conn->lastInsertId();

        $stmt = $conn->prepare("INSERT INTO notifications (child_id, notification_text, user_id) 
                                VALUES (:child_id, :text, :user_id)");
        $stmt->execute([
            ':child_id' => $child_id,
            ':text' => 'New missing child case reported',
            ':user_id' => $_SESSION['user_id']
        ]);

        $conn->commit();
        header('Location: existing_report.php?success=1');
        exit;

    } catch (PDOException $e) {
        $conn->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Missing Child</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">TrackChild</a>
        </div>
    </nav>
    <div class="container">
        <h2 class="mb-4">Report a Missing Child</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="" class="card p-4 shadow-sm bg-white">
            <div class="mb-3">
                <label for="child_name" class="form-label">Child's Name:</label>
                <input type="text" name="child_name" id="child_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="child_age" class="form-label">Child's Age:</label>
                <input type="number" name="child_age" id="child_age" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="child_gender" class="form-label">Child's Gender:</label>
                <select name="child_gender" id="child_gender" class="form-select" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="last_seen_location" class="form-label">Last Seen Location:</label>
                <input type="text" name="last_seen_location" id="last_seen_location" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="last_seen_date" class="form-label">Last Seen Date:</label>
                <input type="date" name="last_seen_date" id="last_seen_date" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Report</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
