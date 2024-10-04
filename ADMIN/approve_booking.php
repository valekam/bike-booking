<?php
//session_start();
include 'config.php'; // Include your database configuration

// Check if the user is an admin
//if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    //echo "<p>You do not have permission to perform this action.</p>";
   // exit;
//}

// Check if booking ID is set
if (isset($_POST['booking_id'])) {
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);

    if (isset($_POST['approve'])) {
        // Approve the booking
        $update_query = "UPDATE bookings SET status = 'approved' WHERE id = '$booking_id'";
        mysqli_query($conn, $update_query);

        // Here, move the bike to the booked table if necessary
        // e.g., INSERT INTO booked_bikes ...
        
        echo "<p>Booking approved successfully.</p>";
    } elseif (isset($_POST['reject'])) {
        // Reject the booking
        $update_query = "UPDATE bookings SET status = 'rejected' WHERE id = '$booking_id'";
        mysqli_query($conn, $update_query);

        echo "<p>Booking rejected successfully.</p>";
    } else {
        echo "<p>Invalid action.</p>";
    }
} else {
    echo "<p>No booking ID provided.</p>";
}
?>
<a href="admin_approve_bookings.php">Back to bookings</a>
