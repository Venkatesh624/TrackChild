<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Include database connection
include('../includes/db.php');

// Fetch existing missing child reports
$query = "SELECT * FROM children WHERE reported_by = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$reported_children = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Redirect if no report exists
if (!$reported_children) {
    header('Location: new_report.php'); 
    exit;
}

// Fetch notifications for user's reported cases
$child_ids = array_column($reported_children, 'child_id');
$placeholders = implode(',', array_fill(0, count($child_ids), '?'));

$notificationQuery = "SELECT * FROM notifications WHERE child_id IN ($placeholders)";
$notificationStmt = $conn->prepare($notificationQuery);
$notificationStmt->execute($child_ids);
$notifications = $notificationStmt->fetchAll(PDO::FETCH_ASSOC);

// Group notifications by child_id
$notificationsByChild = [];
foreach ($notifications as $notification) {
    $notificationsByChild[$notification['child_id']][] = $notification;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Existing Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">TrackChild</a>
        </div>
    </nav>
    <div class="container">
        <h2 class="mb-4 text-center">Your Existing Reports</h2>
        <?php foreach ($reported_children as $reported_child): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <p><strong>Child's Name:</strong> <?php echo htmlspecialchars($reported_child['name']); ?></p>
                    <p><strong>Child's Age:</strong> <?php echo htmlspecialchars($reported_child['age']); ?></p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($reported_child['gender']); ?></p>
                    <p><strong>Last Seen Location:</strong> <?php echo htmlspecialchars($reported_child['last_seen_location']); ?></p>
                    <p><strong>Last Seen Date:</strong> <?php echo htmlspecialchars($reported_child['last_seen_date']); ?></p>
                    <p><strong>Status:</strong> <span class="fw-bold <?php echo $reported_child['status'] === 'missing' ? 'text-danger' : 'text-success'; ?>"><?php echo htmlspecialchars($reported_child['status']); ?></span></p>
                    <h5 class="mt-3">Notifications</h5>
                    <?php if (isset($notificationsByChild[$reported_child['child_id']])): ?>
                        <ul class="list-group mb-3">
                            <?php foreach ($notificationsByChild[$reported_child['child_id']] as $notification): ?>
                                <li class="list-group-item">
                                    <?php echo htmlspecialchars($notification['notification_text']); ?>
                                    <span class="text-muted small ms-2"><?php echo htmlspecialchars($notification['created_at']); ?></span>
                                    <?php if (strpos($notification['notification_text'], 'found at') !== false): ?>
                                        <a href="view_location.php?child_id=<?php echo htmlspecialchars($reported_child['child_id']); ?>" class="btn btn-link btn-sm">View Location</a>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">No notifications yet for this case.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="text-center">
            <a href="new_report.php" class="btn btn-primary">Submit a New Report</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
