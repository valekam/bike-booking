<?php
session_start();
include '../admin/config.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<p>You need to be logged in to perform this action.</p>";
    exit;
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the bike plate from the form
    $bike_plate = $_POST['bike_plate'] ?? null;

    if ($bike_plate) {
        // Prepare the statement to update the bike status
        $stmt = $conn->prepare("UPDATE bikes SET status = 'available' WHERE plate = ?");
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }

        // Bind parameters and execute the statement
        $stmt->bind_param("s", $bike_plate);
        if ($stmt->execute()) {
            // Successfully updated
            $stmt->close();
            header("Location: your_bookings_page.php?message=Bike returned successfully."); // Adjust as needed
            exit;
        } else {
            // Error executing the statement
            echo "Error updating bike status: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "<p>No bike plate specified.</p>";
    }
} else {
    // Not a POST request
    echo "<p>Invalid request.</p>";
}
?>

<!-- Include this form in your main page where the bookings are displayed -->
<form method="POST" action="return_bike.php">
    <input type="hidden" name="bike_plate" value="<?php echo htmlspecialchars($booking['bike_plate']); ?>">
    <button type="submit" name="return">Return Bike</button>
</form>
