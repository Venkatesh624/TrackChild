<?php
// File: view_details.php
include('../includes/db.php');

// Fetch the child and guardian details based on the child ID from the query string
if (isset($_GET['id'])) {
    $child_id = $_GET['id'];

    $query = "SELECT c.child_id, c.name AS child_name, c.age, c.gender, c.last_seen_location, c.last_seen_date, c.status,
                     g.guardian_name, g.contact_info, g.relationship 
              FROM children c
              LEFT JOIN guardians g ON c.child_id = g.child_id
              WHERE c.child_id = :child_id";  // Use c.child_id instead of c.id

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':child_id', $child_id, PDO::PARAM_INT);
    $stmt->execute();
    $child = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$child) {
        die("Child not found.");
    }
}

// Check if the notification was sent successfully
$successMessage = "";
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMessage = "Location and notification sent successfully!";
}

// Handle the location form submission (if the form is submitted)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['location_name'])) {
    $location_name = $_POST['location_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $country = $_POST['country'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Insert location into the database
    $insertQuery = "INSERT INTO locations (child_id, location_name, address, city, state, country, latitude, longitude)
                    VALUES (:child_id, :location_name, :address, :city, :state, :country, :latitude, :longitude)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bindParam(':child_id', $child_id);
    $stmt->bindParam(':location_name', $location_name);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':state', $state);
    $stmt->bindParam(':country', $country);
    $stmt->bindParam(':latitude', $latitude);
    $stmt->bindParam(':longitude', $longitude);

    if ($stmt->execute()) {
        // Send notification to the user
        $notificationText = "Child found at $location_name, $address, $city, $state, $country.";
        $notificationQuery = "INSERT INTO notifications (child_id, notification_text) 
                              VALUES (:child_id, :notification_text)";
        $stmt = $conn->prepare($notificationQuery);
        $stmt->bindParam(':child_id', $child_id);
        $stmt->bindParam(':notification_text', $notificationText);

        if ($stmt->execute()) {
            // Redirect to view_details.php with success message
            header("Location: view_details.php?id=$child_id&success=1");
            exit();
        } else {
            $successMessage = "Failed to send notification.";
        }
    } else {
        $successMessage = "Failed to add location.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child and Guardian Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">TrackChild</a>
        </div>
    </nav>
    <div class="container">
        <h1 class="mb-4">Child Details</h1>
        <?php if (!empty($successMessage)) { ?>
            <div class="alert alert-success">
                <?php echo $successMessage; ?>
            </div>
        <?php } ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h2 class="card-title">Child Details</h2>
                <p><strong>Child Name:</strong> <?php echo htmlspecialchars($child['child_name']); ?></p>
                <p><strong>Age:</strong> <?php echo htmlspecialchars($child['age']); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($child['gender']); ?></p>
                <p><strong>Last Seen Location:</strong> <?php echo htmlspecialchars($child['last_seen_location']); ?></p>
                <p><strong>Last Seen Date:</strong> <?php echo htmlspecialchars($child['last_seen_date']); ?></p>
            </div>
        </div>
        <?php if (isset($child['status']) && $child['status'] === 'found') { ?>
            <div class="alert alert-info text-center fw-bold">This child has been found and the case is resolved.</div>
        <?php } else { ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">Enter Location of Found Child</h3>
                        <form action="view_details.php?id=<?php echo $child['child_id']; ?>" method="POST">
                            <div class="mb-3">
                                <label for="location_name" class="form-label">Location Name:</label>
                                <input type="text" id="location_name" name="location_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address:</label>
                                <input type="text" id="address" name="address" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label">City:</label>
                                <input type="text" id="city" name="city" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="state" class="form-label">State:</label>
                                <input type="text" id="state" name="state" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="country" class="form-label">Country:</label>
                                <input type="text" id="country" name="country" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="latitude" class="form-label">Latitude:</label>
                                <input type="number" id="latitude" name="latitude" step="0.0001" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="longitude" class="form-label">Longitude:</label>
                                <input type="number" id="longitude" name="longitude" step="0.0001" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Location</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">Send Notification</h3>
                        <form action="send_notification.php" method="POST">
                            <div class="mb-3">
                                <label for="notification_text" class="form-label">Notification Text:</label>
                                <textarea id="notification_text" name="notification_text" class="form-control" required></textarea>
                            </div>
                            <input type="hidden" name="child_id" value="<?php echo $child['child_id']; ?>">
                            <button type="submit" class="btn btn-success">Send Notification</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

