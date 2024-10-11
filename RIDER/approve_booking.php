<?php
session_start();
include '../admin/config.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<p>You need to be logged in to perform this action.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $_POST['booking_id'] ?? null;
    $action = isset($_POST['approve']) ? 'approve' : 'reject';

    if ($booking_id) {
        // Prepare the statement to update the booking status
        $status = $action === 'approve' ? 'approved' : 'rejected';
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }

        // Bind parameters and execute the statement
        $stmt->bind_param("si", $status, $booking_id);
        if (!$stmt->execute()) {
            echo "Error executing statement: " . $stmt->error;
        } else {
            // Update notifications as well
            // Assuming you have a notification update logic here
        }
        $stmt->close();

        // Redirect or inform the user
        header("Location: index.php?message=Booking updated successfully."); // Adjust as needed
        exit;
    } else {
        echo "<p>No booking ID specified.</p>";
    }
} else {
    echo "<p>Invalid request.</p>";
}
?>
