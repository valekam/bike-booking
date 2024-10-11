<?php
session_start();
include 'admin/config.php'; // Include your database configuration

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<p>You need to be logged in to view your bookings.</p>";
    exit;
}

$username = $_SESSION['username'];
$bookings = [];

// Fetch user's bookings from the database
$booking_query = mysqli_query($conn, "
    SELECT bookings.*, bikes.bike_image, bikes.rider, bikes.bike_plate 
    FROM bookings 
    JOIN bikes ON bookings.bike_plate = bikes.bike_plate 
    WHERE bookings.username = '$username'
");

if ($booking_query) {
    while ($row = mysqli_fetch_assoc($booking_query)) {
        $bookings[] = $row;
    }
} else {
    echo "<p>Error fetching bookings: " . mysqli_error($conn) . "</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booked Bike</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS -->
    <style>     
        table {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f2f2f2;
            color: #333;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .bike-image {
            max-width: 80px; /* Smaller image size */
            height: auto;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header class="logo">
        <img src="bike/kawa.png" alt="Bike Logo" class="bike-logo">
        <div class="logo-title">SECURE <span>BIKE BOOKING SYSTEM</span></div>
        <nav class="nav-links">
            <a href="search.php">Back</a>   
        </nav>
    </header>
    <h1>Booked Bike</h1>

    <?php if (!empty($bookings)): ?>
        <table class="booked">
            <thead>
                <tr>
                    <th>Bike Image</th>
                    <th>Bike Plate</th>
                    <th>Rider</th>
                    <th>Departure</th>
                    <th>Destination</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><img src="admin/uploaded_img/<?php echo htmlspecialchars($booking['bike_image']); ?>" alt="<?php echo htmlspecialchars($booking['bike_plate']); ?>" class="bike-image"></td>
                        <td><?php echo htmlspecialchars($booking['bike_plate']); ?></td>
                        <td><?php echo htmlspecialchars($booking['rider']); ?></td>
                        <td><?php echo htmlspecialchars($booking['departure']); ?></td>
                        <td><?php echo htmlspecialchars($booking['destination']); ?></td>
                        <td>Ksh <?php echo htmlspecialchars($booking['price']); ?></td>
                        <td><?php echo htmlspecialchars($booking['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No bookings found for your account.</p>
    <?php endif; ?>
</body>
</html>
