<?php
session_start();
include 'config.php'; // Include your database configuration

// Assume the rider is already logged in, and we get their username from the session
$rider_username = $_SESSION['rider_username'] ?? null;

if (!$rider_username) {
    header("Location: login.php"); // Redirect if not logged in
    exit;
}

// Fetch rider bikes and routes
$query = "SELECT bikes.*, routes.destination, routes.departure, routes.price 
          FROM bikes 
          LEFT JOIN routes ON bikes.bike_plate = routes.bike_plate 
          WHERE rider = '$rider_username'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header class="logo">
        <div class="logo-title">SECURE <span>BIKE BOOKING SYSTEM</span></div>
        <nav class="nav-links">
            <a href="index.php">BACK</a>
        </nav>
    </header>
    
    <div class="form-container">
        <h1>Your Bikes and Routes</h1>
        <table align="center">
            <thead>
                <tr>
                    <th>BIKE IMAGE</th>
                    <th>BIKE PLATE</th>
                    <th>DESTINATION</th>
                    <th>DEPARTURE</th>
                    <th>PRICE</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td><img src='uploaded_img/{$row['bike_image']}' height='100' alt='Bike Image'></td>
                                <td>{$row['bike_plate']}</td>
                                <td>{$row['destination']}</td>
                                <td>{$row['departure']}</td>
                                <td>{$row['price']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='empty'>No bikes or routes found for this rider.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
