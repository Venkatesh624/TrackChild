<?php
// File: feedback.php
include('../includes/db.php');

// Fetch child ID from query string
$child_id = isset($_GET['child_id']) ? intval($_GET['child_id']) : null;

if (!$child_id) {
    die("Error: Invalid child ID.");
}

// Fetch guardian ID dynamically
$query = "SELECT guardian_id FROM guardians WHERE child_id = :child_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':child_id', $child_id, PDO::PARAM_INT);
$stmt->execute();
$guardian = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$guardian) {
    die("Error: No guardian found for the specified child.");
}

$guardian_id = $guardian['guardian_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback_text = $_POST['feedback_text'];
    $rating = $_POST['rating'];

    // Insert feedback into database
    $query = "INSERT INTO user_feedback (child_id, guardian_id, feedback_text, rating) 
              VALUES (:child_id, :guardian_id, :feedback_text, :rating)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':child_id', $child_id, PDO::PARAM_INT);
    $stmt->bindParam(':guardian_id', $guardian_id, PDO::PARAM_INT);
    $stmt->bindParam(':feedback_text', $feedback_text, PDO::PARAM_STR);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: feedback.php?child_id=$child_id&success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">TrackChild</a>
        </div>
    </nav>
    <div class="container">
        <h1 class="mb-4">Give Feedback</h1>
        <?php if (isset($_GET['success']) && $_GET['success'] == 1) { ?>
            <div class="alert alert-success">Feedback submitted successfully!</div>
        <?php } ?>
        <form action="feedback.php?child_id=<?php echo $child_id; ?>" method="POST" class="card p-4 shadow-sm bg-white">
            <input type="hidden" name="guardian_id" value="<?php echo htmlspecialchars($guardian_id); ?>">
            <div class="mb-3">
                <label for="feedback_text" class="form-label">Feedback:</label>
                <textarea id="feedback_text" name="feedback_text" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label for="rating" class="form-label">Rating (1-5):</label>
                <input type="number" id="rating" name="rating" min="1" max="5" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Feedback</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
