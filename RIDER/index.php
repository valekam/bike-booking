<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db = "bikebooking";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch rider's bike details
if (!isset($_SESSION['rider_no'])) {
    header("Location: login.php");
    exit();
}

$rider_no = $_SESSION['rider_no'];
$query = "SELECT b.*, r.rider_name FROM bikes b JOIN riders r ON b.rider_no = r.rider_no WHERE r.rider_no='$rider_no'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bike Booking System</title>
    <link rel="icon" href="bike.jpg" type="image">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="logo">
        <img src="avarter.png" alt="Bike Logo" class="bike-logo">
        <div class="logo-title">SECURE <span>BIKE BOOKING SYSTEM</span></div>
        <nav class="nav-links"> 
            <a href="logout.php">LOG OUT</a> 
        </nav>
    </header>
    <section class="manage">
        <?php
        // Check if there is a valid result and fetch the rider's name
        if ($result && mysqli_num_rows($result) > 0) {
            $rider = mysqli_fetch_assoc($result);
            echo "<h1>Welcome Rider, " . htmlspecialchars($rider['rider_name']) . "!</h1>";
            echo "<h2>Here Is Your Bike</h2>";
        } else {
            echo "<h1>No bikes found for this rider.</h1>";
            exit();
        }
        ?>
        
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
                // Reset result pointer to fetch bike details again
                mysqli_data_seek($result, 0);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><img src="uploaded_img/<?php echo htmlspecialchars($row['bike_image']); ?>" height="100" alt=""></td>
                    <td><?php echo htmlspecialchars($row['bike_plate']); ?></td>
                    <td><?php echo htmlspecialchars($row['destination']); ?></td>
                    <td><?php echo htmlspecialchars($row['departure']); ?></td>
                    <td><?php echo htmlspecialchars($row['price']); ?></td>
                </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='5' class='empty'>No bikes found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>
</body>
</html>
