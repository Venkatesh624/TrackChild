<?php
// File: admin.php
include('../includes/db.php');

// Fetch all children with their status details from the database
$query = "SELECT child_id, name AS child_name, age, gender, last_seen_location, last_seen_date, status 
          FROM children"; // Assuming the 'status' column exists in the 'children' table

$children = $conn->query($query);

// Check if the success message should be displayed
$successMessage = "";
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMessage = "Notification sent successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Report Missing Child</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">TrackChild Admin</a>
        </div>
    </nav>
    <div class="container">
        <h1 class="mb-4">Admin Dashboard</h1>
        <?php if (!empty($successMessage)) { ?>
            <div class="alert alert-success text-center">
                <?php echo $successMessage; ?>
            </div>
        <?php } ?>
        <h2 class="mb-3">Children Details</h2>
        <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white">
            <thead class="table-success">
                <tr>
                    <th>Child Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Last Seen Location</th>
                    <th>Last Seen Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $children->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['child_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['age']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_seen_location']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_seen_date']); ?></td>
                        <td class="fw-bold <?php echo $row['status'] === 'missing' ? 'text-danger' : 'text-success'; ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </td>
                        <td><a href="view_details.php?id=<?php echo $row['child_id']; ?>" class="btn btn-outline-primary btn-sm">View Details</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
