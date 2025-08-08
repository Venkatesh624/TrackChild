<?php
// File: submit_child.php
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $name = $_POST['name'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $last_seen_location = $_POST['last_seen_location'];
        $last_seen_date = $_POST['last_seen_date'];
        $guardian_name = $_POST['guardian_name'];
        $relationship = $_POST['relationship'];
        $contact_info = $_POST['contact_info'];

        // Insert child record
        $stmt = $conn->prepare("INSERT INTO children (name, age, gender, last_seen_location, last_seen_date) VALUES (:name, :age, :gender, :location, :date)");
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':age', $age, PDO::PARAM_INT);
        $stmt->bindValue(':gender', $gender);
        $stmt->bindValue(':location', $last_seen_location);
        $stmt->bindValue(':date', $last_seen_date);
        $stmt->execute();

        // Get the last inserted child_id
        $child_id = $conn->lastInsertId();

        // Redirect with a success message
        header("Location: existing_report.php?success=1&child_id=$child_id");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Lost Child Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Lost Child Project</a>
        </div>
    </nav>
    <div class="container">
        <h2 class="mb-4">Submit Lost Child Report</h2>
        <form method="POST" class="card p-4 shadow-sm bg-white">
            <div class="mb-3">
                <label for="childName" class="form-label">Child's Name</label>
                <input type="text" id="childName" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="childAge" class="form-label">Age</label>
                <input type="number" id="childAge" name="age" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="childGender" class="form-label">Gender</label>
                <select id="childGender" name="gender" class="form-select" required>
                    <option value="">Select</option>
                    <option>Male</option>
                    <option>Female</option>
                    <option>Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="lastSeenLocation" class="form-label">Last Seen Location</label>
                <input type="text" id="lastSeenLocation" name="last_seen_location" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="lastSeenDate" class="form-label">Last Seen Date</label>
                <input type="date" id="lastSeenDate" name="last_seen_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="guardianName" class="form-label">Guardian Name</label>
                <input type="text" id="guardianName" name="guardian_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="relationship" class="form-label">Relationship</label>
                <input type="text" id="relationship" name="relationship" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="contactInfo" class="form-label">Contact Info</label>
                <input type="text" id="contactInfo" name="contact_info" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Report</button>
        </form>
    </div>
</body>
</html>
