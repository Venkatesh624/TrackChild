<?php
// File: view_location.php
include('../includes/db.php');

// Fetch child_id from the query string
$child_id = isset($_GET['child_id']) ? intval($_GET['child_id']) : null;

if ($child_id) {
    // Fetch location details for the given child_id
    $query = "SELECT l.location_name, l.address, l.city, l.state, l.country, l.latitude, l.longitude
              FROM locations l
              WHERE l.child_id = :child_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':child_id', $child_id, PDO::PARAM_INT);
    $stmt->execute();
    $location = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$location) {
        die("Location details not found for this child.");
    }
} else {
    die("Invalid child ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">TrackChild</a>
        </div>
    </nav>
    <div class="container">
        <h1 class="mb-4 text-center">Location Details</h1>
        <div class="card shadow-sm mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <p><strong>Location Name:</strong> <?php echo htmlspecialchars($location['location_name']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($location['address']); ?></p>
                <p><strong>City:</strong> <?php echo htmlspecialchars($location['city']); ?></p>
                <p><strong>State:</strong> <?php echo htmlspecialchars($location['state']); ?></p>
                <p><strong>Country:</strong> <?php echo htmlspecialchars($location['country']); ?></p>
                <p><strong>Coordinates:</strong> <?php echo htmlspecialchars($location['latitude']) . ", " . htmlspecialchars($location['longitude']); ?></p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

