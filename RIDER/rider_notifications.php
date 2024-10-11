<?php
session_start();
include '../admin/config.php';

if (!isset($_SESSION['username'])) {
    echo "<p>You need to be logged in to view this page.</p>";
    exit;
}

$rider_username = $_SESSION['username'];

// Fetch notifications for the rider, specifically for pending bookings
$notification_query = $conn->prepare("
    SELECT n.* 
    FROM notifications n
    JOIN bookings b ON n.id = b.id 
    WHERE b.username = ? AND b.status = 'pending'
");

if (!$notification_query) {
    die("Error preparing statement: " . $conn->error);
}

$notification_query->bind_param("s", $rider_username);
$notification_query->execute();
$result = $notification_query->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
$notification_query->close();

// Fetch pending bookings
$bookings_query = mysqli_query($conn, "SELECT * FROM bookings WHERE status = 'pending'");
$bookings = mysqli_fetch_all($bookings_query, MYSQLI_ASSOC);

// Fetch approved bookings
$approved_query = $conn->prepare("SELECT * FROM bookings WHERE status = 'approved' AND username = ?");
$approved_query->bind_param("s", $rider_username);
$approved_query->execute();
$approved_result = $approved_query->get_result();
$approved_bookings = $approved_result->fetch_all(MYSQLI_ASSOC);
$approved_query->close();

// Count notifications
$notification_count = count($notifications);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bike Bookings</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #28a745; /* Green header */
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1; /* Highlight row on hover */
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
            margin-right: 5px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <header class="logo">
        <img src="avarter.png" alt="Bike Logo" class="bike-logo">
        <div class="logo-title">SECURE <span>BIKE BOOKING SYSTEM</span></div>
        <nav class="nav-links">  
            <a href="index.php">Back</a> 
        </nav>
    </header>

    <h1>Pending Bookings</h1>
    <?php if ($bookings): ?>
        <table>
            <tr>
                <th>Bike Plate</th>
                <th>User Name</th>
                <th>Departure</th>
                <th>Destination</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['bike_plate']); ?></td>
                    <td><?php echo htmlspecialchars($booking['username']); ?></td>
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

    <h1>Approved Bookings</h1>
    <?php if ($approved_bookings): ?>
        <table>
            <tr>
                <th>Bike Plate</th>
                <th>User Name</th>
                <th>Departure</th>
                <th>Destination</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($approved_bookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['bike_plate']); ?></td>
                    <td><?php echo htmlspecialchars($booking['username']); ?></td>
                    <td><?php echo htmlspecialchars($booking['departure']); ?></td>
                    <td><?php echo htmlspecialchars($booking['destination']); ?></td>
                    <td>Ksh <?php echo htmlspecialchars($booking['price']); ?></td>
                    <td>
                        <form method="POST" action="return_bike.php">
                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                            <button type="submit" name="return">Return Bike</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No approved bookings.</p>
    <?php endif; ?>

    <h1>Notifications</h1>
    <?php if (!empty($notifications)): ?>
        <ul class="notification-list">
            <?php foreach ($notifications as $notification): ?>
                <li>
                    <p><?php echo htmlspecialchars($notification['message']); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No notifications at this time.</p>
    <?php endif; ?>

</body>
</html>
