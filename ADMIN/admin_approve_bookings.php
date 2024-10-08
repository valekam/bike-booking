<?php
session_start();
include 'config.php'; // Include your database configuration

// Check if the user is an admin
//if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    //exit;
//}

// Fetch all pending bookings
$bookings_query = mysqli_query($conn, "SELECT * FROM bookings WHERE status = 'pending'");
$bookings = mysqli_fetch_all($bookings_query, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Approval - Bike Bookings</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS -->
</head>
<body>
    <h1>Pending Bookings</h1>
    <?php if ($bookings): ?>
        <table>
            <tr>
                <th>Bike Plate</th>
                <th>User Name</th>
                <th>User Email</th>
                <th>User Phone</th>
                <th>Departure</th>
                <th>Destination</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['bike_plate']); ?></td>
                    <td><?php echo htmlspecialchars($booking['username']); ?></td>
                    <td><?php echo htmlspecialchars($booking['user_email']); ?></td>
                    <td><?php echo htmlspecialchars($booking['user_phone']); ?></td>
                    <td><?php echo htmlspecialchars($booking['departure']); ?></td>
                    <td><?php echo htmlspecialchars($booking['destination']); ?></td>
                    <td>Ksh <?php echo htmlspecialchars($booking['price']); ?></td>
                    <td>
                        <form method="POST" action="approve_booking.php">
                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                            <button type="submit" name="approve">Approve</button>
                            <button type="submit" name="reject">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No pending bookings.</p>
    <?php endif; ?>
</body>
</html>
